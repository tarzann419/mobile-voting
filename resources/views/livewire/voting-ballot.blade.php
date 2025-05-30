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

    @if ($election && $election->status === 'active')
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Election Header -->
            <div class="bg-green-600 text-white px-6 py-4">
                <h1 class="text-2xl font-bold">{{ $election->name }}</h1>
                <p class="text-green-100">Cast your vote</p>
                <div class="mt-2 text-sm">
                    <span>Ends: {{ $election->end_time->format('M d, Y H:i') }}</span>
                </div>
            </div>

            @if ($hasVoted)
                <!-- Already Voted Message -->
                <div class="p-6 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Vote Submitted Successfully!</h2>
                    <p class="text-gray-600">Thank you for participating in this election.</p>
                </div>
            @else
                <!-- Voting Form -->
                <form wire:submit.prevent="submitVote">
                    @foreach ($positions as $position)
                        <div class="p-6 border-b">
                            <h3 class="text-xl font-bold mb-4">{{ $position->name }}</h3>
                            <p class="text-gray-600 mb-4">{{ $position->description }}</p>
                            
                            @if ($position->candidates->count() > 0)
                                <div class="space-y-4">
                                    @foreach ($position->candidates as $candidate)
                                        <label class="flex items-center p-4 border rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input type="radio" 
                                                   name="vote_{{ $position->id }}" 
                                                   value="{{ $candidate->id }}"
                                                   wire:model="selectedCandidates.{{ $position->id }}"
                                                   class="mr-4 h-4 w-4 text-green-600">
                                            
                                            <div class="flex items-center flex-1">
                                                <img src="{{ $candidate->photo ?? '/default-avatar.png' }}" 
                                                     alt="{{ $candidate->user->name }}"
                                                     class="w-16 h-16 rounded-full mr-4">
                                                
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-lg">{{ $candidate->user->name }}</h4>
                                                    @if ($candidate->bio)
                                                        <p class="text-gray-600 text-sm mt-1">{{ $candidate->bio }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 italic">No candidates registered for this position.</p>
                            @endif
                        </div>
                    @endforeach

                    <!-- Submit Button -->
                    @if ($positions->count() > 0)
                        <div class="p-6 bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-600">
                                    Please review your selections before submitting.
                                </div>
                                <button type="submit" 
                                        class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-semibold"
                                        @if (empty($selectedCandidates)) disabled @endif>
                                    Submit Vote
                                </button>
                            </div>
                        </div>
                    @endif
                </form>
            @endif
        </div>
    @else
        <!-- Election Not Active -->
        <div class="bg-white shadow-lg rounded-lg p-6 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Election Not Available</h2>
            <p class="text-gray-600">
                @if ($election)
                    @if ($election->status === 'draft')
                        This election has not started yet.
                    @elseif ($election->status === 'completed')
                        This election has ended.
                    @endif
                @else
                    Election not found.
                @endif
            </p>
        </div>
    @endif
</div>
