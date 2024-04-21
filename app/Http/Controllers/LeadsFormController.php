<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeadFormRequest;
use Asciisd\Zoho\ZohoManager;

class LeadsFormController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(LeadFormRequest  $request)
    {
        return response()->json(['message' => 'Data verified successfully'], 201);
    }
}
