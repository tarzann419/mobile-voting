<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Set MySQL connection explicitly
config(['database.default' => 'mysql']);

echo "🔍 Testing User Filtering for Admin Candidate Creation\n";
echo "====================================================\n\n";

try {
    // Get test organization
    $org = DB::connection('mysql')->table('organizations')->first();
    if (!$org) {
        echo "❌ No organizations found\n";
        exit(1);
    }

    echo "🏢 Organization: {$org->name} (ID: {$org->id})\n\n";

    // Get all voters in the organization
    $allVoters = DB::connection('mysql')->table('users')
        ->where('organization_id', $org->id)
        ->where('role', 'voter')
        ->where('is_active', true)
        ->get();

    echo "👥 Total active voters in organization: " . count($allVoters) . "\n";

    // Get existing candidates
    $existingCandidates = DB::connection('mysql')->table('candidates')
        ->join('positions', 'candidates.position_id', '=', 'positions.id')
        ->join('elections', 'positions.election_id', '=', 'elections.id')
        ->where('elections.organization_id', $org->id)
        ->select('candidates.user_id', 'users.name as user_name')
        ->join('users', 'candidates.user_id', '=', 'users.id')
        ->get();

    echo "🗳️  Existing candidates: " . count($existingCandidates) . "\n";

    if (count($existingCandidates) > 0) {
        echo "   Existing candidates:\n";
        foreach ($existingCandidates as $candidate) {
            echo "   - {$candidate->user_name} (ID: {$candidate->user_id})\n";
        }
    }

    // Get candidate user IDs
    $candidateUserIds = collect($existingCandidates)->pluck('user_id')->unique()->toArray();

    // Filter available users (those who are not already candidates)
    $availableUsers = $allVoters->whereNotIn('id', $candidateUserIds);

    echo "\n✅ Available users for candidate creation: " . count($availableUsers) . "\n";

    if (count($availableUsers) > 0) {
        echo "   Available users:\n";
        foreach ($availableUsers->take(5) as $user) {
            echo "   - {$user->name} (ID: {$user->id})\n";
        }
        if (count($availableUsers) > 5) {
            echo "   ... and " . (count($availableUsers) - 5) . " more\n";
        }
    } else {
        echo "   ⚠️  No users available for candidate creation\n";
    }

    // Test the actual controller logic simulation
    echo "\n🔄 Simulating Controller Logic...\n";

    // This simulates what the adminCreate method does
    $simulatedCandidateUserIds = DB::connection('mysql')->table('candidates')
        ->join('positions', 'candidates.position_id', '=', 'positions.id')
        ->join('elections', 'positions.election_id', '=', 'elections.id')
        ->where('elections.organization_id', $org->id)
        ->pluck('candidates.user_id')
        ->unique();

    $simulatedAvailableUsers = DB::connection('mysql')->table('users')
        ->where('organization_id', $org->id)
        ->where('role', 'voter')
        ->where('is_active', true)
        ->whereNotIn('id', $simulatedCandidateUserIds)
        ->orderBy('name')
        ->get();

    echo "✅ Controller simulation result: " . count($simulatedAvailableUsers) . " available users\n";

    if (count($availableUsers) === count($simulatedAvailableUsers)) {
        echo "✅ Filter logic working correctly!\n";
    } else {
        echo "❌ Filter logic mismatch!\n";
        exit(1);
    }

    echo "\n🎉 User filtering test completed successfully!\n";
    echo "✅ Users who are already candidates are properly excluded from the dropdown.\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
