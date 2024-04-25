<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ZohoCRMMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Closure|JsonResponse|Response
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            return response()->json(['error' => 'Failed to get access token'], 500);
        }

        $request->headers->set('Authorization', 'Zoho-oauthtoken ' . $accessToken);

        return $next($request);
    }

    /**
     * Get the grant token from Zoho API.
     *
     * @return string|null
     */
    public function getGrantToken(): Response|null|string
    {
        $response = Http::asForm()->post(env('ZOHO_ACCOUNTS_URL') . '/oauth/v2/token', [
            'client_id' => env('ZOHO_CLIENT_ID'),
            'client_secret' => env('ZOHO_CLIENT_SECRET'),
            'grant_type' => 'authorization_code',
            'redirect_uri' => env('ZOHO_REDIRECT_URI'),
            'code' => env('ZOHO_TOKEN'),
        ]);

        $data = $response->json();
        if ($response->successful() && isset($data['access_token']) && isset($data['refresh_token'])) {
            $accessToken = $data['access_token'];
            $refreshToken = $data['refresh_token'];
            $expiresIn = $data['expires_in'];

            /* Instead of using Cache, a real project should use token storage in database tables.
             * Here Cache is used to quickly delete stored tokens in order to ensure the purity of the experiment
             * */
            Cache::put('zoho_access_token', $accessToken, now()->addSeconds($expiresIn));
            Cache::put('zoho_refresh_token', $refreshToken);

            return $accessToken;
        }

        return null;
    }

    /**
     * Retrieve the saved access token or refresh it if necessary.
     *
     * @return string|null
     */
    public function getAccessToken(): string|null
    {
        $accessToken = Cache::get('zoho_access_token');

        if (!$accessToken) {
            if ($accessToken = $this->refreshAccessToken()) {
                return $accessToken;
            }
            if ($grantToken = $this->getGrantToken()) {
                return $grantToken;
            }
        }

        return $accessToken;
    }

    /**
     * Refresh the access token using the refresh token.
     *
     * @return Response|null
     */
    private function refreshAccessToken(): string|null
    {
        $refreshToken = Cache::get('zoho_refresh_token');

        if ($refreshToken) {
            $response = Http::asForm()->post(env('ZOHO_ACCOUNTS_URL') . '/oauth/v2/token', [
                'client_id' => env('ZOHO_CLIENT_ID'),
                'client_secret' => env('ZOHO_CLIENT_SECRET'),
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ]);

            $data = $response->json();
            if ($response->successful() && isset($data['access_token'])) {
                $accessToken = $data['access_token'];
                $expiresIn = $data['expires_in'];

                Cache::put('zoho_access_token', $accessToken, now()->addSeconds($expiresIn));

                return $accessToken;
            }
        }

        return null;
    }
}
