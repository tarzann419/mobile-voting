<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Voting Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Real-time Results -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Live Election Results</h3>
                    <div id="live-results">
                        <div class="text-center py-4">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                            <p class="text-gray-600 mt-2">Loading results...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Voting Status -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Your Voting Status</h3>
                    <div id="voting-status">
                        <div class="text-center py-4">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                            <p class="text-gray-600 mt-2">Checking status...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <a href="{{ route('voting.ballot', $electionId) }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-lg text-center transition-colors">
                            <svg class="w-8 h-8 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <div class="font-medium">Cast Your Vote</div>
                            <div class="text-sm opacity-75">Go to voting ballot</div>
                        </a>

                        <button onclick="refreshResults()" 
                                class="bg-green-600 hover:bg-green-700 text-white p-4 rounded-lg text-center transition-colors">
                            <svg class="w-8 h-8 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <div class="font-medium">Refresh Results</div>
                            <div class="text-sm opacity-75">Update live data</div>
                        </button>

                        <a href="{{ route('dashboard') }}" 
                           class="bg-gray-600 hover:bg-gray-700 text-white p-4 rounded-lg text-center transition-colors">
                            <svg class="w-8 h-8 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <div class="font-medium">Main Dashboard</div>
                            <div class="text-sm opacity-75">Return to home</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const electionId = {{ $electionId }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Load initial data
        document.addEventListener('DOMContentLoaded', function() {
            loadVotingStatus();
            loadResults();
            
            // Auto-refresh every 30 seconds
            setInterval(loadResults, 30000);
        });

        async function loadVotingStatus() {
            try {
                const response = await fetch(`/elections/${electionId}/ballot`);
                const text = await response.text();
                
                // Simple check if user can access ballot
                if (response.ok && !text.includes('Unable to Access Voting')) {
                    document.getElementById('voting-status').innerHTML = `
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-green-800 font-medium">You are eligible to vote</span>
                            </div>
                        </div>
                    `;
                } else {
                    document.getElementById('voting-status').innerHTML = `
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-yellow-800 font-medium">Voting access restricted</span>
                            </div>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading voting status:', error);
                document.getElementById('voting-status').innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-red-800 font-medium">Error checking voting status</span>
                        </div>
                    </div>
                `;
            }
        }

        async function loadResults() {
            try {
                const response = await fetch(`/voting/${electionId}/results`);
                const data = await response.json();
                
                if (Array.isArray(data) && data.length > 0) {
                    const resultsByPosition = {};
                    data.forEach(result => {
                        if (!resultsByPosition[result.position_title]) {
                            resultsByPosition[result.position_title] = [];
                        }
                        resultsByPosition[result.position_title].push(result);
                    });

                    let html = '';
                    for (const [positionTitle, candidates] of Object.entries(resultsByPosition)) {
                        html += `
                            <div class="mb-6">
                                <h4 class="font-semibold text-lg mb-3">${positionTitle}</h4>
                                <div class="space-y-3">
                        `;
                        
                        candidates.forEach(candidate => {
                            if (candidate.candidate_name) {
                                html += `
                                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-3 h-3 bg-blue-600 rounded-full"></div>
                                            <span class="font-medium">${candidate.candidate_name}</span>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <span class="text-sm text-gray-600">${candidate.vote_count || 0} votes</span>
                                            <span class="text-sm font-medium">${candidate.percentage || 0}%</span>
                                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: ${candidate.percentage || 0}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }
                        });
                        
                        html += `
                                </div>
                            </div>
                        `;
                    }
                    
                    document.getElementById('live-results').innerHTML = html;
                } else {
                    document.getElementById('live-results').innerHTML = `
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-2">
                                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900">No votes yet</h3>
                            <p class="text-sm text-gray-500">Results will appear here as votes are cast.</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading results:', error);
                document.getElementById('live-results').innerHTML = `
                    <div class="text-center py-8">
                        <div class="text-red-400 mb-2">
                            <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900">Error loading results</h3>
                        <p class="text-sm text-gray-500">Please try refreshing the page.</p>
                    </div>
                `;
            }
        }

        function refreshResults() {
            document.getElementById('live-results').innerHTML = `
                <div class="text-center py-4">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                    <p class="text-gray-600 mt-2">Refreshing results...</p>
                </div>
            `;
            loadResults();
        }

        // Listen for vote events
        window.addEventListener('vote-cast', function() {
            setTimeout(loadResults, 1000); // Refresh results after vote
        });
    </script>
</x-app-layout>
