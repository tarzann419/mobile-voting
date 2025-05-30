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
        <div class="bg-teal-600 text-white px-6 py-4">
            <h1 class="text-2xl font-bold">Voter Accreditation</h1>
            <p class="text-teal-100">Manage voter eligibility and screening</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-700">Total Applications</h3>
            <p class="text-3xl font-bold text-teal-600">{{ $stats['total'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-700">Pending Review</h3>
            <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-700">Approved</h3>
            <p class="text-3xl font-bold text-green-600">{{ $stats['approved'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-700">Rejected</h3>
            <p class="text-3xl font-bold text-red-600">{{ $stats['rejected'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Election</label>
                <select wire:model="selectedElection" 
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-teal-500">
                    <option value="">All Elections</option>
                    @foreach ($elections as $election)
                        <option value="{{ $election->id }}">{{ $election->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select wire:model="statusFilter" 
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-teal-500">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" 
                       wire:model="searchTerm" 
                       placeholder="Search by name or email..."
                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-teal-500">
            </div>
            
            <div class="flex items-end">
                <button wire:click="resetFilters" 
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                    Reset Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Accreditations List -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        @if ($accreditations->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Voter
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Election
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Applied Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($accreditations as $accreditation)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full" 
                                                 src="{{ $accreditation->user->avatar ?? '/default-avatar.png' }}" 
                                                 alt="{{ $accreditation->user->name }}">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $accreditation->user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $accreditation->user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $accreditation->election->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $accreditation->election->organization->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $accreditation->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($accreditation->status === 'approved') bg-green-100 text-green-800
                                        @elseif($accreditation->status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($accreditation->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if ($accreditation->status === 'pending')
                                        <div class="flex space-x-2">
                                            <button wire:click="approveAccreditation({{ $accreditation->id }})" 
                                                    class="text-green-600 hover:text-green-900">
                                                Approve
                                            </button>
                                            <button wire:click="rejectAccreditation({{ $accreditation->id }})" 
                                                    class="text-red-600 hover:text-red-900">
                                                Reject
                                            </button>
                                        </div>
                                    @else
                                        <button wire:click="viewDetails({{ $accreditation->id }})" 
                                                class="text-teal-600 hover:text-teal-900">
                                            View Details
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-3 bg-gray-50">
                {{ $accreditations->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.196-2.196M17 20H7m10 0v-2c0-5.523-4.477-10-10-10s-10 4.477-10 10v2m10 0H7m10 0v-2a3 3 0 00-3-3m-3 3h.01M7 20v-2a3 3 0 013-3m-3 3h-.01" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No accreditation applications</h3>
                <p class="mt-1 text-sm text-gray-500">No voter accreditation applications found matching your criteria.</p>
            </div>
        @endif
    </div>

    <!-- Bulk Actions -->
    @if ($selectedAccreditations && count($selectedAccreditations) > 0)
        <div class="fixed bottom-4 right-4 bg-white shadow-lg rounded-lg p-4 border">
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">{{ count($selectedAccreditations) }} selected</span>
                <button wire:click="bulkApprove" 
                        class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700">
                    Approve All
                </button>
                <button wire:click="bulkReject" 
                        class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                    Reject All
                </button>
            </div>
        </div>
    @endif

    <!-- Details Modal -->
    @if ($showDetailsModal && $selectedAccreditation)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        Accreditation Details
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Voter Name</label>
                                <p class="text-sm text-gray-900">{{ $selectedAccreditation->user->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <p class="text-sm text-gray-900">{{ $selectedAccreditation->user->email }}</p>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Election</label>
                            <p class="text-sm text-gray-900">{{ $selectedAccreditation->election->name }}</p>
                        </div>
                        
                        @if ($selectedAccreditation->verification_data)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Verification Data</label>
                                <div class="mt-1 p-3 bg-gray-50 rounded-lg">
                                    <pre class="text-xs text-gray-700">{{ json_encode($selectedAccreditation->verification_data, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        @endif
                        
                        @if ($selectedAccreditation->notes)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Notes</label>
                                <p class="text-sm text-gray-900">{{ $selectedAccreditation->notes }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-end space-x-4 mt-6">
                        <button wire:click="closeDetailsModal"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Close
                        </button>
                        @if ($selectedAccreditation->status === 'pending')
                            <button wire:click="approveAccreditation({{ $selectedAccreditation->id }})" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                Approve
                            </button>
                            <button wire:click="rejectAccreditation({{ $selectedAccreditation->id }})" 
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                Reject
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
