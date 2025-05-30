<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "Testing Candidate Creation Functionality\n";
echo "========================================\n\n";

// Test 1: Check if voter can access candidate registration
echo "1. Testing voter candidate registration access:\n";
try {
    $voter = App\Models\User::where('role', 'voter')->first();
    if ($voter) {
        echo "Found voter: {$voter->name} ({$voter->email})\n";

        // Simulate authenticated request
        $request = Illuminate\Http\Request::create('/candidate/register', 'GET');
        $request->setUserResolver(function () use ($voter) {
            return $voter;
        });

        auth()->login($voter);
        $response = $kernel->handle($request);

        echo "Status: " . $response->getStatusCode() . "\n";
        if ($response->getStatusCode() === 200) {
            echo "✅ Voter can access candidate registration form\n";
        } else {
            echo "❌ Voter cannot access candidate registration form\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Error testing voter access: " . $e->getMessage() . "\n";
}

echo "\n---\n\n";

// Test 2: Check if organization admin can access admin candidate creation
echo "2. Testing admin candidate creation access:\n";
try {
    $admin = App\Models\User::where('role', 'organization_admin')->first();
    if ($admin) {
        echo "Found admin: {$admin->name} ({$admin->email})\n";

        // Simulate authenticated request
        auth()->login($admin);
        $request = Illuminate\Http\Request::create('/admin/candidates/create', 'GET');
        $request->setUserResolver(function () use ($admin) {
            return $admin;
        });

        $response = $kernel->handle($request);

        echo "Status: " . $response->getStatusCode() . "\n";
        if ($response->getStatusCode() === 200) {
            echo "✅ Admin can access candidate creation form\n";
        } else {
            echo "❌ Admin cannot access candidate creation form\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Error testing admin access: " . $e->getMessage() . "\n";
}

echo "\n---\n\n";

// Test 3: Check election filtering logic
echo "3. Testing election filtering for candidate registration:\n";
try {
    $controller = new App\Http\Controllers\CandidateController();

    // Test the logic that finds available elections
    $elections = App\Models\Election::whereIn('status', ['published', 'active'])
        ->where('registration_start_date', '<=', now())
        ->where('registration_end_date', '>=', now())
        ->get();

    echo "Found {$elections->count()} elections with open registration:\n";
    foreach ($elections as $election) {
        echo "- {$election->title} (Status: {$election->status})\n";
        echo "  Registration: {$election->registration_start_date} to {$election->registration_end_date}\n";
    }

    if ($elections->count() > 0) {
        echo "✅ Election filtering logic working correctly\n";
    } else {
        echo "❌ No elections found with open registration\n";
    }
} catch (Exception $e) {
    echo "❌ Error testing election filtering: " . $e->getMessage() . "\n";
}

echo "\n---\n\n";

// Test 4: Test candidate creation validation
echo "4. Testing candidate creation validation:\n";
try {
    $voter = App\Models\User::where('role', 'voter')->first();
    $election = App\Models\Election::whereIn('status', ['published', 'active'])
        ->where('registration_start_date', '<=', now())
        ->where('registration_end_date', '>=', now())
        ->first();

    if ($voter && $election) {
        echo "Testing with voter: {$voter->name}\n";
        echo "Testing with election: {$election->title}\n";

        // Check if voter already has a candidate record for this election
        $existingCandidate = App\Models\Candidate::where('user_id', $voter->id)
            ->where('election_id', $election->id)
            ->first();

        if ($existingCandidate) {
            echo "⚠️ Voter already has candidate record for this election\n";
        } else {
            echo "✅ Voter can potentially register as candidate\n";
        }

        // Check organization membership
        if ($voter->organization_id === $election->organization_id) {
            echo "✅ Voter belongs to election organization\n";
        } else {
            echo "❌ Voter does not belong to election organization\n";
        }
    } else {
        echo "❌ Missing voter or election for testing\n";
    }
} catch (Exception $e) {
    echo "❌ Error testing validation: " . $e->getMessage() . "\n";
}

echo "\n---\n\n";

// Test 5: Check current candidates
echo "5. Current candidate statistics:\n";
try {
    $totalCandidates = App\Models\Candidate::count();
    $pendingCandidates = App\Models\Candidate::where('status', 'pending')->count();
    $approvedCandidates = App\Models\Candidate::where('status', 'approved')->count();
    $rejectedCandidates = App\Models\Candidate::where('status', 'rejected')->count();

    echo "Total candidates: {$totalCandidates}\n";
    echo "Pending: {$pendingCandidates}\n";
    echo "Approved: {$approvedCandidates}\n";
    echo "Rejected: {$rejectedCandidates}\n";
} catch (Exception $e) {
    echo "❌ Error getting candidate statistics: " . $e->getMessage() . "\n";
}

echo "\nTest completed!\n";
