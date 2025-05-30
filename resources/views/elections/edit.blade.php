<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Election: ') . $election->title }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('elections.show', $election) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Election
                </a>
                <a href="{{ route('elections.index') }}" class="bg-gray-600 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded">
                    Back to Elections
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('elections.update', $election) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                            <h3 class="text-lg font-medium mb-4">Basic Information</h3>
                            
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Election Title <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="title" name="title" value="{{ old('title', $election->title) }}" 
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                           placeholder="Enter election title" required>
                                    @error('title')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Description
                                    </label>
                                    <textarea id="description" name="description" rows="4"
                                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                              placeholder="Enter election description (optional)">{{ old('description', $election->description) }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Registration Period -->
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                            <h3 class="text-lg font-medium mb-4">Registration Period</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="registration_start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Registration Start <span class="text-red-500">*</span>
                                    </label>
                                    <input type="datetime-local" id="registration_start_date" name="registration_start_date" 
                                           value="{{ old('registration_start_date', $election->registration_start_date->format('Y-m-d\TH:i')) }}"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                           required>
                                    @error('registration_start_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="registration_end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Registration End <span class="text-red-500">*</span>
                                    </label>
                                    <input type="datetime-local" id="registration_end_date" name="registration_end_date" 
                                           value="{{ old('registration_end_date', $election->registration_end_date->format('Y-m-d\TH:i')) }}"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                           required>
                                    @error('registration_end_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Voting Period -->
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                            <h3 class="text-lg font-medium mb-4">Voting Period</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="voting_start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Voting Start <span class="text-red-500">*</span>
                                    </label>
                                    <input type="datetime-local" id="voting_start_date" name="voting_start_date" 
                                           value="{{ old('voting_start_date', $election->voting_start_date->format('Y-m-d\TH:i')) }}"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                           required>
                                    @error('voting_start_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="voting_end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Voting End <span class="text-red-500">*</span>
                                    </label>
                                    <input type="datetime-local" id="voting_end_date" name="voting_end_date" 
                                           value="{{ old('voting_end_date', $election->voting_end_date->format('Y-m-d\TH:i')) }}"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                           required>
                                    @error('voting_end_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Election Settings -->
                        <div class="pb-6">
                            <h3 class="text-lg font-medium mb-4">Election Settings</h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input type="hidden" name="allow_multiple_votes" value="0">
                                    <input type="checkbox" id="allow_multiple_votes" name="allow_multiple_votes" value="1"
                                           {{ old('allow_multiple_votes', $election->allow_multiple_votes) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="allow_multiple_votes" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                        Allow voters to change their votes before voting ends
                                    </label>
                                </div>

                                <div class="flex items-center">
                                    <input type="hidden" name="require_payment" value="0">
                                    <input type="checkbox" id="require_payment" name="require_payment" value="1"
                                           {{ old('require_payment', $election->require_payment) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="require_payment" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                        Require payment for candidate registration
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Current Status Info -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                            <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Current Status</h4>
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                This election is currently in <strong>{{ ucfirst($election->status) }}</strong> status.
                                @if($election->status === 'draft')
                                    You can modify all settings while the election is in draft status.
                                @endif
                            </p>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-between pt-6">
                            <div>
                                @if($election->status === 'draft')
                                    <form action="{{ route('elections.destroy', $election) }}" method="POST" class="inline-block"
                                          onsubmit="return confirm('Are you sure you want to delete this election? This action cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200">
                                            Delete Election
                                        </button>
                                    </form>
                                @endif
                            </div>
                            
                            <div class="flex space-x-3">
                                <a href="{{ route('elections.show', $election) }}" 
                                   class="px-6 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-200">
                                    Cancel
                                </a>
                                <button type="submit" 
                                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                    Update Election
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
