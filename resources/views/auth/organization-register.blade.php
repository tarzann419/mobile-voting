<x-guest-layout>
    <form method="POST" action="{{ route('organization.register') }}">
        @csrf

        <!-- Organization Information -->
        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Organization Information</h3>
            
            <!-- Organization Name -->
            <div class="mt-4">
                <x-input-label for="organization_name" :value="__('Organization Name')" />
                <x-text-input id="organization_name" class="block mt-1 w-full" type="text" name="organization_name" :value="old('organization_name')" required autofocus autocomplete="organization" />
                <x-input-error :messages="$errors->get('organization_name')" class="mt-2" />
            </div>

            <!-- Organization Email -->
            <div class="mt-4">
                <x-input-label for="organization_email" :value="__('Organization Email')" />
                <x-text-input id="organization_email" class="block mt-1 w-full" type="email" name="organization_email" :value="old('organization_email')" required autocomplete="email" />
                <x-input-error :messages="$errors->get('organization_email')" class="mt-2" />
            </div>

            <!-- Organization Phone -->
            <div class="mt-4">
                <x-input-label for="organization_phone" :value="__('Organization Phone (Optional)')" />
                <x-text-input id="organization_phone" class="block mt-1 w-full" type="tel" name="organization_phone" :value="old('organization_phone')" autocomplete="tel" />
                <x-input-error :messages="$errors->get('organization_phone')" class="mt-2" />
            </div>

            <!-- Organization Address -->
            <div class="mt-4">
                <x-input-label for="organization_address" :value="__('Organization Address (Optional)')" />
                <textarea id="organization_address" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" name="organization_address" rows="3" autocomplete="street-address">{{ old('organization_address') }}</textarea>
                <x-input-error :messages="$errors->get('organization_address')" class="mt-2" />
            </div>
        </div>

        <!-- Administrator Information -->
        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Administrator Information</h3>
            
            <!-- Admin Name -->
            <div class="mt-4">
                <x-input-label for="admin_name" :value="__('Administrator Name')" />
                <x-text-input id="admin_name" class="block mt-1 w-full" type="text" name="admin_name" :value="old('admin_name')" required autocomplete="name" />
                <x-input-error :messages="$errors->get('admin_name')" class="mt-2" />
            </div>

            <!-- Admin Email -->
            <div class="mt-4">
                <x-input-label for="admin_email" :value="__('Administrator Email')" />
                <x-text-input id="admin_email" class="block mt-1 w-full" type="email" name="admin_email" :value="old('admin_email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('admin_email')" class="mt-2" />
            </div>

            <!-- Admin Phone -->
            <div class="mt-4">
                <x-input-label for="admin_phone" :value="__('Administrator Phone (Optional)')" />
                <x-text-input id="admin_phone" class="block mt-1 w-full" type="tel" name="admin_phone" :value="old('admin_phone')" autocomplete="tel" />
                <x-input-error :messages="$errors->get('admin_phone')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <!-- Terms and Features -->
        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
            <h4 class="font-medium text-blue-900 dark:text-blue-100 mb-2">What you'll get:</h4>
            <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
                <li>✓ Unlimited voter registration and accreditation</li>
                <li>✓ Multiple election management with real-time results</li>
                <li>✓ Candidate registration with payment processing</li>
                <li>✓ Secure voting system with audit trails</li>
                <li>✓ Comprehensive reporting and analytics</li>
                <li>✓ 24/7 technical support</li>
            </ul>
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already have an account?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register Organization') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
