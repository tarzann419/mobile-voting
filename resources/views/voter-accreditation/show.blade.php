<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Voter Accreditation Details') }}
            </h2>
            <a href="{{ route('voter-accreditation.index') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Status Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 dark:bg-green-800 dark:border-green-600 dark:text-green-200">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 dark:bg-red-800 dark:border-red-600 dark:text-red-200">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Application Overview -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Application Overview</h3>
                        <div class="flex items-center space-x-2">
                            @if($accreditation->status === 'pending')
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200">
                                    Pending Review
                                </span>
                            @elseif($accreditation->status === 'approved')
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200">
                                    Approved
                                </span>
                            @elseif($accreditation->status === 'rejected')
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200">
                                    Rejected
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Applicant Information -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-3">Applicant Information</h4>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $accreditation->user->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $accreditation->user->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Applied On</dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $accreditation->applied_at->format('F j, Y \a\t g:i A') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Election Information -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-3">Election Information</h4>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Election Title</dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $accreditation->election->title }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Voting Period</dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ $accreditation->election->voting_start_date->format('M j, Y') }} - 
                                        {{ $accreditation->election->voting_end_date->format('M j, Y') }}
                                    </dd>
                                </div>
                                @if($accreditation->election->description)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $accreditation->election->description }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Uploaded Documents -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Uploaded Documents</h3>
                    
                    @if($accreditation->documents && count($accreditation->documents) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($accreditation->documents as $index => $document)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <!-- File Icon -->
                                        <div class="flex-shrink-0">
                                            @php
                                                $documentType = $document['type'] ?? '';
                                                $documentName = $document['name'] ?? 'Unknown';
                                                $fileExtension = strtolower(pathinfo($documentName, PATHINFO_EXTENSION));
                                            @endphp
                                            
                                            @if(str_contains($documentType, 'image') || in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            @elseif(str_contains($documentType, 'pdf') || $fileExtension === 'pdf')
                                                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            @else
                                                <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            @endif
                                        </div>
                                        
                                        <!-- File Info -->
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                                {{ $documentName }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ strtoupper($fileExtension ?: 'FILE') }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- View/Download Button -->
                                    <div class="mt-3">
                                        @if(isset($document['path']) && $document['path'])
                                            <a href="{{ Storage::url($document['path']) }}" 
                                               target="_blank"
                                               class="w-full bg-blue-500 hover:bg-blue-700 text-white text-sm font-bold py-2 px-4 rounded text-center block">
                                                View Document
                                            </a>
                                        @else
                                            <span class="w-full bg-gray-400 text-white text-sm font-bold py-2 px-4 rounded text-center block cursor-not-allowed">
                                                Document Unavailable
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No documents uploaded</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This application has no supporting documents.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Review Information -->
            @if($accreditation->reviewed_at)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Review Information</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Reviewed By</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $accreditation->reviewer->name ?? 'Unknown' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Reviewed On</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $accreditation->reviewed_at->format('F j, Y \a\t g:i A') }}</dd>
                            </div>
                            @if($accreditation->verification_notes)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        @if($accreditation->status === 'rejected')
                                            Reason for Rejection
                                        @else
                                            Review Notes
                                        @endif
                                    </dt>
                                    <dd class="text-sm text-gray-900 dark:text-gray-100 mt-1 p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                        {{ $accreditation->verification_notes }}
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            @if($accreditation->status === 'pending')
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Review Actions</h3>
                        <div class="flex space-x-4">
                            <!-- Approve Button -->
                            <form method="POST" action="{{ route('voter-accreditation.approve', $accreditation) }}" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded"
                                        onclick="return confirm('Are you sure you want to approve this voter accreditation application?')">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Approve Application
                                </button>
                            </form>
                            
                            <!-- Reject Button -->
                            <button type="button" 
                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-6 rounded"
                                    onclick="openRejectModal()">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Reject Application
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Reject Accreditation</h3>
                <form method="POST" action="{{ route('voter-accreditation.reject', $accreditation) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="verification_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Reason for Rejection (Required)
                        </label>
                        <textarea name="verification_notes" 
                                  id="verification_notes" 
                                  rows="4" 
                                  class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Please provide a detailed reason for rejection..."
                                  required></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" 
                                onclick="closeRejectModal()" 
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Reject Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openRejectModal() {
            const modal = document.getElementById('rejectModal');
            modal.classList.remove('hidden');
        }

        function closeRejectModal() {
            const modal = document.getElementById('rejectModal');
            modal.classList.add('hidden');
            // Clear the form
            document.getElementById('verification_notes').value = '';
        }

        // Close modal when clicking outside
        document.getElementById('rejectModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRejectModal();
            }
        });
    </script>
</x-app-layout>
