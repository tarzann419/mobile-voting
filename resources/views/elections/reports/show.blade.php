<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Election Report: ') . $election->title }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('elections.show', $election) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Election
                </a>
                <button onclick="window.print()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Print Report
                </button>
                <button onclick="exportToPDF()" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Export PDF
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Election Summary -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-xl font-bold mb-6">Election Summary</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-semibold mb-3">Basic Information</h4>
                            <div class="space-y-2 text-sm">
                                <div><strong>Title:</strong> {{ $election->title }}</div>
                                <div><strong>Organization:</strong> {{ $election->organization->name }}</div>
                                <div><strong>Status:</strong> 
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ ucfirst($election->status) }}
                                    </span>
                                </div>
                                <div><strong>Description:</strong> {{ $election->description ?? 'N/A' }}</div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="font-semibold mb-3">Timeline</h4>
                            <div class="space-y-2 text-sm">
                                <div><strong>Registration:</strong> {{ $election->registration_start_date->format('M j, Y g:i A') }} - {{ $election->registration_end_date->format('M j, Y g:i A') }}</div>
                                <div><strong>Voting:</strong> {{ $election->voting_start_date->format('M j, Y g:i A') }} - {{ $election->voting_end_date->format('M j, Y g:i A') }}</div>
                                <div><strong>Duration:</strong> {{ $election->voting_start_date->diff($election->voting_end_date)->format('%d days, %h hours') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overall Statistics -->
            @if(isset($statistics))
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-xl font-bold mb-6">Overall Statistics</h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600">{{ $statistics['total_votes'] ?? 0 }}</div>
                            <div class="text-sm text-gray-500">Total Votes Cast</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600">{{ $statistics['unique_voters'] ?? 0 }}</div>
                            <div class="text-sm text-gray-500">Unique Voters</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-purple-600">{{ $statistics['eligible_voters'] ?? 0 }}</div>
                            <div class="text-sm text-gray-500">Eligible Voters</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-orange-600">{{ $statistics['participation_rate'] ?? 0 }}%</div>
                            <div class="text-sm text-gray-500">Participation Rate</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Position Results -->
            @if(isset($results) && count($results) > 0)
                @foreach($results as $positionId => $positionResults)
                    @php
                        $position = $election->positions->firstWhere('id', $positionId);
                    @endphp
                    @if($position)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900 dark:text-gray-100">
                                <h3 class="text-xl font-bold mb-6">{{ $position->title }} - Results</h3>
                                
                                @if(isset($positionResults['candidates']) && count($positionResults['candidates']) > 0)
                                    @php
                                        $totalVotes = collect($positionResults['candidates'])->sum('votes');
                                        $sortedCandidates = collect($positionResults['candidates'])->sortByDesc('votes');
                                    @endphp
                                    
                                    <div class="mb-4">
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            Total Votes for this Position: <strong>{{ $totalVotes }}</strong>
                                        </div>
                                    </div>

                                    <!-- Winner Announcement -->
                                    @if($totalVotes > 0)
                                        @php $winner = $sortedCandidates->first(); @endphp
                                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                                            <div class="flex items-center">
                                                <svg class="w-6 h-6 text-yellow-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                                <div>
                                                    <h4 class="font-bold text-yellow-800 dark:text-yellow-200">Winner: {{ $winner['name'] }}</h4>
                                                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                                        {{ $winner['votes'] }} votes ({{ $totalVotes > 0 ? round(($winner['votes'] / $totalVotes) * 100, 1) : 0 }}%)
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Detailed Results Table -->
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead class="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rank</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Candidate</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Votes</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Percentage</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Visual</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                @foreach($sortedCandidates as $index => $candidate)
                                                    @php
                                                        $percentage = $totalVotes > 0 ? round(($candidate['votes'] / $totalVotes) * 100, 1) : 0;
                                                        $rank = $index + 1;
                                                    @endphp
                                                    <tr class="{{ $index === 0 && $candidate['votes'] > 0 ? 'bg-yellow-50 dark:bg-yellow-900/20' : '' }}">
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="flex items-center">
                                                                @if($index === 0 && $candidate['votes'] > 0)
                                                                    <svg class="w-5 h-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                                    </svg>
                                                                @endif
                                                                <span class="text-sm font-medium">{{ $rank }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $candidate['name'] }}</div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $candidate['votes'] }}</div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $percentage }}%</div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-gray-500">No votes were cast for this position.</p>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif

            <!-- Report Footer -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="text-center text-sm text-gray-500">
                        <p>This report was generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
                        <p class="mt-1">{{ config('app.name') }} - Digital Voting System</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .py-12 {
                padding-top: 1rem !important;
                padding-bottom: 1rem !important;
            }
            .bg-white {
                background-color: white !important;
            }
            .text-gray-900 {
                color: black !important;
            }
        }
    </style>

    <script>
        function exportToPDF() {
            // Simple implementation - in production, you might want to use a proper PDF library
            window.print();
        }
    </script>
</x-app-layout>
