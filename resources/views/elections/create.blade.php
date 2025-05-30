<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Create New Election') }}
            </h2>
            <a href="{{ route('elections.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Elections
            </a>
        </div>
    </x-slot>
    
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('elections.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <!-- Basic Information -->
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                            <h3 class="text-lg font-medium mb-4">Basic Information</h3>
                            
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Election Title <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="title" name="title" value="{{ old('title') }}" 
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
                                    placeholder="Enter election description (optional)">{{ old('description') }}</textarea>
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
                                    value="{{ old('registration_start_date') }}"
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
                                    value="{{ old('registration_end_date') }}"
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
                                    value="{{ old('voting_start_date') }}"
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
                                    value="{{ old('voting_end_date') }}"
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
                                    <input type="checkbox" id="allow_multiple_votes" name="allow_multiple_votes" value="1"
                                    {{ old('allow_multiple_votes') ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="allow_multiple_votes" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                        Allow voters to change their votes before voting ends
                                    </label>
                                </div>
                                
                                <div class="flex items-center">
                                    <input type="checkbox" id="require_payment" name="require_payment" value="1"
                                    {{ old('require_payment') ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="require_payment" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                        Require payment for candidate registration
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-3 pt-6">
                            <a href="{{ route('elections.index') }}" 
                            class="px-6 py-2 border border-gray-300 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-200">
                            Cancel
                        </a>
                        <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                        Create Election
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<script>
    // Auto-fill dates based on previous selections
    document.addEventListener('DOMContentLoaded', function() {
        const regStart = document.getElementById('registration_start_date');
        const regEnd = document.getElementById('registration_end_date');
        const voteStart = document.getElementById('voting_start_date');
        const voteEnd = document.getElementById('voting_end_date');
        
        regStart.addEventListener('change', function() {
            if (!regEnd.value) {
                const date = new Date(this.value);
                date.setDate(date.getDate() + 7); // 7 days later
                regEnd.value = date.toISOString().slice(0, 16);
            }
        });
        
        regEnd.addEventListener('change', function() {
            if (!voteStart.value) {
                const date = new Date(this.value);
                date.setDate(date.getDate() + 1); // 1 day later
                voteStart.value = date.toISOString().slice(0, 16);
            }
        });
        
        voteStart.addEventListener('change', function() {
            if (!voteEnd.value) {
                const date = new Date(this.value);
                date.setDate(date.getDate() + 3); // 3 days later
                voteEnd.value = date.toISOString().slice(0, 16);
            }
        });
    });
</script>
</x-app-layout>
