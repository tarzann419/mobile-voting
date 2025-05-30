@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <!-- Your existing form content -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-medium mb-6">Create Candidate for User (Modern Version)</h3>
                
                <form action="{{ route('admin.candidates.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- User Selection with Choices.js -->
                    <div>
                        <label for="user_id_modern" class="block text-sm font-medium text-gray-700 mb-2">
                            Select User <span class="text-red-500">*</span>
                        </label>
                        <select name="user_id" id="user_id_modern" required 
                                class="choices-select">
                            <option value="">Choose a user to create candidate for...</option>
                            @foreach($organizationUsers as $orgUser)
                                <option value="{{ $orgUser->id }}">
                                    {{ $orgUser->name }} ({{ $orgUser->email }})
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Start typing to search for a user by name or email.</p>
                    </div>

                    <!-- Rest of your form fields -->
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Choices.js for modern searchable dropdowns -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Choices.js for searchable dropdown
    const userSelect = new Choices('#user_id_modern', {
        searchEnabled: true,
        searchChoices: true,
        searchFields: ['label', 'value'],
        searchPlaceholderValue: 'Search for a user...',
        noResultsText: 'No users found',
        itemSelectText: 'Press to select',
        allowHTML: false,
        removeItemButton: true,
        classNames: {
            containerOuter: 'choices w-full',
            containerInner: 'choices__inner bg-white border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500',
        }
    });
});
</script>
@endsection
