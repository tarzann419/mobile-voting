<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Set MySQL connection explicitly
config(['database.default' => 'mysql']);

echo "🚀 Testing MySQL Candidate Creation\n";
echo "==================================\n\n";

try {
    // Test database connection
    echo "1. Testing MySQL Connection...\n";
    $pdo = DB::connection('mysql')->getPdo();
    echo "✅ Connected to MySQL database: " . DB::connection('mysql')->getDatabaseName() . "\n\n";

    // Check if candidates table has bio column
    echo "2. Checking candidates table structure...\n";
    $columns = DB::connection('mysql')->select('SHOW COLUMNS FROM candidates');
    $hasBio = false;
    foreach ($columns as $column) {
        if ($column->Field === 'bio') {
            $hasbio = true;
            echo "✅ Bio column found: {$column->Field} ({$column->Type})\n";
            break;
        }
    }

    if (!$hasbio) {
        echo "❌ Bio column not found in candidates table\n";
        exit(1);
    }

    // Check for required data
    echo "\n3. Checking test data availability...\n";

    $org = DB::connection('mysql')->table('organizations')->first();
    if (!$org) {
        echo "❌ No organizations found\n";
        exit(1);
    }
    echo "✅ Organization found: {$org->name} (ID: {$org->id})\n";

    $user = DB::connection('mysql')->table('users')
        ->where('organization_id', $org->id)
        ->where('role', 'voter')
        ->first();
    if (!$user) {
        echo "❌ No voter users found\n";
        exit(1);
    }
    echo "✅ User found: {$user->name} (ID: {$user->id})\n";

    $election = DB::connection('mysql')->table('elections')
        ->where('organization_id', $org->id)
        ->whereIn('status', ['published', 'active'])
        ->first();
    if (!$election) {
        echo "❌ No active elections found\n";
        exit(1);
    }
    echo "✅ Election found: {$election->title} (ID: {$election->id})\n";

    $position = DB::connection('mysql')->table('positions')
        ->where('election_id', $election->id)
        ->first();
    if (!$position) {
        echo "❌ No positions found\n";
        exit(1);
    }
    echo "✅ Position found: {$position->title} (ID: {$position->id})\n";

    // Test candidate creation
    echo "\n4. Testing candidate creation...\n";

    $candidateData = [
        'user_id' => $user->id,
        'position_id' => $position->id,
        'organization_id' => $org->id,
        'bio' => 'This is a test biography for the candidate.',
        'manifesto' => 'This is a test manifesto outlining the candidate\'s vision and goals.',
        'status' => 'pending',
        'registered_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ];

    // Check if candidate already exists
    $existingCandidate = DB::connection('mysql')->table('candidates')
        ->where('user_id', $user->id)
        ->where('position_id', $position->id)
        ->first();

    if ($existingCandidate) {
        echo "⚠️  Candidate already exists, deleting for fresh test...\n";
        DB::connection('mysql')->table('candidates')
            ->where('id', $existingCandidate->id)
            ->delete();
    }

    $candidateId = DB::connection('mysql')->table('candidates')->insertGetId($candidateData);

    if ($candidateId) {
        echo "✅ Candidate created successfully with ID: {$candidateId}\n";

        // Verify the candidate was created with all fields
        $candidate = DB::connection('mysql')->table('candidates')->find($candidateId);
        echo "✅ Verification - Bio: " . substr($candidate->bio, 0, 50) . "...\n";
        echo "✅ Verification - Manifesto: " . substr($candidate->manifesto, 0, 50) . "...\n";
        echo "✅ Verification - Status: {$candidate->status}\n";

        // Clean up
        DB::connection('mysql')->table('candidates')->where('id', $candidateId)->delete();
        echo "✅ Test candidate cleaned up\n";
    } else {
        echo "❌ Failed to create candidate\n";
        exit(1);
    }

    echo "\n🎉 All tests passed! MySQL candidate creation is working correctly.\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
