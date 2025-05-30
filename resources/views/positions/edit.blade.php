<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Position') }}
            </h2>
            <a href="{{ route('positions.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back to Positions
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">{/* Content wrapper */}

        <form action="{{ route('positions.update', $position) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Position Title</label>
                <input type="text" id="title" name="title" value="{{ old('title', $position->title) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       required>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea id="description" name="description" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $position->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="max_candidates" class="block text-sm font-medium text-gray-700 mb-2">Maximum Candidates</label>
                    <input type="number" id="max_candidates" name="max_candidates" value="{{ old('max_candidates', $position->max_candidates) }}"
                           min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('max_candidates')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
                    <input type="number" id="order" name="order" value="{{ old('order', $position->order) }}"
                           min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('order')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="registration_fee" class="block text-sm font-medium text-gray-700 mb-2">Registration Fee (₱)</label>
                <input type="number" id="registration_fee" name="registration_fee" value="{{ old('registration_fee', $position->amount_required ?? 0) }}"
                       min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="0.00">
                <p class="mt-1 text-xs text-gray-500">
                    Amount candidates must pay to register for this position (set to 0 for free registration)
                </p>
                @error('registration_fee')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Election Information</h3>
                <p class="text-sm text-gray-600">
                    <strong>Election:</strong> {{ $position->election->title }}<br>
                    <strong>Status:</strong> 
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                        {{ $position->election->status === 'draft' ? 'bg-gray-100 text-gray-800' : 
                           ($position->election->status === 'active' ? 'bg-green-100 text-green-800' : 
                            'bg-blue-100 text-blue-800') }}">
                        {{ ucfirst($position->election->status) }}
                    </span>
                </p>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('positions.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                    Update Position
                </button>
            </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
