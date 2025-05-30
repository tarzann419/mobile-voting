<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Candidate Management') }}
            </h2>
            <div class="flex space-x-2">
                @if(auth()->user()->isOrganizationAdmin())
                    <a href="{{ route('admin.candidates.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Add Candidate
                    </a>
                @endif
                <a href="{{ route('candidates.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Register as Candidate
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Filter Candidates</h3>
                    <form method="GET" action="{{ route('candidates.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="election_id" class="block text-sm font-medium text-gray-700 mb-2">Election</label>
                            <select name="election_id" id="election_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" onchange="updatePositions()">
                                <option value="">All Elections</option>
                                @foreach($elections as $election)
                                    <option value="{{ $election->id }}" {{ request('election_id') == $election->id ? 'selected' : '' }}>
                                        {{ $election->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="position_id" class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                            <select name="position_id" id="position_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Positions</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}" 
                                            data-election="{{ $position->election_id }}"
                                            {{ request('position_id') == $position->id ? 'selected' : '' }}>
                                        {{ $position->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="flex items-end space-x-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Apply Filters
                            </button>
                            <a href="{{ route('candidates.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Candidates List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-lg font-medium">
                                Candidates ({{ $candidates->total() }})
                                @if(request()->hasAny(['status', 'election_id', 'position_id']))
                                    <span class="text-sm font-normal text-gray-500">- Filtered</span>
                                @endif
                            </h3>
                            @if($candidates->total() > 0)
                                <p class="text-sm text-gray-600 mt-1">
                                    Showing {{ $candidates->firstItem() }} to {{ $candidates->lastItem() }} of {{ $candidates->total() }} candidates
                                </p>
                            @endif
                        </div>
                        
                        @if(request()->hasAny(['status', 'election_id', 'position_id']))
                            <div class="text-sm text-gray-600">
                                <span class="font-medium">Active filters:</span>
                                @if(request('status'))
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 ml-1">
                                        Status: {{ ucfirst(request('status')) }}
                                    </span>
                                @endif
                                @if(request('election_id'))
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800 ml-1">
                                        Election: {{ $elections->firstWhere('id', request('election_id'))->title ?? 'Unknown' }}
                                    </span>
                                @endif
                                @if(request('position_id'))
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-800 ml-1">
                                        Position: {{ $positions->firstWhere('id', request('position_id'))->title ?? 'Unknown' }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                    
                    @if($candidates->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Candidate</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Election</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applied</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($candidates as $candidate)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        @if($candidate->profile_photo_path)
                                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($candidate->profile_photo_path) }}" alt="{{ $candidate->user->name }}">
                                                        @else
                                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                                <span class="text-sm font-medium text-gray-700">{{ substr($candidate->user->name, 0, 1) }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $candidate->user->name }}</div>
                                                        <div class="text-sm text-gray-500">{{ $candidate->user->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $candidate->position->title }}</div>
                                                <div class="text-sm text-gray-500">{{ $candidate->position->description }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $candidate->position->election->title }}</div>
                                                <div class="text-sm text-gray-500">{{ $candidate->position->election->status }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($candidate->status === 'pending')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Pending
                                                    </span>
                                                @elseif($candidate->status === 'approved')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Approved
                                                    </span>
                                                @elseif($candidate->status === 'rejected')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Rejected
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $candidate->created_at->format('M j, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('candidates.show', $candidate->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        View
                                                    </a>
                                                    @if($candidate->status === 'pending')
                                                        <form action="{{ route('candidates.approve', $candidate->id) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="text-green-600 hover:text-green-900" onclick="return confirm('Are you sure you want to approve this candidate?')">
                                                                Approve
                                                            </button>
                                                        </form>
                                                        <button type="button" class="text-red-600 hover:text-red-900" onclick="showRejectModal({{ $candidate->id }})">
                                                            Reject
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination Links -->
                        <div class="mt-6">
                            {{ $candidates->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-400 text-lg mb-4">No candidates found</div>
                            <p class="text-gray-500">
                                @if(request('status') || request('position_id'))
                                    Try adjusting your filters or 
                                    <a href="{{ route('candidates.index') }}" class="text-indigo-600 hover:text-indigo-900">view all candidates</a>.
                                @else
                                    No candidates have registered yet.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Candidate</h3>
                <form id="rejectForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Reason for rejection</label>
                        <textarea name="reason" id="reason" rows="4" required 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Please provide a reason for rejecting this candidate..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideRejectModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Cancel
                        </button>
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Reject Candidate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showRejectModal(candidateId) {
            const modal = document.getElementById('rejectModal');
            const form = document.getElementById('rejectForm');
            form.action = `/candidates/${candidateId}/reject`;
            modal.classList.remove('hidden');
        }

        function hideRejectModal() {
            const modal = document.getElementById('rejectModal');
            const textarea = document.getElementById('reason');
            modal.classList.add('hidden');
            textarea.value = '';
        }

        function updatePositions() {
            const electionSelect = document.getElementById('election_id');
            const positionSelect = document.getElementById('position_id');
            const selectedElection = electionSelect.value;
            
            // Reset position dropdown
            const options = positionSelect.querySelectorAll('option');
            options.forEach(option => {
                if (option.value === '') {
                    option.style.display = 'block';
                } else {
                    const electionId = option.getAttribute('data-election');
                    if (selectedElection === '' || electionId === selectedElection) {
                        option.style.display = 'block';
                    } else {
                        option.style.display = 'none';
                    }
                }
            });
            
            // Reset position selection if it's no longer valid
            if (selectedElection !== '') {
                const currentPosition = positionSelect.value;
                const currentOption = positionSelect.querySelector(`option[value="${currentPosition}"]`);
                if (currentOption && currentOption.style.display === 'none') {
                    positionSelect.value = '';
                }
            }
        }

        // Initialize position filtering on page load
        document.addEventListener('DOMContentLoaded', function() {
            updatePositions();
        });

        // Close modal when clicking outside
        document.getElementById('rejectModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideRejectModal();
            }
        });
    </script>
</x-app-layout>
