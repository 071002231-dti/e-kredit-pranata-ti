<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Config;

class MockShibbolethHeaders
{
    /**
     * Handle an incoming request.
     * Simulates Shibboleth headers for local development testing
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only allow in local/debug environments
        if (!Config::get('app.debug') || Config::get('app.env') === 'production') {
            return $next($request);
        }

        // Check if mock SSO is requested via query parameter
        if ($request->query('mock_sso') !== '1') {
            return $next($request);
        }

        // Determine which test user to simulate based on query parameter
        $testUser = $request->query('test_user', 'faculty');

        $mockUsers = [
            'faculty' => [
                'uid' => '123456789',
                'mail' => '123456789@uii.ac.id',
                'displayName' => 'Dr. Ahmad Fauzi (Test Faculty)',
                'eduPersonAffiliation' => 'faculty',
                'eduPersonOrgUnitDN' => 'ou=Fakultas Teknologi Industri,dc=uii,dc=ac,dc=id',
            ],
            'staff' => [
                'uid' => '987654321',
                'mail' => '987654321@uii.ac.id',
                'displayName' => 'Budi Santoso (Test Staff)',
                'eduPersonAffiliation' => 'staff',
                'eduPersonOrgUnitDN' => 'ou=Direktorat TI,dc=uii,dc=ac,dc=id',
            ],
            'student' => [
                'uid' => '111222333',
                'mail' => '111222333@students.uii.ac.id',
                'displayName' => 'Citra Dewi (Test Student)',
                'eduPersonAffiliation' => 'student',
                'eduPersonOrgUnitDN' => 'ou=Fakultas Teknologi Industri,dc=uii,dc=ac,dc=id',
            ],
            'existing' => [
                // This simulates an existing user in the system
                'uid' => '196701011992031001', // Should match existing test user
                'mail' => 'admin.pti@uii.ac.id',
                'displayName' => 'Admin PTI (Existing User)',
                'eduPersonAffiliation' => 'faculty',
                'eduPersonOrgUnitDN' => 'ou=Fakultas Teknologi Industri,dc=uii,dc=ac,dc=id',
            ],
        ];

        // Get mock user data
        $mockData = $mockUsers[$testUser] ?? $mockUsers['faculty'];

        // Inject headers (both styles for compatibility)
        foreach ($mockData as $key => $value) {
            $request->headers->set($key, $value);
            $_SERVER['HTTP_' . strtoupper($key)] = $value;
        }

        // Log mock SSO usage
        \Log::info('Mock SSO: Using test user', [
            'test_user' => $testUser,
            'uid' => $mockData['uid'],
            'mail' => $mockData['mail'],
        ]);

        return $next($request);
    }
}
