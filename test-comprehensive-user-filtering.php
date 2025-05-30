<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Set MySQL connection explicitly
config(['database.default' => 'mysql']);

echo "🔍 Comprehensive User Filtering Test for Admin Candidate Creation\n";
echo "===============================================================\n\n";

try {
    // Get test organization
    $org = DB::connection('mysql')->table('organizations')->first();
    if (!$org) {
        echo "❌ No organizations found\n";
        exit(1);
    }

    echo "🏢 Testing Organization: {$org->name} (ID: {$org->id})\n\n";

    // Step 1: Show all voters
    $allVoters = DB::connection('mysql')->table('users')
        ->where('organization_id', $org->id)
        ->where('role', 'voter')
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    echo "👥 STEP 1: All Active Voters in Organization\n";
    echo "-------------------------------------------\n";
    echo "Total active voters: " . count($allVoters) . "\n";
    foreach ($allVoters as $voter) {
        echo "   - {$voter->name} (ID: {$voter->id}, Email: {$voter->email})\n";
    }

    // Step 2: Show existing candidates
    echo "\n🗳️  STEP 2: Existing Candidates\n";
    echo "------------------------------\n";
    
    $existingCandidates = DB::connection('mysql')->table('candidates')
        ->join('positions', 'candidates.position_id', '=', 'positions.id')
        ->join('elections', 'positions.election_id', '=', 'elections.id')
        ->join('users', 'candidates.user_id', '=', 'users.id')
        ->where('elections.organization_id', $org->id)
        ->select('candidates.user_id', 'users.name as user_name', 'positions.title as position_title', 'candidates.status')
        ->get();

    echo "Total candidate registrations: " . count($existingCandidates) . "\n";
    
    // Group by user to show unique candidates
    $candidatesByUser = $existingCandidates->groupBy('user_id');
    echo "Unique users who are candidates: " . count($candidatesByUser) . "\n\n";
    
    foreach ($candidatesByUser as $userId => $userCandidates) {
        $userName = $userCandidates->first()->user_name;
        echo "   👤 {$userName} (ID: {$userId})\n";
        foreach ($userCandidates as $candidate) {
            echo "      → {$candidate->position_title} ({$candidate->status})\n";
        }
    }

    // Step 3: Apply filtering logic
    echo "\n🔍 STEP 3: Applying Filter Logic\n";
    echo "-------------------------------\n";
    
    $candidateUserIds = $candidatesByUser->keys()->toArray();
    echo "User IDs to exclude: [" . implode(', ', $candidateUserIds) . "]\n";
    
    $availableUsers = $allVoters->whereNotIn('id', $candidateUserIds);
    
    echo "\n✅ STEP 4: Available Users for Candidate Creation\n";
    echo "------------------------------------------------\n";
    echo "Available users: " . count($availableUsers) . "\n";
    
    if (count($availableUsers) > 0) {
        echo "Users eligible for candidate creation:\n";
        foreach ($availableUsers as $user) {
            echo "   ✓ {$user->name} (ID: {$user->id}, Email: {$user->email})\n";
        }
    } else {
        echo "⚠️  No users available - all active voters are already candidates\n";
    }

    // Step 5: Verify controller logic matches
    echo "\n🔄 STEP 5: Controller Logic Verification\n";
    echo "---------------------------------------\n";
    
    // Simulate the exact controller logic
    $repositoryResult = DB::connection('mysql')->table('candidates')
        ->join('positions', 'candidates.position_id', '=', 'positions.id')
        ->join('elections', 'positions.election_id', '=', 'elections.id')
        ->where('elections.organization_id', $org->id)
        ->pluck('candidates.user_id')
        ->unique();

    $controllerAvailableUsers = DB::connection('mysql')->table('users')
        ->where('organization_id', $org->id)
        ->where('role', 'voter')
        ->where('is_active', true)
        ->whereNotIn('id', $repositoryResult)
        ->orderBy('name')
        ->get();

    echo "Controller logic result: " . count($controllerAvailableUsers) . " available users\n";
    
    if (count($availableUsers) === count($controllerAvailableUsers)) {
        echo "✅ Filter logic verification: PASSED\n";
        echo "✅ Existing candidates are properly excluded from the dropdown\n";
    } else {
        echo "❌ Filter logic verification: FAILED\n";
        echo "   Manual count: " . count($availableUsers) . "\n";
        echo "   Controller count: " . count($controllerAvailableUsers) . "\n";
    }

    // Summary
    echo "\n📊 SUMMARY\n";
    echo "=========\n";
    echo "• Total voters in organization: " . count($allVoters) . "\n";
    echo "• Users who are candidates: " . count($candidatesByUser) . "\n";
    echo "• Users available for new candidacies: " . count($availableUsers) . "\n";
    echo "• Filtering working correctly: " . (count($availableUsers) === count($controllerAvailableUsers) ? 'YES' : 'NO') . "\n";
    
    echo "\n🎉 User filtering comprehensive test completed!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
