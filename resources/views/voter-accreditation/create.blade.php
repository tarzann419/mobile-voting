<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Apply for Voter Accreditation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Voter Accreditation Application</h3>
                        <p class="text-sm text-gray-600">
                            To participate in elections, you must be accredited as a voter. Please select an election and upload the required documents.
                        </p>
                    </div>

                    <!-- Display validation errors -->
                    @if($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($elections->count() > 0)
                        <form method="POST" action="{{ route('voter-accreditation.store') }}" enctype="multipart/form-data">
                            @csrf

                            <!-- Election Selection -->
                            <div class="mb-6">
                                <label for="election_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Election <span class="text-red-500">*</span>
                                </label>
                                <select name="election_id" 
                                        id="election_id" 
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required>
                                    <option value="">Choose an election...</option>
                                    @foreach($elections as $election)
                                        <option value="{{ $election->id }}" {{ old('election_id') == $election->id ? 'selected' : '' }}>
                                            {{ $election->title }}
                                            (Registration: {{ $election->registration_start_date->format('M j') }} - {{ $election->registration_end_date->format('M j, Y') }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">
                                    Only elections currently accepting registrations are shown.
                                </p>
                            </div>

                            <!-- Document Upload -->
                            <div class="mb-6">
                                <label for="documents" class="block text-sm font-medium text-gray-700 mb-2">
                                    Upload Required Documents <span class="text-red-500">*</span>
                                </label>
                                <input type="file" 
                                       name="documents[]" 
                                       id="documents" 
                                       multiple 
                                       accept=".pdf,.jpg,.jpeg,.png"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                       required>
                                <p class="text-xs text-gray-500 mt-1">
                                    Upload supporting documents (PDF, JPG, PNG). Maximum file size: 2MB per file.
                                    Common documents include: ID card, proof of membership, utility bills, etc.
                                </p>
                            </div>

                            <!-- Information Notice -->
                            <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800">What happens next?</h3>
                                        <div class="mt-2 text-sm text-blue-700">
                                            <ul class="list-disc list-inside">
                                                <li>Your application will be reviewed by the organization administrators</li>
                                                <li>You will be notified of the approval status via email</li>
                                                <li>Once approved, you will be able to vote in the selected election</li>
                                                <li>The review process typically takes 1-3 business days</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex justify-end space-x-3">
                                <a href="{{ route('dashboard') }}" 
                                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                    Cancel
                                </a>
                                <button type="submit" 
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Submit Application
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-500 text-lg mb-4">No Elections Available</div>
                            <p class="text-sm text-gray-400 mb-6">
                                There are currently no elections accepting voter registration applications.
                            </p>
                            <a href="{{ route('dashboard') }}" 
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Return to Dashboard
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add file validation
        document.getElementById('documents').addEventListener('change', function(e) {
            const files = e.target.files;
            const maxSize = 2 * 1024 * 1024; // 2MB in bytes
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                
                // Check file size
                if (file.size > maxSize) {
                    alert(`File "${file.name}" is too large. Maximum size is 2MB.`);
                    e.target.value = '';
                    return;
                }
                
                // Check file type
                if (!allowedTypes.includes(file.type)) {
                    alert(`File "${file.name}" is not a supported format. Please use PDF, JPG, or PNG files.`);
                    e.target.value = '';
                    return;
                }
            }
        });
    </script>
</x-app-layout>
