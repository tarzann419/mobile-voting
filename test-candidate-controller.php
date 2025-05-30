<?php

use App\Http\Controllers\CandidateController;
use App\Repositories\CandidateRepository;
use App\Repositories\ElectionRepository;
use App\Services\CandidateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

echo "🗳️ Testing Candidate Creation Controller\n";
echo "=======================================\n\n";

// Test with a voter user from organization 1
$testUser = App\Models\User::where('email', 'william.miller.1@university.edu')->first();

if (!$testUser) {
    echo "❌ Test user not found\n";
    exit(1);
}

echo "Test User: {$testUser->name} ({$testUser->email})\n";
echo "Role: {$testUser->role}\n";
echo "Organization: {$testUser->organization_id}\n\n";

// Simulate logged in user
Auth::login($testUser);

// Create controller instance
$candidateRepository = new CandidateRepository();
$electionRepository = new ElectionRepository();
$candidateService = new CandidateService($candidateRepository);

$controller = new CandidateController(
    $candidateService,
    $candidateRepository,
    $electionRepository
);

echo "Testing candidate create method...\n";

try {
    $request = new Request();
    $response = $controller->create($request);

    // Get the data passed to the view
    $data = $response->getData();

    echo "✅ Controller method executed successfully\n";
    echo "Available positions: {$data['availablePositions']->count()}\n";

    if ($data['availablePositions']->count() > 0) {
        echo "\nPositions available for registration:\n";
        foreach ($data['availablePositions']->take(5) as $position) {
            echo "  - {$position->title} (Election: {$position->election->title})\n";
        }
    } else {
        echo "❌ No positions available for candidate registration\n";
        echo "This could be because:\n";
        echo "  - No elections are in published/active status\n";
        echo "  - Registration periods have ended\n";
        echo "  - User has already registered for all available positions\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Testing admin candidate create method...\n";

// Test with organization admin
$adminUser = App\Models\User::where('email', 'john.anderson@university.edu')->first();
Auth::login($adminUser);

echo "Admin User: {$adminUser->name} ({$adminUser->email})\n";
echo "Role: {$adminUser->role}\n\n";

try {
    $request = new Request();
    $response = $controller->adminCreate($request);

    // Get the data passed to the view
    $data = $response->getData();

    echo "✅ Admin controller method executed successfully\n";
    echo "Available positions: {$data['availablePositions']->count()}\n";
    echo "Organization users: {$data['organizationUsers']->count()}\n";

    if ($data['availablePositions']->count() > 0) {
        echo "\nPositions available for admin candidate creation:\n";
        foreach ($data['availablePositions']->take(3) as $position) {
            echo "  - {$position->title} (Election: {$position->election->title})\n";
        }
    }

    if ($data['organizationUsers']->count() > 0) {
        echo "\nUsers available to create candidates for:\n";
        foreach ($data['organizationUsers']->take(3) as $user) {
            echo "  - {$user->name} ({$user->email})\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n✅ Test completed!\n";
