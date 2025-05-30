<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Candidate (Admin)') }}
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
                    <strong>No positions available for candidate creation.</strong><br>
                    There are no active elections with open registration periods in your organization.
                    <div class="mt-2">
                        <a href="{{ route('candidates.index') }}" class="text-yellow-800 underline">Back to candidate management</a>
                    </div>
                </div>
            @elseif($organizationUsers->count() === 0)
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4">
                    <strong>No users available for candidate creation.</strong><br>
                    All active voter users in your organization are already registered as candidates, or there are no active voter users available.
                    <div class="mt-2">
                        <a href="{{ route('candidates.index') }}" class="text-yellow-800 underline">Back to candidate management</a>
                    </div>
                </div>
            @else
                <!-- Admin Candidate Creation Form -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-6">Create Candidate for User</h3>
                        
                        <form action="{{ route('admin.candidates.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf

                            <!-- User Selection -->
                            <div>
                                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Select User <span class="text-red-500">*</span>
                                </label>
                                <select name="user_id" id="user_id" required 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('user_id') border-red-500 @enderror">
                                    <option value="">Choose a user to create candidate for...</option>
                                    @foreach($organizationUsers as $orgUser)
                                        <option value="{{ $orgUser->id }}" {{ old('user_id') == $orgUser->id ? 'selected' : '' }}>
                                            {{ $orgUser->name }} ({{ $orgUser->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Start typing to search for a user by name or email.</p>
                            </div>

                            <!-- Position Selection -->
                            <div>
                                <label for="position_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Position <span class="text-red-500">*</span>
                                </label>
                                <select name="position_id" id="position_id" required 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('position_id') border-red-500 @enderror">
                                    <option value="">Choose a position...</option>
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
                                <p class="mt-1 text-sm text-gray-500">Select the position the user will run for.</p>
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
                                          placeholder="Provide information about the candidate's background, experience, and qualifications...">{{ old('bio') }}</textarea>
                                @error('bio')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Candidate's biography and background. Maximum 1000 characters.</p>
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
                                          placeholder="Outline the candidate's campaign promises, policies, goals, and what they plan to achieve if elected...">{{ old('manifesto') }}</textarea>
                                @error('manifesto')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Candidate's campaign platform and manifesto. Maximum 5000 characters.</p>
                                <div class="text-sm text-gray-400 mt-1">
                                    <span id="manifesto-count">0</span>/5000 characters
                                </div>
                            </div>

                            <!-- Auto-approval Option -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <input type="checkbox" name="auto_approve" id="auto_approve" value="1" 
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                           {{ old('auto_approve') ? 'checked' : '' }}>
                                    <label for="auto_approve" class="ml-2 block text-sm text-gray-700">
                                        Auto-approve this candidate application
                                    </label>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">If checked, the candidate will be automatically approved. Otherwise, it will go through the normal approval process.</p>
                            </div>

                            <!-- Admin Information -->
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h4 class="font-medium text-blue-900 mb-2">Admin Candidate Creation</h4>
                                <ul class="text-sm text-blue-700 space-y-1">
                                    <li>• You are creating a candidate application on behalf of a user</li>
                                    <li>• The user will be notified about their candidate registration</li>
                                    <li>• You can choose to auto-approve or send through normal approval process</li>
                                    <li>• Ensure you have permission from the user before creating their candidate profile</li>
                                </ul>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex justify-between items-center pt-6">
                                <a href="{{ route('candidates.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                    Cancel
                                </a>
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                                    Create Candidate
                                </button>
                            </div>
                        </form>
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
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const userId = document.getElementById('user_id').value;
            const positionId = document.getElementById('position_id').value;
            const bio = document.getElementById('bio').value.trim();
            const manifesto = document.getElementById('manifesto').value.trim();

            if (!userId) {
                e.preventDefault();
                alert('Please select a user to create candidate for.');
                return false;
            }

            if (!positionId) {
                e.preventDefault();
                alert('Please select a position.');
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
            submitButton.textContent = 'Creating...';
        });
    </script>

    <!-- Select2 CSS and JS for searchable dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <style>
        /* Custom Select2 styling to match Tailwind */
        .select2-container--default .select2-selection--single {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            height: 42px;
            padding: 8px 12px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px;
            padding-left: 0;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: 8px;
        }
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #6366f1;
            box-shadow: 0 0 0 1px #6366f1;
        }
        .select2-dropdown {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
        }
        .select2-search--dropdown .select2-search__field {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 8px 12px;
        }
    </style>
    
    <script>
        $(document).ready(function() {
            // Initialize Select2 for user dropdown
            $('#user_id').select2({
                placeholder: 'Search for a user by name or email...',
                allowClear: true,
                width: '100%',
                matcher: function(params, data) {
                    // If there are no search terms, return all of the data
                    if ($.trim(params.term) === '') {
                        return data;
                    }

                    // Do not display the item if there is no 'text' property
                    if (typeof data.text === 'undefined') {
                        return null;
                    }

                    // `params.term` should be the term that is used for searching
                    // `data.text` is the text that is displayed for the data object
                    var searchTerm = params.term.toLowerCase();
                    var text = data.text.toLowerCase();
                    
                    // Check if the text contains the search term
                    if (text.indexOf(searchTerm) > -1) {
                        var modifiedData = $.extend({}, data, true);
                        return modifiedData;
                    }

                    // Return `null` if the term should not be displayed
                    return null;
                }
            });

            // Also make position dropdown searchable  
            $('#position_id').select2({
                placeholder: 'Search for a position...',
                allowClear: true,
                width: '100%'
            });

            // Add some helpful features
            $('#user_id').on('select2:select', function (e) {
                var data = e.params.data;
                console.log('Selected user:', data.text);
            });
        });
    </script>
</x-app-layout>
