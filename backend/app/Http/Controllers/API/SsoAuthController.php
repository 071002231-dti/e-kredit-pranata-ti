<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\ShibbolethService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class SsoAuthController extends Controller
{
    protected ShibbolethService $shibbolethService;

    public function __construct(ShibbolethService $shibbolethService)
    {
        $this->shibbolethService = $shibbolethService;
    }

    /**
     * Initiate SSO login flow
     * Returns redirect URL to Shibboleth endpoint
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function initiateLogin(Request $request): JsonResponse
    {
        try {
            // Get return URL from query parameter or default to dashboard
            $returnUrl = $request->query('return_url', '/dashboard');

            // Shibboleth login endpoint (protected by Nginx)
            // Nginx will intercept and redirect to IdP
            $ssoLoginUrl = url('/api/auth/sso/login');

            // Append return_url as query parameter
            $ssoLoginUrl .= '?return_url=' . urlencode($returnUrl);

            return response()->json([
                'message' => 'Redirect to SSO login',
                'redirect_url' => $ssoLoginUrl,
            ]);
        } catch (\Exception $e) {
            Log::error('SSO: Initiate login failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Failed to initiate SSO login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle SSO login callback
     * This endpoint is protected by Shibboleth via Nginx
     * SAML attributes are injected as HTTP headers by Nginx
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        try {
            // Extract SAML attributes from Shibboleth headers
            $attributes = $this->shibbolethService->extractAttributes($request);

            if (!$attributes) {
                Log::warning('SSO: Login failed - missing SAML attributes', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);

                return response()->json([
                    'message' => 'SSO authentication failed: Missing required attributes',
                    'error_code' => 'MISSING_SAML_ATTRIBUTES'
                ], 401);
            }

            // Validate attributes format
            if (!$this->shibbolethService->validateAttributes($attributes)) {
                Log::warning('SSO: Login failed - invalid SAML attributes', [
                    'attributes' => $attributes,
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'message' => 'SSO authentication failed: Invalid attribute format',
                    'error_code' => 'INVALID_SAML_ATTRIBUTES'
                ], 401);
            }

            // Find or create user from SAML attributes
            $user = $this->shibbolethService->findOrCreateUser($attributes);

            if (!$user) {
                Log::error('SSO: User provisioning failed', [
                    'attributes' => $attributes,
                    'ip' => $request->ip()
                ]);

                return response()->json([
                    'message' => 'Failed to provision user account',
                    'error_code' => 'USER_PROVISIONING_FAILED'
                ], 500);
            }

            // Generate Sanctum API token (7-day expiry)
            $tokenName = 'sso-token-' . now()->format('Y-m-d-H-i-s');
            $token = $user->createToken($tokenName, ['*'], now()->addDays(7))->plainTextToken;

            Log::info('SSO: Login successful', [
                'user_id' => $user->id,
                'nip_sso' => $user->nip_sso,
                'email' => $user->email,
                'auth_method' => $user->auth_method,
                'ip' => $request->ip()
            ]);

            // Return user data and token
            return response()->json([
                'message' => 'SSO login successful',
                'user' => [
                    'id' => $user->id,
                    'nip' => $user->nip,
                    'nip_sso' => $user->nip_sso,
                    'sso_uid' => $user->sso_uid,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'position' => $user->position,
                    'unit_kerja' => $user->unit_kerja,
                    'jenjang_jabatan' => $user->jenjang_jabatan,
                    'affiliation' => $user->affiliation,
                    'auth_method' => $user->auth_method,
                    'sso_last_login' => $user->sso_last_login?->toIso8601String(),
                    'created_at' => $user->created_at->toIso8601String(),
                    'updated_at' => $user->updated_at->toIso8601String(),
                ],
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_at' => now()->addDays(7)->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            Log::error('SSO: Login exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'message' => 'SSO login failed',
                'error' => Config::get('app.debug') ? $e->getMessage() : 'Internal server error',
                'error_code' => 'SSO_LOGIN_EXCEPTION'
            ], 500);
        }
    }

    /**
     * Handle SSO logout
     * Revoke current Sanctum token and return Shibboleth logout URL
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'message' => 'Not authenticated'
                ], 401);
            }

            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            Log::info('SSO: Logout successful', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            // Shibboleth logout URL
            $shibbolethLogoutUrl = url('/Shibboleth.sso/Logout');

            // Return logout URL for client-side redirect
            return response()->json([
                'message' => 'Logged out successfully',
                'shibboleth_logout_url' => $shibbolethLogoutUrl,
                'redirect_url' => url('/login'), // Frontend login page
            ]);
        } catch (\Exception $e) {
            Log::error('SSO: Logout exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug endpoint to display Shibboleth headers
     * Only available in development mode
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function debug(Request $request): JsonResponse
    {
        // Only allow in non-production environments
        if (Config::get('app.env') === 'production') {
            return response()->json([
                'message' => 'Debug endpoint not available in production'
            ], 403);
        }

        // Extract all Shibboleth-related headers
        $headers = [];
        $shibbolethPrefixes = ['uid', 'mail', 'displayName', 'eduPerson', 'shib'];

        foreach ($request->headers->all() as $key => $value) {
            $headerKey = strtolower($key);
            foreach ($shibbolethPrefixes as $prefix) {
                if (str_contains($headerKey, strtolower($prefix))) {
                    $headers[$key] = $value;
                    break;
                }
            }
        }

        // Also check server variables
        $serverVars = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_') || str_starts_with($key, 'SHIB_')) {
                $serverVars[$key] = $value;
            }
        }

        // Extract attributes using service
        $attributes = $this->shibbolethService->extractAttributes($request);

        return response()->json([
            'message' => 'Shibboleth Debug Information',
            'headers' => $headers,
            'server_vars' => $serverVars,
            'extracted_attributes' => $attributes,
            'is_authenticated' => !empty($attributes),
            'validation' => $attributes ? $this->shibbolethService->validateAttributes($attributes) : false,
        ]);
    }
}
