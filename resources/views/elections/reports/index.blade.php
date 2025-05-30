<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Election Reports & Analytics') }}
            </h2>
            <a href="{{ route('elections.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Elections
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-6">Election Reports & Analytics</h3>
                    
                    @if($elections->count() > 0)
                        <div class="grid gap-6">
                            @foreach($elections as $election)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $election->title }}
                                            </h4>
                                            <p class="text-gray-600 dark:text-gray-400 mt-1">
                                                {{ $election->description }}
                                            </p>
                                            <div class="flex items-center mt-2 space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4M8 7l4 7 4-7M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3"></path>
                                                    </svg>
                                                    {{ $election->voting_start_date->format('M j') }} - {{ $election->voting_end_date->format('M j, Y') }}
                                                </div>
                                                <span class="px-2 py-1 text-xs rounded-full
                                                    @if($election->status === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                    @elseif($election->status === 'completed') bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                                    @else bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                    @endif">
                                                    {{ ucfirst($election->status) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            @if($election->status === 'completed' || $election->status === 'active')
                                                <a href="{{ route('elections.reports', $election) }}" 
                                                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    Generate Report
                                                </a>
                                            @endif
                                            <a href="{{ route('elections.show', $election) }}" 
                                               class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                                View Election
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Election Statistics -->
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                        @php
                                            $totalPositions = $election->positions->count();
                                            $totalCandidates = $election->positions->sum(function($position) {
                                                return $position->candidates->count();
                                            });
                                            $totalVotes = $election->positions->sum(function($position) {
                                                return $position->candidates->sum(function($candidate) {
                                                    return $candidate->votes->count();
                                                });
                                            });
                                            $avgVotesPerPosition = $totalPositions > 0 ? round($totalVotes / $totalPositions, 1) : 0;
                                        @endphp
                                        
                                        <div class="bg-blue-50 dark:bg-blue-900 p-3 rounded-lg">
                                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-300">{{ $totalPositions }}</div>
                                            <div class="text-sm text-blue-700 dark:text-blue-400">Positions</div>
                                        </div>
                                        
                                        <div class="bg-green-50 dark:bg-green-900 p-3 rounded-lg">
                                            <div class="text-2xl font-bold text-green-600 dark:text-green-300">{{ $totalCandidates }}</div>
                                            <div class="text-sm text-green-700 dark:text-green-400">Candidates</div>
                                        </div>
                                        
                                        <div class="bg-purple-50 dark:bg-purple-900 p-3 rounded-lg">
                                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-300">{{ $totalVotes }}</div>
                                            <div class="text-sm text-purple-700 dark:text-purple-400">Total Votes</div>
                                        </div>
                                        
                                        <div class="bg-yellow-50 dark:bg-yellow-900 p-3 rounded-lg">
                                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-300">{{ $avgVotesPerPosition }}</div>
                                            <div class="text-sm text-yellow-700 dark:text-yellow-400">Avg/Position</div>
                                        </div>
                                    </div>

                                    <!-- Position Overview -->
                                    @if($election->positions->count() > 0)
                                        <div class="space-y-3">
                                            <h5 class="font-medium text-gray-900 dark:text-gray-100">Position Overview</h5>
                                            <div class="grid md:grid-cols-2 gap-4">
                                                @foreach($election->positions as $position)
                                                    @php
                                                        $candidateCount = $position->candidates->count();
                                                        $voteCount = $position->candidates->sum(function($candidate) {
                                                            return $candidate->votes->count();
                                                        });
                                                    @endphp
                                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                                        <div class="flex justify-between items-start">
                                                            <div>
                                                                <h6 class="font-medium text-gray-900 dark:text-gray-100">{{ $position->title }}</h6>
                                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                                    {{ $candidateCount }} candidates • {{ $voteCount }} votes
                                                                </p>
                                                            </div>
                                                            <div class="text-right">
                                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                                    Max: {{ $position->max_candidates }}
                                                                </div>
                                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                                    Order: {{ $position->order }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-gray-500 dark:text-gray-400">No positions configured for this election.</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="mt-2 text-gray-500 dark:text-gray-400">No elections available for reporting.</p>
                            <p class="text-sm text-gray-400 dark:text-gray-500">Create elections to generate reports and analytics.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
