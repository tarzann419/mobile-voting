<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;

class OrganizationRegistrationController extends Controller
{
    public function create()
    {
        return view('auth.organization-register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'organization_name' => ['required', 'string', 'max:255'],
            'organization_email' => ['required', 'string', 'email', 'max:255', 'unique:organizations,email'],
            'organization_phone' => ['nullable', 'string', 'max:20'],
            'organization_address' => ['nullable', 'string', 'max:500'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'admin_phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        DB::transaction(function () use ($request) {
            // Create organization
            $organization = Organization::create([
                'name' => $request->organization_name,
                'email' => $request->organization_email,
                'phone' => $request->organization_phone,
                'address' => $request->organization_address,
                'is_active' => true,
                'settings' => [
                    'allow_candidate_registration' => true,
                    'require_voter_accreditation' => true,
                    'max_elections_per_month' => 5,
                ],
            ]);

            // Create organization admin user
            $user = User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'phone' => $request->admin_phone,
                'password' => Hash::make($request->password),
                'organization_id' => $organization->id,
                'role' => 'organization_admin',
                'is_active' => true,
            ]);

            // Log in the newly created admin
            auth()->login($user);
        });

        return redirect()->route('dashboard')->with('success', 'Organization registered successfully! Welcome to the voting platform.');
    }
}
