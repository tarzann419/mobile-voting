<?php

use App\Models\User;
use App\Models\Organization;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public ?int $organization_id = null;
    public ?Organization $organization = null;

    /**
     * Mount the component and check for organization parameter
     */
    public function mount(): void
    {
        // Get organization ID from URL parameter
        $orgId = request()->query('org_id');
        
        if ($orgId) {
            $organization = Organization::where('id', $orgId)
                ->where('is_active', true)
                ->first();
                
            if ($organization) {
                $this->organization_id = $organization->id;
                $this->organization = $organization;
            }
        }
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ];

        // Only require organization_id if not provided via URL
        if (!$this->organization_id) {
            $rules['organization_id'] = ['required', 'exists:organizations,id'];
        }

        $validated = $this->validate($rules);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'voter'; // Set default role for new registrations
        $validated['is_active'] = true; // Activate user by default
        $validated['email_verified_at'] = now(); // Auto-verify email for simplicity
        
        // Use organization from URL if available
        if ($this->organization_id) {
            $validated['organization_id'] = $this->organization_id;
        }

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Get available organizations for manual selection (fallback)
     */
    public function getOrganizationsProperty()
    {
        return Organization::where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}; ?>

<div>
    <form wire:submit="register">
        <!-- Organization Info Display -->
        @if($organization)
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-md">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-blue-900 dark:text-blue-100">
                            Registering for: {{ $organization->name }}
                        </h3>
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            You will be automatically assigned to this organization
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Organization Selection (only show if no org_id in URL) -->
        @if(!$organization)
            <div class="mt-4">
                <x-input-label for="organization_id" :value="__('Select Organization')" />
                <select wire:model="organization_id" id="organization_id" name="organization_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                    <option value="">Choose an organization...</option>
                    @foreach($this->organizations as $org)
                        <option value="{{ $org->id }}">{{ $org->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('organization_id')" class="mt-2" />
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Select the organization you want to participate in elections for.
                </p>
            </div>
        @endif

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</div>
