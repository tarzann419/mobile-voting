<div class="container mx-auto px-4 py-6">
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Header -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
        <div class="bg-orange-600 text-white px-6 py-4 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">Position Management</h1>
                <p class="text-orange-100">
                    @if ($election)
                        {{ $election->name }} - {{ ucfirst($election->status) }}
                    @else
                        Manage election positions
                    @endif
                </p>
            </div>
            @if ($election && $election->status === 'draft')
                <button wire:click="openCreateForm" 
                        class="bg-orange-500 hover:bg-orange-400 text-white px-4 py-2 rounded-lg">
                    Add Position
                </button>
            @endif
        </div>
    </div>

    @if ($election)
        <!-- Positions List -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            @if ($positions->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                    @foreach ($positions as $position)
                        <div class="border rounded-lg p-6 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-bold text-gray-900">{{ $position->name }}</h3>
                                @if ($election->status === 'draft')
                                    <div class="flex space-x-2">
                                        <button wire:click="editPosition({{ $position->id }})" 
                                                class="text-orange-600 hover:text-orange-900">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                            </svg>
                                        </button>
                                        <button wire:click="deletePosition({{ $position->id }})" 
                                                class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Are you sure?')">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9zM4 5a2 2 0 012-2h8a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zM6 5a1 1 0 012 0v6a1 1 0 11-2 0V5zm6 0a1 1 0 10-2 0v6a1 1 0 102 0V5z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            
                            <p class="text-gray-600 mb-4 text-sm">{{ $position->description }}</p>
                            
                            <div class="space-y-2 text-sm">
                                @if ($position->registration_fee > 0)
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Registration Fee:</span>
                                        <span class="font-semibold text-orange-600">
                                            ${{ number_format($position->registration_fee, 2) }}
                                        </span>
                                    </div>
                                @endif
                                
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Candidates:</span>
                                    <span class="font-semibold">{{ $position->candidates_count ?? 0 }}</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Max Candidates:</span>
                                    <span class="font-semibold">{{ $position->max_candidates ?? 'Unlimited' }}</span>
                                </div>
                            </div>

                            @if ($position->candidates_count > 0)
                                <div class="mt-4 pt-4 border-t">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Candidates:</h4>
                                    <div class="space-y-1">
                                        @foreach ($position->candidates as $candidate)
                                            <div class="flex items-center text-sm">
                                                <img src="{{ $candidate->photo ?? '/default-avatar.png' }}" 
                                                     alt="{{ $candidate->user->name }}"
                                                     class="w-6 h-6 rounded-full mr-2">
                                                <span>{{ $candidate->user->name }}</span>
                                                <span class="ml-auto px-2 py-1 text-xs rounded
                                                    @if($candidate->status === 'approved') bg-green-100 text-green-800
                                                    @elseif($candidate->status === 'pending') bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800 @endif">
                                                    {{ ucfirst($candidate->status) }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No positions</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by adding positions for this election.</p>
                </div>
            @endif
        </div>
    @else
        <div class="bg-white shadow-lg rounded-lg p-6 text-center">
            <p class="text-gray-500">No election selected.</p>
        </div>
    @endif

    <!-- Create/Edit Position Modal -->
    @if ($showForm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        {{ $editingPosition ? 'Edit Position' : 'Add New Position' }}
                    </h3>
                    
                    <form wire:submit.prevent="savePosition">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Position Name *
                                </label>
                                <input type="text" 
                                       wire:model="formData.name" 
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-orange-500"
                                       placeholder="e.g., President, Secretary, Treasurer">
                                @error('formData.name') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Description
                                </label>
                                <textarea wire:model="formData.description" 
                                          rows="3" 
                                          class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-orange-500"
                                          placeholder="Describe the responsibilities and requirements..."></textarea>
                                @error('formData.description') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">
                                        Registration Fee ($)
                                    </label>
                                    <input type="number" 
                                           wire:model="formData.registration_fee" 
                                           min="0" 
                                           step="0.01"
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-orange-500"
                                           placeholder="0.00">
                                    @error('formData.registration_fee') 
                                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">
                                        Max Candidates
                                    </label>
                                    <input type="number" 
                                           wire:model="formData.max_candidates" 
                                           min="1"
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-orange-500"
                                           placeholder="Leave empty for unlimited">
                                    @error('formData.max_candidates') 
                                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4 mt-6">
                            <button type="button" 
                                    wire:click="closeForm"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                                {{ $editingPosition ? 'Update Position' : 'Add Position' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
