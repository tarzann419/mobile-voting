<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if(auth()->user()->isOrganizationAdmin())
                {{ __('Register as Candidate (Self)') }}
            @else
                {{ __('Register as Candidate') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <!-- Status Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->has('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    {{ $errors->first('error') }}
                </div>
            @endif

            @if($availablePositions->count() === 0)
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4">
                    <strong>No positions available for registration.</strong><br>
                    Either there are no active elections with open registration, or you have already registered for all available positions.
                    <div class="mt-2">
                        <a href="{{ route('candidates.index') }}" class="text-yellow-800 underline">View your existing applications</a>
                    </div>
                </div>
            @else
                <!-- Registration Form -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-6">Candidate Registration Form</h3>
                        
                        <form action="{{ route('candidates.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf

                            <!-- Position Selection -->
                            <div>
                                <label for="position_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Position <span class="text-red-500">*</span>
                                </label>
                                <select name="position_id" id="position_id" required 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('position_id') border-red-500 @enderror">
                                    <option value="">Choose a position to run for...</option>
                                    @foreach($availablePositions as $position)
                                        <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                            {{ $position->title }} - {{ $position->election->title }}
                                            (Registration ends: {{ $position->election->registration_end_date->format('M j, Y') }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('position_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Select the position you want to run for. You can only register for one position per election.</p>
                            </div>

                            <!-- Profile Photo -->
                            <div>
                                <label for="profile_photo" class="block text-sm font-medium text-gray-700 mb-2">
                                    Profile Photo
                                </label>
                                <input type="file" name="profile_photo" id="profile_photo" accept="image/jpeg,image/png,image/jpg"
                                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 @error('profile_photo') border-red-500 @enderror">
                                @error('profile_photo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Optional. Upload a professional photo (JPEG, PNG only, max 2MB).</p>
                            </div>

                            <!-- Biography -->
                            <div>
                                <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                                    Biography <span class="text-red-500">*</span>
                                </label>
                                <textarea name="bio" id="bio" rows="4" required maxlength="1000"
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('bio') border-red-500 @enderror"
                                          placeholder="Tell voters about yourself, your background, experience, and qualifications...">{{ old('bio') }}</textarea>
                                @error('bio')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Introduce yourself to the voters. Maximum 1000 characters.</p>
                                <div class="text-sm text-gray-400 mt-1">
                                    <span id="bio-count">0</span>/1000 characters
                                </div>
                            </div>

                            <!-- Manifesto -->
                            <div>
                                <label for="manifesto" class="block text-sm font-medium text-gray-700 mb-2">
                                    Campaign Manifesto <span class="text-red-500">*</span>
                                </label>
                                <textarea name="manifesto" id="manifesto" rows="8" required maxlength="5000"
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('manifesto') border-red-500 @enderror"
                                          placeholder="Outline your campaign promises, policies, goals, and what you plan to achieve if elected...">{{ old('manifesto') }}</textarea>
                                @error('manifesto')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Detail your campaign platform and what you will do if elected. Maximum 5000 characters.</p>
                                <div class="text-sm text-gray-400 mt-1">
                                    <span id="manifesto-count">0</span>/5000 characters
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-medium text-gray-900 mb-2">Important Information</h4>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li>• Your application will be reviewed by election administrators</li>
                                    <li>• You must meet all eligibility requirements for the position</li>
                                    <li>• False information may result in disqualification</li>
                                    <li>• You can only register for one position per election</li>
                                    <li>• Registration must be completed before the deadline</li>
                                </ul>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex justify-between items-center pt-6">
                                <a href="{{ route('candidates.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                    Cancel
                                </a>
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                                    Submit Application
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="bg-blue-50 border border-blue-200 overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-blue-800 mb-4">What happens next?</h3>
                        <div class="text-blue-700 space-y-2">
                            <p><strong>1. Review Process:</strong> Your application will be reviewed by election administrators.</p>
                            <p><strong>2. Notification:</strong> You will be notified of the decision via email and your candidate dashboard.</p>
                            <p><strong>3. Campaign Period:</strong> If approved, you can start campaigning according to election rules.</p>
                            <p><strong>4. Voting:</strong> Eligible voters can vote for you during the voting period.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Character counters
        document.addEventListener('DOMContentLoaded', function() {
            const bioTextarea = document.getElementById('bio');
            const bioCounter = document.getElementById('bio-count');
            const manifestoTextarea = document.getElementById('manifesto');
            const manifestoCounter = document.getElementById('manifesto-count');

            function updateBioCounter() {
                const count = bioTextarea.value.length;
                bioCounter.textContent = count;
                bioCounter.style.color = count > 900 ? '#ef4444' : '#9ca3af';
            }

            function updateManifestoCounter() {
                const count = manifestoTextarea.value.length;
                manifestoCounter.textContent = count;
                manifestoCounter.style.color = count > 4500 ? '#ef4444' : '#9ca3af';
            }

            // Initialize counters
            updateBioCounter();
            updateManifestoCounter();

            // Add event listeners
            bioTextarea.addEventListener('input', updateBioCounter);
            manifestoTextarea.addEventListener('input', updateManifestoCounter);

            // Position selection handler
            const positionSelect = document.getElementById('position_id');
            positionSelect.addEventListener('change', function() {
                if (this.value) {
                    // You could add logic here to show position-specific information
                    console.log('Selected position:', this.value);
                }
            });
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const positionId = document.getElementById('position_id').value;
            const bio = document.getElementById('bio').value.trim();
            const manifesto = document.getElementById('manifesto').value.trim();

            if (!positionId) {
                e.preventDefault();
                alert('Please select a position to run for.');
                return false;
            }

            if (!bio || bio.length < 10) {
                e.preventDefault();
                alert('Please provide a meaningful biography (at least 10 characters).');
                return false;
            }

            if (!manifesto || manifesto.length < 50) {
                e.preventDefault();
                alert('Please provide a detailed manifesto (at least 50 characters).');
                return false;
            }

            // Show loading state
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.textContent = 'Submitting...';
        });
    </script>
</x-app-layout>
