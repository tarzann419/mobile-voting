<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Live Results: ') . $election->title }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('elections.show', $election) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Election
                </a>
                <button onclick="refreshResults()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Refresh Results
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Election Status -->
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800 dark:text-green-200">
                            Election is Currently Active
                        </h3>
                        <p class="text-sm text-green-700 dark:text-green-300">
                            Voting ends: {{ $election->voting_end_date->format('M j, Y g:i A') }}
                            <span class="ml-2 text-xs">
                                ({{ $election->voting_end_date->diffForHumans() }})
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Real-time Results -->
            <div id="results-container">
                @if(isset($results) && count($results) > 0)
                    @foreach($results as $positionId => $positionResults)
                        @php
                            $position = $election->positions->firstWhere('id', $positionId);
                        @endphp
                        @if($position)
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                                <div class="p-6 text-gray-900 dark:text-gray-100">
                                    <h3 class="text-lg font-medium mb-4">{{ $position->title }}</h3>
                                    
                                    @if(isset($positionResults['candidates']) && count($positionResults['candidates']) > 0)
                                        <div class="space-y-4">
                                            @php
                                                $totalVotes = collect($positionResults['candidates'])->sum('votes');
                                                $sortedCandidates = collect($positionResults['candidates'])->sortByDesc('votes');
                                            @endphp
                                            
                                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                                Total Votes Cast: <strong>{{ $totalVotes }}</strong>
                                            </div>

                                            @foreach($sortedCandidates as $index => $candidate)
                                                @php
                                                    $percentage = $totalVotes > 0 ? round(($candidate['votes'] / $totalVotes) * 100, 1) : 0;
                                                    $isLeading = $index === 0 && $candidate['votes'] > 0;
                                                @endphp
                                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 {{ $isLeading ? 'ring-2 ring-yellow-400' : '' }}">
                                                    <div class="flex justify-between items-center mb-2">
                                                        <div class="flex items-center">
                                                            @if($isLeading)
                                                                <svg class="w-5 h-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                                </svg>
                                                            @endif
                                                            <h4 class="font-semibold">{{ $candidate['name'] }}</h4>
                                                            @if($isLeading)
                                                                <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">Leading</span>
                                                            @endif
                                                        </div>
                                                        <div class="text-right">
                                                            <div class="font-bold text-lg">{{ $candidate['votes'] }}</div>
                                                            <div class="text-sm text-gray-500">{{ $percentage }}%</div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Progress Bar -->
                                                    <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                                        <div class="bg-blue-500 h-2 rounded-full transition-all duration-500" 
                                                             style="width: {{ $percentage }}%"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-gray-500">No votes cast yet for this position.</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No votes yet</h3>
                                <p class="mt-1 text-sm text-gray-500">Voting results will appear here as votes are cast.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Voting Progress -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Voting Statistics</h3>
                    
                    @php
                        $totalVotersInOrg = $election->organization->users()->where('role', 'voter')->count();
                        $totalVotesCast = isset($results) ? collect($results)->sum(function($positionResults) {
                            return isset($positionResults['candidates']) ? collect($positionResults['candidates'])->sum('votes') : 0;
                        }) : 0;
                        $uniqueVoters = $totalVotesCast; // This could be more accurate with actual voter tracking
                        $participationRate = $totalVotersInOrg > 0 ? round(($uniqueVoters / $totalVotersInOrg) * 100, 1) : 0;
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $totalVotesCast }}</div>
                            <div class="text-sm text-gray-500">Total Votes Cast</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $totalVotersInOrg }}</div>
                            <div class="text-sm text-gray-500">Eligible Voters</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ $participationRate }}%</div>
                            <div class="text-sm text-gray-500">Participation Rate</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh every 30 seconds
        let autoRefresh = setInterval(refreshResults, 30000);
        
        function refreshResults() {
            // Show loading indicator
            const refreshBtn = document.querySelector('button[onclick="refreshResults()"]');
            const originalText = refreshBtn.textContent;
            refreshBtn.textContent = 'Refreshing...';
            refreshBtn.disabled = true;
            
            // Reload the page to get fresh data
            window.location.reload();
        }
        
        // Stop auto-refresh when user leaves the page
        window.addEventListener('beforeunload', function() {
            clearInterval(autoRefresh);
        });
        
        // Display last update time
        const lastUpdateElement = document.createElement('div');
        lastUpdateElement.className = 'text-xs text-gray-500 text-center mt-4';
        lastUpdateElement.textContent = `Last updated: ${new Date().toLocaleTimeString()}`;
        document.getElementById('results-container').appendChild(lastUpdateElement);
    </script>
</x-app-layout>
