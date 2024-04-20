<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Asciisd\Zoho\ZohoManager;

class ZohoCRMController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        
        $accounts = ZohoManager::useModule('Accounts');
        $deals = ZohoManager::useModule('Deals');
        return response()->json(['message' => true], 200);
    }
}
