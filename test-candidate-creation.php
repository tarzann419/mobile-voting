<?php

// Test candidate creation functionality
use Illuminate\Support\Facades\DB;

echo "🗳️ Testing Candidate Creation Functionality\n";
echo "==========================================\n\n";

// Check current user in session
if (session()->has('user_id')) {
    $userId = session('user_id');
    echo "Session User ID: $userId\n";

    $user = DB::table('users')->find($userId);
    if ($user) {
        echo "User: {$user->name} ({$user->email})\n";
        echo "Role: {$user->role}\n";
        echo "Organization ID: {$user->organization_id}\n\n";
    }
} else {
    echo "No user session found\n\n";
}

// Check available elections for candidate registration
echo "Available Elections for Candidate Registration:\n";
echo "-----------------------------------------------\n";

$elections = DB::table('elections')
    ->select('id', 'title', 'status', 'organization_id', 'registration_start_date', 'registration_end_date')
    ->whereIn('status', ['registration', 'active'])
    ->get();

if ($elections->count() > 0) {
    foreach ($elections as $election) {
        echo "Election: {$election->title}\n";
        echo "  Status: {$election->status}\n";
        echo "  Organization: {$election->organization_id}\n";
        echo "  Registration: {$election->registration_start_date} to {$election->registration_end_date}\n";

        // Get positions for this election
        $positions = DB::table('positions')
            ->where('election_id', $election->id)
            ->get();

        echo "  Positions: " . $positions->count() . "\n";
        foreach ($positions as $position) {
            echo "    - {$position->title}\n";
        }
        echo "\n";
    }
} else {
    echo "No elections available for candidate registration\n\n";
}

// Check existing candidates
echo "Existing Candidates:\n";
echo "-------------------\n";
$candidates = DB::table('candidates')
    ->join('users', 'candidates.user_id', '=', 'users.id')
    ->join('positions', 'candidates.position_id', '=', 'positions.id')
    ->join('elections', 'positions.election_id', '=', 'elections.id')
    ->select('candidates.*', 'users.name as user_name', 'positions.title as position_title', 'elections.title as election_title')
    ->orderBy('candidates.created_at', 'desc')
    ->limit(10)
    ->get();

foreach ($candidates as $candidate) {
    echo "Candidate: {$candidate->user_name}\n";
    echo "  Position: {$candidate->position_title}\n";
    echo "  Election: {$candidate->election_title}\n";
    echo "  Status: {$candidate->status}\n";
    echo "  Created: {$candidate->created_at}\n\n";
}

echo "Total candidates: " . DB::table('candidates')->count() . "\n\n";

// Check users that can become candidates
echo "Users Available for Candidate Creation:\n";
echo "--------------------------------------\n";
$voters = DB::table('users')
    ->where('role', 'voter')
    ->where('is_active', true)
    ->orderBy('organization_id')
    ->orderBy('name')
    ->get();

$organizationGroups = $voters->groupBy('organization_id');
foreach ($organizationGroups as $orgId => $orgUsers) {
    $org = DB::table('organizations')->find($orgId);
    echo "Organization: " . ($org ? $org->name : "Unknown") . " (ID: $orgId)\n";
    echo "  Voters: " . $orgUsers->count() . "\n";
    foreach ($orgUsers->take(3) as $user) {
        echo "    - {$user->name} ({$user->email})\n";
    }
    if ($orgUsers->count() > 3) {
        echo "    ... and " . ($orgUsers->count() - 3) . " more\n";
    }
    echo "\n";
}

echo "✅ Test completed!\n";
