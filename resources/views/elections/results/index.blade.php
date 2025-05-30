<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Election Results') }}
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
                    <h3 class="text-lg font-medium mb-6">Completed Elections with Results</h3>
                    
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
                                            <div class="flex items-center mt-2 text-sm text-gray-500 dark:text-gray-400">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4M8 7l4 7 4-7M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3"></path>
                                                </svg>
                                                Ended: {{ $election->voting_end_date->format('M j, Y g:i A') }}
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('elections.results', $election) }}" 
                                               class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                                View Detailed Results
                                            </a>
                                            <a href="{{ route('elections.show', $election) }}" 
                                               class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                                View Election
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Quick Results Summary -->
                                    @if($election->positions->count() > 0)
                                        <div class="space-y-4">
                                            @foreach($election->positions as $position)
                                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                                    <h5 class="font-medium text-gray-900 dark:text-gray-100 mb-2">
                                                        {{ $position->title }}
                                                    </h5>
                                                    @if($position->candidates->count() > 0)
                                                        <div class="space-y-2">
                                                            @foreach($position->candidates->sortByDesc(function($candidate) { return $candidate->votes->count(); })->take(3) as $candidate)
                                                                @php
                                                                    $voteCount = $candidate->votes->count();
                                                                    $totalVotes = $position->candidates->sum(function($c) { return $c->votes->count(); });
                                                                    $percentage = $totalVotes > 0 ? round(($voteCount / $totalVotes) * 100, 1) : 0;
                                                                @endphp
                                                                <div class="flex items-center justify-between">
                                                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                                                        {{ $candidate->user->name }}
                                                                    </span>
                                                                    <div class="flex items-center space-x-2">
                                                                        <span class="text-sm font-medium">{{ $voteCount }} votes</span>
                                                                        <span class="text-sm text-gray-500">({{ $percentage }}%)</span>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <p class="text-sm text-gray-500 dark:text-gray-400">No candidates</p>
                                                    @endif
                                                </div>
                                            @endforeach
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <p class="mt-2 text-gray-500 dark:text-gray-400">No completed elections with results found.</p>
                            <p class="text-sm text-gray-400 dark:text-gray-500">Results will appear here once elections are completed.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
