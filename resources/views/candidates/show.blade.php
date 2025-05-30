<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Candidate Details') }}
            </h2>
            <a href="{{ route('candidates.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Candidates
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Status Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Candidate Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-start space-x-6">
                        <!-- Profile Photo -->
                        <div class="flex-shrink-0">
                            @if($candidate->profile_photo_path)
                                <img class="h-32 w-32 rounded-full object-cover" src="{{ Storage::url($candidate->profile_photo_path) }}" alt="{{ $candidate->user->name }}">
                            @else
                                <div class="h-32 w-32 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-3xl font-medium text-gray-700">{{ substr($candidate->user->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Candidate Info -->
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">{{ $candidate->user->name }}</h1>
                                    <p class="text-gray-600">{{ $candidate->user->email }}</p>
                                    <p class="text-lg text-gray-800 mt-2">Running for: <span class="font-semibold">{{ $candidate->position->title }}</span></p>
                                    <p class="text-gray-600">{{ $candidate->position->election->title }}</p>
                                </div>

                                <!-- Status Badge -->
                                <div>
                                    @if($candidate->status === 'pending')
                                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800">
                                            Pending Approval
                                        </span>
                                    @elseif($candidate->status === 'approved')
                                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800">
                                            Approved
                                        </span>
                                    @elseif($candidate->status === 'rejected')
                                        <span class="px-3 py-1 text-sm font-medium rounded-full bg-red-100 text-red-800">
                                            Rejected
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Quick Stats -->
                            <div class="mt-4 grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Application Date</p>
                                    <p class="font-medium">{{ $candidate->created_at->format('F j, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Election Status</p>
                                    <p class="font-medium capitalize">{{ $candidate->position->election->status }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    @if($candidate->status === 'pending' && auth()->user()->role === 'organization_admin')
                        <div class="mt-6 flex space-x-4">
                            <form action="{{ route('candidates.approve', $candidate->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to approve this candidate?')">
                                    Approve Candidate
                                </button>
                            </form>
                            <button type="button" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="showRejectModal()">
                                Reject Candidate
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Candidate Bio -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Biography</h3>
                    <div class="prose max-w-none">
                        {!! nl2br(e($candidate->bio)) !!}
                    </div>
                </div>
            </div>

            <!-- Candidate Manifesto -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Manifesto</h3>
                    <div class="prose max-w-none">
                        {!! nl2br(e($candidate->manifesto)) !!}
                    </div>
                </div>
            </div>

            <!-- Position Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Position Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-900">Position</h4>
                            <p class="text-gray-600">{{ $candidate->position->title }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ $candidate->position->description }}</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Election</h4>
                            <p class="text-gray-600">{{ $candidate->position->election->title }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ $candidate->position->election->description }}</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Voting Period</h4>
                            <p class="text-gray-600">
                                {{ $candidate->position->election->voting_start_date->format('M j, Y') }} - 
                                {{ $candidate->position->election->voting_end_date->format('M j, Y') }}
                            </p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Registration Period</h4>
                            <p class="text-gray-600">
                                {{ $candidate->position->election->registration_start_date->format('M j, Y') }} - 
                                {{ $candidate->position->election->registration_end_date->format('M j, Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rejection Reason (if rejected) -->
            @if($candidate->status === 'rejected' && $candidate->rejection_reason)
                <div class="bg-red-50 border border-red-200 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-red-800 mb-4">Rejection Reason</h3>
                        <p class="text-red-700">{{ $candidate->rejection_reason }}</p>
                        @if($candidate->rejected_at)
                            <p class="text-sm text-red-600 mt-2">Rejected on {{ $candidate->rejected_at->format('F j, Y \a\t g:i A') }}</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Approval Details (if approved) -->
            @if($candidate->status === 'approved' && $candidate->approved_at)
                <div class="bg-green-50 border border-green-200 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-green-800 mb-4">Approval Details</h3>
                        <p class="text-green-700">This candidate has been approved and can participate in the election.</p>
                        <p class="text-sm text-green-600 mt-2">Approved on {{ $candidate->approved_at->format('F j, Y \a\t g:i A') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Candidate</h3>
                <form action="{{ route('candidates.reject', $candidate->id) }}" method="POST">
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
        function showRejectModal() {
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function hideRejectModal() {
            const modal = document.getElementById('rejectModal');
            const textarea = document.getElementById('reason');
            modal.classList.add('hidden');
            textarea.value = '';
        }

        // Close modal when clicking outside
        document.getElementById('rejectModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideRejectModal();
            }
        });
    </script>
</x-app-layout>
