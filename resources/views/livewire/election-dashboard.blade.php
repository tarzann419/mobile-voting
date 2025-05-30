<div class="container mx-auto px-4 py-6">
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if ($election)
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Election Header -->
            <div class="bg-blue-600 text-white px-6 py-4">
                <h1 class="text-2xl font-bold">{{ $election->name }}</h1>
                <p class="text-blue-100">{{ $election->description }}</p>
                <div class="flex items-center space-x-4 mt-2 text-sm">
                    <span class="px-2 py-1 bg-blue-500 rounded">{{ ucfirst($election->status) }}</span>
                    <span>Start: {{ $election->start_time->format('M d, Y H:i') }}</span>
                    <span>End: {{ $election->end_time->format('M d, Y H:i') }}</span>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 p-6">
                <div class="bg-gradient-to-r from-green-400 to-green-600 text-white p-4 rounded-lg">
                    <h3 class="text-lg font-semibold">Total Votes</h3>
                    <p class="text-3xl font-bold">{{ $stats['total_votes'] ?? 0 }}</p>
                </div>
                <div class="bg-gradient-to-r from-blue-400 to-blue-600 text-white p-4 rounded-lg">
                    <h3 class="text-lg font-semibold">Eligible Voters</h3>
                    <p class="text-3xl font-bold">{{ $stats['eligible_voters'] ?? 0 }}</p>
                </div>
                <div class="bg-gradient-to-r from-purple-400 to-purple-600 text-white p-4 rounded-lg">
                    <h3 class="text-lg font-semibold">Turnout Rate</h3>
                    <p class="text-3xl font-bold">{{ number_format($stats['turnout_rate'] ?? 0, 1) }}%</p>
                </div>
                <div class="bg-gradient-to-r from-yellow-400 to-yellow-600 text-white p-4 rounded-lg">
                    <h3 class="text-lg font-semibold">Active Positions</h3>
                    <p class="text-3xl font-bold">{{ $stats['total_positions'] ?? 0 }}</p>
                </div>
            </div>

            <!-- Control Buttons -->
            @if (auth()->user()->role === 'organization_admin')
                <div class="px-6 py-4 bg-gray-50 border-t">
                    <div class="flex space-x-4">
                        @if ($election->status === 'draft')
                            <button wire:click="startElection" 
                                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                                Start Election
                            </button>
                        @endif
                        
                        @if ($election->status === 'active')
                            <button wire:click="endElection" 
                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                                End Election
                            </button>
                        @endif

                        <button wire:click="exportResults" 
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                            Export Results
                        </button>
                    </div>
                </div>
            @endif

            <!-- Real-time Results -->
            @if ($election->status === 'active' || $election->status === 'completed')
                <div class="p-6 border-t">
                    <h2 class="text-xl font-bold mb-4">Live Results</h2>
                    
                    @foreach ($results as $positionResult)
                        <div class="mb-6 p-4 border rounded-lg">
                            <h3 class="text-lg font-semibold mb-3">{{ $positionResult['position_name'] }}</h3>
                            
                            @foreach ($positionResult['candidates'] as $candidate)
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center">
                                        <img src="{{ $candidate['photo'] ?? '/default-avatar.png' }}" 
                                             alt="{{ $candidate['name'] }}"
                                             class="w-10 h-10 rounded-full mr-3">
                                        <span class="font-medium">{{ $candidate['name'] }}</span>
                                    </div>
                                    
                                    <div class="flex items-center space-x-4">
                                        <div class="w-32 bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" 
                                                 style="width: {{ $candidate['percentage'] }}%"></div>
                                        </div>
                                        <span class="text-sm font-semibold">{{ $candidate['votes'] }} votes</span>
                                        <span class="text-sm text-gray-500">({{ number_format($candidate['percentage'], 1) }}%)</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>

<script>
    // Auto-refresh every 30 seconds for live updates
    setInterval(function() {
        @this.call('refreshStats');
    }, 30000);
</script>
