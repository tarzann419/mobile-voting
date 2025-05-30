<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Voting Ballot') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Election Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    @if(isset($election) && is_object($election))
                        <h3 class="text-lg font-semibold mb-2">{{ $election->title }}</h3>
                        <p class="text-gray-600 mb-4">{{ $election->description }}</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium">Voting Period:</span>
                                {{ $election->voting_start_date?->format('M d, Y g:i A') }} - 
                                {{ $election->voting_end_date?->format('M d, Y g:i A') }}
                            </div>
                            <div>
                                <span class="font-medium">Status:</span>
                                <span class="px-2 py-1 rounded-full text-xs 
                                    @if($election->status === 'active') bg-green-100 text-green-800
                                    @elseif($election->status === 'published') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($election->status) }}
                                </span>
                            </div>
                        </div>
                    @else
                        <h3 class="text-lg font-semibold mb-2">Election Ballot</h3>
                        <p class="text-gray-600">Cast your votes for the available positions.</p>
                    @endif
                </div>
            </div>

            <!-- Voting History -->
            @if(!empty($votingHistory))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-green-800 mb-2">Your Voting History</h4>
                    <div class="space-y-2">
                        @foreach($votingHistory as $vote)
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-green-700">
                                    {{ $vote['position']['title'] ?? $vote['position'] ?? 'Position' }}: 
                                    {{ $vote['candidate']['user']['name'] ?? $vote['candidate'] ?? 'Vote Cast' }}
                                </span>
                                <span class="text-green-600">
                                    {{ \Carbon\Carbon::parse($vote['voted_at'])->format('M d, g:i A') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Available Positions -->
            @if(!empty($availablePositions))
                <div class="space-y-6">
                    @foreach($availablePositions as $positionData)
                        @php
                            $position = $positionData['position'];
                            $candidates = $positionData['candidates'];
                            $hasVoted = $positionData['has_voted'] ?? false;
                        @endphp
                        
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900">
                                <div class="mb-4">
                                    <h3 class="text-lg font-semibold flex items-center">
                                        {{ $position->title }}
                                        @if($hasVoted)
                                            <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                                ✓ Voted
                                            </span>
                                        @endif
                                    </h3>
                                    <p class="text-gray-600 mt-1">{{ $position->description }}</p>
                                </div>

                                @if(!empty($candidates))
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($candidates as $candidate)
                                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                                <div class="flex items-start space-x-3">
                                                    <div class="flex-shrink-0">
                                                        @if($candidate->photo)
                                                            <img src="{{ $candidate->photo }}" alt="{{ $candidate->user->name }}" 
                                                                 class="w-12 h-12 rounded-full object-cover">
                                                        @else
                                                            <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                                                                <span class="text-gray-600 font-medium">
                                                                    {{ substr($candidate->user->name, 0, 1) }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <h4 class="font-medium text-gray-900 truncate">
                                                            {{ $candidate->user->name }}
                                                        </h4>
                                                        @if($candidate->user->email)
                                                            <p class="text-sm text-gray-500 truncate">
                                                                {{ $candidate->user->email }}
                                                            </p>
                                                        @endif
                                                        @if($candidate->manifesto)
                                                            <p class="text-sm text-gray-600 mt-2 line-clamp-3">
                                                                {{ $candidate->manifesto }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <div class="mt-4">
                                                    @if(!$hasVoted)
                                                        <button type="button" 
                                                                onclick="castVote({{ $position->id }}, {{ $candidate->id }})"
                                                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                                            Vote for {{ $candidate->user->name }}
                                                        </button>
                                                    @else
                                                        <button type="button" disabled
                                                                class="w-full bg-gray-300 text-gray-500 font-medium py-2 px-4 rounded-lg cursor-not-allowed">
                                                            Already Voted
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <div class="text-gray-400 mb-2">
                                            <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-sm font-medium text-gray-900">No candidates available</h3>
                                        <p class="text-sm text-gray-500">There are no approved candidates for this position yet.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-4">
                                <svg class="mx-auto h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">All votes cast!</h3>
                            <p class="text-gray-500">You have voted for all available positions in this election.</p>
                            <div class="mt-6">
                                <a href="{{ route('dashboard') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                    Return to Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div id="success-message" class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg z-50">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div id="error-message" class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-lg z-50">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Voting Script -->
    <script>
        // Auto-hide messages after 5 seconds
        setTimeout(() => {
            const successMsg = document.getElementById('success-message');
            const errorMsg = document.getElementById('error-message');
            if (successMsg) successMsg.style.display = 'none';
            if (errorMsg) errorMsg.style.display = 'none';
        }, 5000);

        // CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        async function castVote(positionId, candidateId) {
            const button = event.target;
            const originalText = button.textContent;
            
            // Disable button and show loading state
            button.disabled = true;
            button.textContent = 'Casting Vote...';
            button.classList.add('opacity-50');

            try {
                const response = await fetch(`/elections/{{ $electionId ?? $election->id ?? 'ID' }}/vote`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        position_id: positionId,
                        candidate_id: candidateId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Show success message
                    showMessage(data.message + (data.vote_hash ? ` Vote Hash: ${data.vote_hash}` : ''), 'success');
                    
                    // Reload page to update the ballot
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showMessage(data.message || 'Failed to cast vote', 'error');
                    // Re-enable button
                    button.disabled = false;
                    button.textContent = originalText;
                    button.classList.remove('opacity-50');
                }
            } catch (error) {
                console.error('Error casting vote:', error);
                showMessage('An error occurred while casting your vote. Please try again.', 'error');
                
                // Re-enable button
                button.disabled = false;
                button.textContent = originalText;
                button.classList.remove('opacity-50');
            }
        }

        function showMessage(message, type) {
            // Remove existing messages
            const existingMessages = document.querySelectorAll('#success-message, #error-message');
            existingMessages.forEach(msg => msg.remove());

            // Create new message
            const messageDiv = document.createElement('div');
            messageDiv.id = type + '-message';
            messageDiv.className = `fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 ${
                type === 'success' 
                    ? 'bg-green-100 border border-green-400 text-green-700' 
                    : 'bg-red-100 border border-red-400 text-red-700'
            }`;
            
            messageDiv.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        ${type === 'success' 
                            ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>'
                            : '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>'
                        }
                    </svg>
                    ${message}
                </div>
            `;
            
            document.body.appendChild(messageDiv);
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                messageDiv.remove();
            }, 5000);
        }
    </script>
</x-app-layout>
