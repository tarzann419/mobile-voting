<div class="container mx-auto px-4 py-6">
    @if ($election)
        <!-- Election Header -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-4">
                <h1 class="text-2xl font-bold">{{ $election->name }} - Live Results</h1>
                <div class="flex items-center space-x-6 mt-2 text-sm">
                    <span class="px-3 py-1 bg-white bg-opacity-20 rounded">
                        {{ ucfirst($election->status) }}
                    </span>
                    <span>Total Votes: {{ $totalVotes }}</span>
                    <span>Eligible Voters: {{ $eligibleVoters }}</span>
                    <span>Turnout: {{ number_format($turnoutPercentage, 1) }}%</span>
                </div>
            </div>
        </div>

        <!-- Auto-refresh Controls -->
        <div class="bg-white shadow-lg rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               wire:model="autoRefresh" 
                               class="mr-2">
                        Auto-refresh every {{ $refreshInterval }} seconds
                    </label>
                    
                    @if ($autoRefresh)
                        <div class="text-sm text-gray-500">
                            Next update in: <span id="countdown">{{ $refreshInterval }}</span>s
                        </div>
                    @endif
                </div>
                
                <div class="flex items-center space-x-2">
                    <button wire:click="refreshResults" 
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Refresh Now
                    </button>
                    
                    @if ($election->status === 'completed')
                        <button wire:click="exportResults" 
                                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            Export Results
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Results by Position -->
        @if ($results && count($results) > 0)
            <div class="space-y-6">
                @foreach ($results as $positionResult)
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                        <!-- Position Header -->
                        <div class="bg-gray-50 px-6 py-4 border-b">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">{{ $positionResult['position_name'] }}</h2>
                                    <p class="text-gray-600">{{ $positionResult['total_votes'] }} total votes</p>
                                </div>
                                
                                @if ($positionResult['winner'] && $election->status === 'completed')
                                    <div class="text-right">
                                        <div class="flex items-center text-green-600">
                                            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Winner: {{ $positionResult['winner']['name'] }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Candidates Results -->
                        <div class="p-6">
                            @if (count($positionResult['candidates']) > 0)
                                <div class="space-y-4">
                                    @foreach ($positionResult['candidates'] as $candidate)
                                        <div class="flex items-center p-4 border rounded-lg
                                            @if ($candidate['is_winner'] ?? false) border-green-500 bg-green-50 @endif">
                                            
                                            <!-- Candidate Info -->
                                            <div class="flex items-center flex-1">
                                                <img src="{{ $candidate['photo'] ?? '/default-avatar.png' }}" 
                                                     alt="{{ $candidate['name'] }}"
                                                     class="w-16 h-16 rounded-full mr-4">
                                                
                                                <div class="flex-1">
                                                    <div class="flex items-center">
                                                        <h3 class="text-lg font-semibold">{{ $candidate['name'] }}</h3>
                                                        @if ($candidate['is_winner'] ?? false)
                                                            <span class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                                </svg>
                                                                Winner
                                                            </span>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="flex items-center space-x-4 mt-2">
                                                        <span class="text-2xl font-bold text-blue-600">{{ $candidate['votes'] }}</span>
                                                        <span class="text-gray-500">votes</span>
                                                        <span class="text-lg font-semibold text-gray-700">
                                                            {{ number_format($candidate['percentage'], 1) }}%
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Progress Bar -->
                                            <div class="w-1/3 ml-4">
                                                <div class="w-full bg-gray-200 rounded-full h-4">
                                                    <div class="h-4 rounded-full transition-all duration-500 ease-out
                                                        @if ($candidate['is_winner'] ?? false) bg-green-500 
                                                        @else bg-blue-500 @endif" 
                                                        style="width: {{ $candidate['percentage'] }}%">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <p class="text-gray-500">No candidates for this position</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white shadow-lg rounded-lg p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No results available</h3>
                <p class="mt-1 text-sm text-gray-500">Results will appear here as votes are cast.</p>
            </div>
        @endif

        <!-- Voting Timeline -->
        @if ($votingTimeline && count($votingTimeline) > 0)
            <div class="bg-white shadow-lg rounded-lg overflow-hidden mt-6">
                <div class="bg-gray-50 px-6 py-4 border-b">
                    <h2 class="text-xl font-bold text-gray-900">Voting Timeline</h2>
                </div>
                
                <div class="p-6">
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach ($votingTimeline as $timeEntry)
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">
                                    {{ $timeEntry['time']->format('H:i:s') }}
                                </span>
                                <span class="text-sm font-medium">
                                    {{ $timeEntry['votes'] }} votes cast
                                </span>
                                <span class="text-sm text-gray-500">
                                    Total: {{ $timeEntry['cumulative'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="bg-white shadow-lg rounded-lg p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No election selected</h3>
            <p class="mt-1 text-sm text-gray-500">Please select an election to view results.</p>
        </div>
    @endif
</div>

@if ($autoRefresh)
<script>
    let countdown = {{ $refreshInterval }};
    const countdownElement = document.getElementById('countdown');
    
    const timer = setInterval(function() {
        countdown--;
        if (countdownElement) {
            countdownElement.textContent = countdown;
        }
        
        if (countdown <= 0) {
            @this.call('refreshResults');
            countdown = {{ $refreshInterval }};
        }
    }, 1000);
    
    // Clear timer when component is destroyed
    document.addEventListener('livewire:load', function () {
        window.addEventListener('beforeunload', function() {
            clearInterval(timer);
        });
    });
</script>
@endif
