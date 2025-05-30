<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $election->title }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('elections.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Elections
                </a>
                @if($election->status === 'draft')
                    <a href="{{ route('elections.edit', $election) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Edit Election
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Election Details -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium mb-4">Election Information</h3>
                            <div class="space-y-3">
                                <div>
                                    <span class="font-semibold">Status:</span>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ml-2
                                        {{ $election->status === 'draft' ? 'bg-gray-100 text-gray-800' : 
                                           ($election->status === 'active' ? 'bg-green-100 text-green-800' : 
                                            'bg-blue-100 text-blue-800') }}">
                                        {{ ucfirst($election->status) }}
                                    </span>
                                </div>
                                <div>
                                    <span class="font-semibold">Description:</span>
                                    <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $election->description ?? 'No description provided' }}</p>
                                </div>
                                <div>
                                    <span class="font-semibold">Allow Multiple Votes:</span>
                                    <span class="ml-2">{{ $election->allow_multiple_votes ? 'Yes' : 'No' }}</span>
                                </div>
                                <div>
                                    <span class="font-semibold">Require Payment:</span>
                                    <span class="ml-2">{{ $election->require_payment ? 'Yes' : 'No' }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium mb-4">Important Dates</h3>
                            <div class="space-y-3">
                                <div>
                                    <span class="font-semibold">Registration Period:</span>
                                    <p class="text-sm">{{ $election->registration_start_date->format('M j, Y g:i A') }} - {{ $election->registration_end_date->format('M j, Y g:i A') }}</p>
                                </div>
                                <div>
                                    <span class="font-semibold">Voting Period:</span>
                                    <p class="text-sm">{{ $election->voting_start_date->format('M j, Y g:i A') }} - {{ $election->voting_end_date->format('M j, Y g:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Positions -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Positions</h3>
                        @if($election->status === 'draft')
                            <a href="{{ route('positions.index') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                                Manage Positions
                            </a>
                        @endif
                    </div>

                    @if($election->positions && $election->positions->count() > 0)
                        <div class="grid gap-4">
                            @foreach($election->positions->sortBy('order') as $position)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-semibold">{{ $position->title }}</h4>
                                            @if($position->description)
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $position->description }}</p>
                                            @endif
                                            <p class="text-xs text-gray-500 mt-2">
                                                Max Candidates: {{ $position->max_candidates }} | 
                                                Current Candidates: {{ $position->candidates->count() }}
                                            </p>
                                        </div>
                                        <span class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                            Order: {{ $position->order }}
                                        </span>
                                    </div>

                                    @if($position->candidates && $position->candidates->count() > 0)
                                        <div class="mt-3">
                                            <h5 class="text-sm font-medium mb-2">Candidates:</h5>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                                @foreach($position->candidates as $candidate)
                                                    <div class="text-xs bg-blue-50 dark:bg-blue-900/20 px-2 py-1 rounded">
                                                        {{ $candidate->user->name }}
                                                        <span class="text-gray-500">({{ ucfirst($candidate->status) }})</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500 mt-3">No candidates registered yet</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No positions have been created for this election yet.</p>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        @if($election->status === 'completed')
                            <a href="{{ route('elections.results', $election) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded text-center">
                                View Results
                            </a>
                            <a href="{{ route('elections.reports', $election) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-4 rounded text-center">
                                Generate Reports
                            </a>
                        @elseif($election->status === 'active')
                            <a href="{{ route('elections.live-results', $election) }}" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-3 px-4 rounded text-center">
                                Live Results
                            </a>
                        @endif
                        
                        <a href="{{ route('candidates.index') }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded text-center">
                            Manage Candidates
                        </a>
                        
                        <a href="{{ route('voter-accreditation.index') }}" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-3 px-4 rounded text-center">
                            Voter Accreditation
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
