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

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-purple-600 text-white px-6 py-4">
            <h1 class="text-2xl font-bold">Candidate Registration</h1>
            <p class="text-purple-100">Register to run for available positions</p>
        </div>

        <!-- Available Positions -->
        <div class="p-6">
            <h2 class="text-xl font-bold mb-4">Available Positions</h2>
            
            @if ($availablePositions->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($availablePositions as $position)
                        <div class="border rounded-lg p-4">
                            <h3 class="text-lg font-semibold">{{ $position->name }}</h3>
                            <p class="text-gray-600 mb-3">{{ $position->description }}</p>
                            
                            @if ($position->registration_fee > 0)
                                <p class="text-sm text-purple-600 font-semibold mb-3">
                                    Registration Fee: ${{ number_format($position->registration_fee, 2) }}
                                </p>
                            @endif

                            @if (in_array($position->id, $userRegistrations->pluck('position_id')->toArray()))
                                <div class="bg-green-100 text-green-800 px-3 py-2 rounded text-sm">
                                    ✓ Already Registered
                                </div>
                            @else
                                <button wire:click="selectPosition({{ $position->id }})" 
                                        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded text-sm">
                                    Register for this Position
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-500">No positions available for registration at this time.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Registration Modal -->
    @if ($showRegistrationForm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        Register for: {{ $selectedPosition->name ?? '' }}
                    </h3>
                    
                    <form wire:submit.prevent="submitRegistration">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Bio/Platform Statement
                            </label>
                            <textarea wire:model="candidateData.bio" 
                                      rows="4" 
                                      class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500"
                                      placeholder="Tell voters about yourself and your platform..."></textarea>
                            @error('candidateData.bio') 
                                <span class="text-red-500 text-sm">{{ $message }}</span> 
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Photo
                            </label>
                            <input type="file" 
                                   wire:model="candidateData.photo" 
                                   accept="image/*"
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500">
                            @error('candidateData.photo') 
                                <span class="text-red-500 text-sm">{{ $message }}</span> 
                            @enderror
                        </div>

                        @if ($selectedPosition && $selectedPosition->registration_fee > 0)
                            <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <h4 class="font-semibold text-yellow-800">Payment Required</h4>
                                <p class="text-yellow-700">
                                    Registration fee: ${{ number_format($selectedPosition->registration_fee, 2) }}
                                </p>
                                
                                <div class="mt-3">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">
                                        Payment Method
                                    </label>
                                    <select wire:model="paymentData.method" 
                                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500">
                                        <option value="">Select payment method</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="mobile_money">Mobile Money</option>
                                        <option value="cash">Cash Payment</option>
                                    </select>
                                </div>

                                <div class="mt-3">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">
                                        Transaction Reference
                                    </label>
                                    <input type="text" 
                                           wire:model="paymentData.transaction_ref" 
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-purple-500"
                                           placeholder="Enter transaction reference">
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-end space-x-4">
                            <button type="button" 
                                    wire:click="closeRegistrationForm"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                Submit Registration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- User's Registrations -->
    @if ($userRegistrations->count() > 0)
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mt-6">
            <div class="bg-gray-50 px-6 py-4 border-b">
                <h2 class="text-xl font-bold">Your Registrations</h2>
            </div>
            
            <div class="p-6">
                <div class="space-y-4">
                    @foreach ($userRegistrations as $registration)
                        <div class="flex items-center justify-between p-4 border rounded-lg">
                            <div>
                                <h3 class="font-semibold">{{ $registration->position->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $registration->position->election->name }}</p>
                                <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded mt-1">
                                    {{ ucfirst($registration->status) }}
                                </span>
                            </div>
                            
                            @if ($registration->position->registration_fee > 0)
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">Fee: ${{ number_format($registration->position->registration_fee, 2) }}</p>
                                    @if ($registration->candidatePayment)
                                        <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs rounded">
                                            {{ ucfirst($registration->candidatePayment->status) }}
                                        </span>
                                    @else
                                        <span class="inline-block px-2 py-1 bg-red-100 text-red-800 text-xs rounded">
                                            Payment Pending
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
