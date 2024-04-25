<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class LeadsFormController extends Controller
{
    private $accountID = null;
    private $dealID = null;

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        // Validate incoming data. It will be necessary to move this into a separate class, inherited from Request
        $validatedData = $request->validate([
            'dealName' => ['required', 'string', 'regex:/^[A-Za-z0-9\s\'\-]+$/', 'max:255'],
            'dealStage' => ['required', 'string', Rule::in(['Qualification', 'Needs Analysis', 'Value Proposition', 'Identify Decision Makers', 'Proposal/Price Quote', 'Negotiation/Review', 'Closed Won', 'Closed Lost', 'Closed Lost to Competition'])],
            'dealClosingDate' => 'required|date|date_format:Y-m-d',
            'accountName' => ['required', 'string', 'regex:/^[A-Za-z0-9\s\'\-]+$/', 'max:255'],
            'accountWebsite' => ['required', 'regex:/^(http:\/\/www\.|https:\/\/www\.|ftp:\/\/www\.|www\.|http:\/\/|https:\/\/|ftp:\/\/){1}[^\\x00-\\x19\\x22-\\x27\\x2A-\\x2C\\x2E-\\x2F\\x3A-\\x3F\\x5B-\\x5E\\x60\\x7B\\x7D-\\x7F]+(\.[^\\x00-\\x19\\x22\\x24-\\x2C\\x2E-\\x2F\\x3C\\x3E\\x40\\x5B-\\x5E\\x60\\x7B\\x7D-\\x7F]+)+([\/\?].*)*$/'],
            'accountPhone' => ['required', 'string', 'regex:/^([+]?)(?![.-])(?>(?>[.-]?[ ]?[\da-zA-Z]+)+|([ ]?((?![.-])(?>[ .-]?[\da-zA-Z]+)+)(?![.])([ -]?[\da-zA-Z]+)?)+)+(?>(?>([,]+)?[;]?[\da-zA-Z]+)+(([#][\da-zA-Z]+)+)?)?[#;]?$/', 'max:30'],
        ]);

        // Creating account entry - this part of the code should be extracted to a separate Service
        $createAccountPostData = [
            'data' => [
                [
                    'Account_Name' => $validatedData['accountName'],
                    'Website' => $validatedData['accountWebsite'],
                    'Phone' => $validatedData['accountPhone']
                ]
            ]
        ];

        $response = Http::withHeaders([
            'Authorization' => $request->headers->get('Authorization'),
            'Content-Type' => 'application/json'
        ])->post(env('ZOHO_CRM_API_URL') . '/Accounts', $createAccountPostData);

        if ($response->successful()) {
            $responseData = $response->json();
            $this->accountID = $responseData['data'][0]['details']['id'];
        } else {
            return response()->json(['success' => false, 'error' => $response->json()], $response->status());
        }

        // Creating deal entry - this part of the code should be extracted to a separate Service
        $createDealPostData = [
            'data' => [
                [
                    'Deal_Name' => $validatedData['dealName'],
                    'Account_ID' => $this->accountID,
                    'Account_Name' => $validatedData['accountName'],
                    'Stage' => $validatedData['dealStage'],
                    'Closing_Date' => $validatedData['dealClosingDate']
                ]
            ]
        ];

        $response = Http::withHeaders([
            'Authorization' => $request->headers->get('Authorization'),
            'Content-Type' => 'application/json'
        ])->post(env('ZOHO_CRM_API_URL') . '/Deals', $createDealPostData);

        if ($response->successful()) {
            $responseData = $response->json();
            $this->dealID = $responseData['data'][0]['details']['id'];
        } else {
            return response()->json(['success' => false, 'error' => $response->json()], $response->status());
        }

        // Fetching created entries - this part of the code might also benefit from extraction to a separate Service
        if ($this->accountID && $this->dealID) {
            $response = Http::withHeaders([
                'Authorization' => $request->headers->get('Authorization'),
                'Content-Type' => 'application/json'
            ])->get(env('ZOHO_CRM_API_URL') . "/Accounts/{$this->accountID}/Deals/{$this->dealID}");

            if ($response->successful()) {
                return response()->json(['success' => true, 'message' => 'You have successfully created associated Account and Deal entries! Add another entry if necessary.']);
            } else {
                return response()->json(['success' => false, 'error' => $response->json()], $response->status());
            }
        }

        return response()->json(['message' => 'Data verified successfully'], 201);
    }
}
