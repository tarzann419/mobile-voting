<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Voting Access Restricted') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="text-center py-8">
                        <!-- Warning Icon -->
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-6">
                            <svg class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>

                        <!-- Title -->
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Unable to Access Voting
                        </h3>

                        <!-- Reason -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                            <p class="text-yellow-800 font-medium">
                                {{ $reason ?? 'You are not eligible to vote in this election.' }}
                            </p>
                        </div>

                        <!-- Election Info (if available) -->
                        @if(isset($election) && is_object($election))
                            <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                                <h4 class="font-semibold text-gray-900 mb-2">Election Details</h4>
                                <div class="space-y-2 text-sm text-gray-600">
                                    <div><span class="font-medium">Title:</span> {{ $election->title }}</div>
                                    <div><span class="font-medium">Description:</span> {{ $election->description }}</div>
                                    @if($election->voting_start_date && $election->voting_end_date)
                                        <div>
                                            <span class="font-medium">Voting Period:</span>
                                            {{ $election->voting_start_date->format('M d, Y g:i A') }} - 
                                            {{ $election->voting_end_date->format('M d, Y g:i A') }}
                                        </div>
                                    @endif
                                    <div>
                                        <span class="font-medium">Status:</span>
                                        <span class="px-2 py-1 rounded-full text-xs 
                                            @if($election->status === 'active') bg-green-100 text-green-800
                                            @elseif($election->status === 'published') bg-blue-100 text-blue-800
                                            @elseif($election->status === 'completed') bg-gray-100 text-gray-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ ucfirst($election->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Action Steps -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <h4 class="font-semibold text-blue-900 mb-3">What can you do?</h4>
                            <div class="text-left space-y-3">
                                @php
                                    $reasonLower = strtolower($reason ?? '');
                                @endphp

                                @if(str_contains($reasonLower, 'not accredited') || str_contains($reasonLower, 'accredited'))
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                                        </div>
                                        <div class="text-blue-800">
                                            <strong>Apply for Voter Accreditation:</strong> You need to be accredited before you can vote.
                                            <a href="{{ route('voter-accreditation.create') }}" class="block mt-1 text-blue-600 hover:text-blue-800 underline">
                                                → Apply for accreditation here
                                            </a>
                                        </div>
                                    </div>
                                @endif

                                @if(str_contains($reasonLower, 'not active') || str_contains($reasonLower, 'voting is not'))
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                                        </div>
                                        <div class="text-blue-800">
                                            <strong>Wait for Voting Period:</strong> Voting is not currently active for this election.
                                        </div>
                                    </div>
                                @endif

                                @if(str_contains($reasonLower, 'already voted'))
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                                        </div>
                                        <div class="text-blue-800">
                                            <strong>You've Already Voted:</strong> Your vote has been recorded successfully.
                                            <a href="{{ route('elections.show', $electionId ?? ($election->id ?? '')) }}" class="block mt-1 text-blue-600 hover:text-blue-800 underline">
                                                → View election results
                                            </a>
                                        </div>
                                    </div>
                                @endif

                                @if(str_contains($reasonLower, 'not found'))
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                                        </div>
                                        <div class="text-blue-800">
                                            <strong>Election Not Found:</strong> The election you're trying to access may have been removed or doesn't exist.
                                        </div>
                                    </div>
                                @endif

                                <!-- General help option -->
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                                    </div>
                                    <div class="text-blue-800">
                                        <strong>Contact Support:</strong> If you believe this is an error, contact your organization administrator.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <a href="{{ route('dashboard') }}" 
                               class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Return to Dashboard
                            </a>

                            @if(!str_contains($reasonLower, 'already voted'))
                                <a href="{{ route('voter-accreditation.create') }}" 
                                   class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Apply for Accreditation
                                </a>
                            @endif
                        </div>

                        <!-- Additional Information -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <p class="text-sm text-gray-500">
                                If you need assistance, please contact your organization administrator or support team.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
