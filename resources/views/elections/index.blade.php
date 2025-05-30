<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Elections Management') }}
            </h2>
            <a href="{{ route('elections.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create New Election
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($elections->count() > 0)
                        <div class="grid gap-6">
                            @foreach($elections as $election)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $election->title }}
                                            </h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $election->description }}
                                            </p>
                                        </div>
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full 
                                            {{ $election->status === 'draft' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : 
                                               ($election->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 
                                                'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100') }}">
                                            {{ ucfirst($election->status) }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600 dark:text-gray-400 mb-4">
                                        <div>
                                            <strong>Voting Period:</strong><br>
                                            {{ $election->voting_start_date->format('M j, Y H:i') }} - 
                                            {{ $election->voting_end_date->format('M j, Y H:i') }}
                                        </div>
                                        <div>
                                            <strong>Registration:</strong><br>
                                            {{ $election->registration_start_date->format('M j, Y') }} - 
                                            {{ $election->registration_end_date->format('M j, Y') }}
                                        </div>
                                        <div>
                                            <strong>Positions:</strong> {{ $election->positions_count ?? 0 }}<br>
                                            <strong>Candidates:</strong> {{ $election->candidates_count ?? 0 }}
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('elections.show', $election) }}" 
                                           class="px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 transition duration-200">
                                            View Details
                                        </a>
                                        
                                        @if($election->status === 'draft')
                                            <a href="{{ route('elections.edit', $election) }}" 
                                               class="px-3 py-1 bg-yellow-500 text-white text-sm rounded hover:bg-yellow-600 transition duration-200">
                                                Edit
                                            </a>
                                        @endif

                                        @if($election->status === 'completed')
                                            <a href="{{ route('elections.results', $election) }}" 
                                               class="px-3 py-1 bg-green-500 text-white text-sm rounded hover:bg-green-600 transition duration-200">
                                                View Results
                                            </a>
                                        @endif

                                        <a href="{{ route('positions.index', ['election_id' => $election->id]) }}" 
                                           class="px-3 py-1 bg-purple-500 text-white text-sm rounded hover:bg-purple-600 transition duration-200">
                                            Manage Positions
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $elections->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-gray-500 dark:text-gray-400 mb-4">
                                <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No elections found</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">Get started by creating your first election.</p>
                            <a href="{{ route('elections.create') }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 transition duration-200">
                                Create Election
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
