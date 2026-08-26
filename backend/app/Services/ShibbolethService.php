<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ShibbolethService
{
    /**
     * Extract SAML attributes from Shibboleth headers
     *
     * @param Request $request
     * @return array|null Array of attributes or null if invalid
     */
    public function extractAttributes(Request $request): ?array
    {
        // Extract headers (both HTTP_ prefixed and direct)
        $uid = $request->header('uid') ?? $request->server('HTTP_UID');
        $mail = $request->header('mail') ?? $request->server('HTTP_MAIL');
        $displayName = $request->header('displayName') ?? $request->server('HTTP_DISPLAYNAME');
        $affiliation = $request->header('eduPersonAffiliation') ?? $request->server('HTTP_EDUPERSONAFFILIATION');
        $orgUnitDN = $request->header('eduPersonOrgUnitDN') ?? $request->server('HTTP_EDUPERSONORGUNITDN');

        // Validate required attributes
        if (empty($uid) || empty($mail)) {
            Log::warning('SSO: Missing required SAML attributes', [
                'uid' => $uid,
                'mail' => $mail,
                'ip' => $request->ip()
            ]);
            return null;
        }

        $attributes = [
            'uid' => trim($uid),
            'mail' => trim($mail),
            'displayName' => $displayName ? trim($displayName) : null,
            'affiliation' => $affiliation ? trim($affiliation) : null,
            'orgUnitDN' => $orgUnitDN ? trim($orgUnitDN) : null,
        ];

        Log::info('SSO: Extracted SAML attributes', [
            'uid' => $attributes['uid'],
            'mail' => $attributes['mail'],
            'affiliation' => $attributes['affiliation'],
            'ip' => $request->ip()
        ]);

        return $attributes;
    }

    /**
     * Find existing user or create new user from SAML attributes
     *
     * @param array $attributes SAML attributes
     * @return User
     * @throws \Exception
     */
    public function findOrCreateUser(array $attributes): User
    {
        $nipSso = $this->extractNipFromUid($attributes['uid']);
        $ssoUid = $attributes['uid'];

        // Try to find user by sso_uid first (most reliable)
        $user = User::where('sso_uid', $ssoUid)->first();

        // If not found, try by nip_sso
        if (!$user && $nipSso) {
            $user = User::where('nip_sso', $nipSso)->first();
        }

        // If not found, try by email
        if (!$user) {
            $user = User::where('email', $attributes['mail'])->first();
        }

        if ($user) {
            // Update existing user with latest SSO data
            $this->updateUserFromSso($user, $attributes, $nipSso, $ssoUid);

            Log::info('SSO: Updated existing user', [
                'user_id' => $user->id,
                'nip_sso' => $nipSso,
                'auth_method' => $user->auth_method
            ]);
        } else {
            // Create new user (auto-provisioning)
            $user = $this->createUserFromSso($attributes, $nipSso, $ssoUid);

            Log::info('SSO: Created new user', [
                'user_id' => $user->id,
                'nip_sso' => $nipSso,
                'email' => $user->email
            ]);
        }

        return $user;
    }

    /**
     * Update existing user with SSO data
     *
     * @param User $user
     * @param array $attributes
     * @param string|null $nipSso
     * @param string $ssoUid
     * @return void
     */
    protected function updateUserFromSso(User $user, array $attributes, ?string $nipSso, string $ssoUid): void
    {
        $updates = [
            'sso_uid' => $ssoUid,
            'sso_last_login' => now(),
        ];

        // Update nip_sso if not set
        if (!$user->nip_sso && $nipSso) {
            $updates['nip_sso'] = $nipSso;
        }

        // Update email if changed
        if ($user->email !== $attributes['mail']) {
            $updates['email'] = $attributes['mail'];
        }

        // Update name if provided and different
        if ($attributes['displayName'] && $user->name !== $attributes['displayName']) {
            $updates['name'] = $attributes['displayName'];
        }

        // Update affiliation if provided
        if ($attributes['affiliation']) {
            $updates['affiliation'] = $attributes['affiliation'];
        }

        // Update unit_kerja from orgUnitDN if not set
        if (!$user->unit_kerja && $attributes['orgUnitDN']) {
            $orgUnit = $this->parseOrgUnit($attributes['orgUnitDN']);
            if ($orgUnit) {
                $updates['unit_kerja'] = $orgUnit;
            }
        }

        // Update auth_method
        if ($user->auth_method === 'local' && $user->password) {
            $updates['auth_method'] = 'hybrid'; // User has both local and SSO
        } else {
            $updates['auth_method'] = 'sso';
        }

        $user->update($updates);
    }

    /**
     * Create new user from SSO attributes
     *
     * @param array $attributes
     * @param string|null $nipSso
     * @param string $ssoUid
     * @return User
     */
    protected function createUserFromSso(array $attributes, ?string $nipSso, string $ssoUid): User
    {
        $role = $this->determineRole($attributes['affiliation'] ?? '');
        $orgUnit = $this->parseOrgUnit($attributes['orgUnitDN'] ?? '');

        $userData = [
            'nip' => null, // No legacy 18-char NIP for SSO-only users
            'nip_sso' => $nipSso,
            'sso_uid' => $ssoUid,
            'name' => $attributes['displayName'] ?? $this->extractNameFromEmail($attributes['mail']),
            'email' => $attributes['mail'],
            'password' => null, // SSO-only users don't have passwords
            'role' => $role,
            'position' => $this->determinePosition($attributes['affiliation'] ?? ''),
            'unit_kerja' => $orgUnit ?? 'Unknown',
            'affiliation' => $attributes['affiliation'],
            'auth_method' => 'sso',
            'sso_last_login' => now(),
        ];

        return User::create($userData);
    }

    /**
     * Extract 9-digit NIP from uid attribute
     *
     * @param string $uid UID from SAML (e.g., "123456789@uii.ac.id" or "123456789")
     * @return string|null 9-digit NIP
     */
    public function extractNipFromUid(string $uid): ?string
    {
        // Remove @uii.ac.id if present
        $nip = str_replace('@uii.ac.id', '', $uid);

        // Extract only digits
        $nip = preg_replace('/[^0-9]/', '', $nip);

        // Validate length (should be 9 digits)
        if (strlen($nip) === 9) {
            return $nip;
        }

        // If longer, take first 9 digits
        if (strlen($nip) > 9) {
            return substr($nip, 0, 9);
        }

        Log::warning('SSO: Invalid NIP format in uid', ['uid' => $uid, 'extracted' => $nip]);
        return null;
    }

    /**
     * Determine application role from eduPersonAffiliation
     *
     * @param string $affiliation eduPersonAffiliation value (e.g., "faculty", "staff", "student")
     * @return string Application role
     */
    public function determineRole(string $affiliation): string
    {
        $affiliation = strtolower($affiliation);

        // Map SAML affiliation to application roles
        $roleMap = [
            'faculty' => 'verifier',    // Faculty → Verifier (can approve activities)
            'staff' => 'user',          // Staff → User
            'student' => 'user',        // Student → User
            'employee' => 'user',       // Employee → User
            'member' => 'user',         // Member → User
        ];

        return $roleMap[$affiliation] ?? 'user'; // Default to 'user'
    }

    /**
     * Determine position from affiliation
     *
     * @param string $affiliation
     * @return string
     */
    protected function determinePosition(string $affiliation): string
    {
        $affiliation = strtolower($affiliation);

        $positionMap = [
            'faculty' => 'Dosen',
            'staff' => 'Tenaga Kependidikan',
            'student' => 'Mahasiswa',
            'employee' => 'Pegawai',
        ];

        return $positionMap[$affiliation] ?? 'Pranata TI';
    }

    /**
     * Parse organizational unit from DN (Distinguished Name)
     *
     * @param string $dn DN string (e.g., "ou=Fakultas Teknologi Industri,dc=uii,dc=ac,dc=id")
     * @return string|null Organizational unit name
     */
    public function parseOrgUnit(string $dn): ?string
    {
        if (empty($dn)) {
            return null;
        }

        // Parse DN and extract OU (Organizational Unit)
        if (preg_match('/ou=([^,]+)/i', $dn, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    /**
     * Extract name from email
     *
     * @param string $email
     * @return string
     */
    protected function extractNameFromEmail(string $email): string
    {
        $localPart = explode('@', $email)[0];

        // Convert to title case and replace separators
        $name = str_replace(['.', '_', '-'], ' ', $localPart);
        $name = Str::title($name);

        return $name;
    }

    /**
     * Validate SAML attributes format
     *
     * @param array $attributes
     * @return bool
     */
    public function validateAttributes(array $attributes): bool
    {
        // Check required fields
        if (empty($attributes['uid']) || empty($attributes['mail'])) {
            return false;
        }

        // Validate email format
        if (!filter_var($attributes['mail'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Validate uid format (should contain digits)
        if (!preg_match('/\d/', $attributes['uid'])) {
            return false;
        }

        return true;
    }
}
