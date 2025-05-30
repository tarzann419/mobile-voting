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

    <!-- Header with Create Button -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
        <div class="bg-indigo-600 text-white px-6 py-4 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">Election Management</h1>
                <p class="text-indigo-100">Manage your organization's elections</p>
            </div>
            <button wire:click="openCreateForm" 
                    class="bg-indigo-500 hover:bg-indigo-400 text-white px-4 py-2 rounded-lg">
                Create New Election
            </button>
        </div>
    </div>

    <!-- Elections List -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        @if ($elections->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Election
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Timeline
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Positions
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($elections as $election)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $election->name }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($election->description, 50) }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($election->status === 'draft') bg-gray-100 text-gray-800
                                        @elseif($election->status === 'active') bg-green-100 text-green-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ ucfirst($election->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>Start: {{ $election->start_time->format('M d, Y H:i') }}</div>
                                    <div>End: {{ $election->end_time->format('M d, Y H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $election->positions->count() }} positions
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button wire:click="editElection({{ $election->id }})" 
                                            class="text-indigo-600 hover:text-indigo-900">
                                        Edit
                                    </button>
                                    <a href="/elections/{{ $election->id }}/dashboard" 
                                       class="text-green-600 hover:text-green-900">
                                        Dashboard
                                    </a>
                                    @if ($election->status === 'draft')
                                        <button wire:click="deleteElection({{ $election->id }})" 
                                                class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Are you sure?')">
                                            Delete
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No elections</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating your first election.</p>
            </div>
        @endif
    </div>

    <!-- Create/Edit Election Modal -->
    @if ($showForm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        {{ $editingElection ? 'Edit Election' : 'Create New Election' }}
                    </h3>
                    
                    <form wire:submit.prevent="saveElection">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Election Name *
                                </label>
                                <input type="text" 
                                       wire:model="formData.name" 
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-indigo-500"
                                       placeholder="e.g., Student Council Elections 2025">
                                @error('formData.name') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Description
                                </label>
                                <textarea wire:model="formData.description" 
                                          rows="3" 
                                          class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-indigo-500"
                                          placeholder="Describe the election purpose and process..."></textarea>
                                @error('formData.description') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Start Date & Time *
                                </label>
                                <input type="datetime-local" 
                                       wire:model="formData.start_time" 
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-indigo-500">
                                @error('formData.start_time') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    End Date & Time *
                                </label>
                                <input type="datetime-local" 
                                       wire:model="formData.end_time" 
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-indigo-500">
                                @error('formData.end_time') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4 mt-6">
                            <button type="button" 
                                    wire:click="closeForm"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                {{ $editingElection ? 'Update Election' : 'Create Election' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
