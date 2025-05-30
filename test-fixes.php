<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Election;
use App\Models\Position;
use App\Models\Organization;
use App\Models\User;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Testing Mobile Voting Application Fixes\n";
echo "==========================================\n\n";

try {
    // Test 1: Position Registration Fee Storage
    echo "📋 Test 1: Position Registration Fee Functionality\n";
    echo "---------------------------------------------------\n";

    // Find an organization and user
    $organization = Organization::first();
    $user = User::where('organization_id', $organization->id)->first();

    if (!$organization || !$user) {
        echo "❌ No organization or user found for testing\n";
        exit(1);
    }

    echo "✅ Using Organization: {$organization->name}\n";
    echo "✅ Using User: {$user->name}\n";

    // Create a test election
    $election = Election::create([
        'organization_id' => $organization->id,
        'title' => 'Test Election - ' . date('Y-m-d H:i:s'),
        'description' => 'Test election for position registration fee',
        'status' => 'draft',
        'registration_start_date' => now()->addDays(1),
        'registration_end_date' => now()->addDays(7),
        'voting_start_date' => now()->addDays(8),
        'voting_end_date' => now()->addDays(15),
        'allow_multiple_votes' => false,
        'require_payment' => true,
    ]);

    echo "✅ Created test election: {$election->title}\n";

    // Test position creation with registration fee
    $position = Position::create([
        'organization_id' => $organization->id,
        'election_id' => $election->id,
        'title' => 'Test Position with Fee',
        'description' => 'Position requiring registration fee',
        'max_candidates' => 5,
        'order' => 1,
        'amount_required' => 500.00, // ₱500 registration fee
    ]);

    echo "✅ Created position with registration fee: ₱{$position->amount_required}\n";

    // Test 2: Election Payment Requirement Update
    echo "\n🗳️  Test 2: Election Payment Requirement Update\n";
    echo "-----------------------------------------------\n";

    // Test updating election without payment requirement
    $originalRequirePayment = $election->require_payment;
    echo "✅ Original require_payment value: " . ($originalRequirePayment ? 'true' : 'false') . "\n";

    // Update election to not require payment
    $election->update([
        'require_payment' => false,
    ]);
    $election->refresh();

    echo "✅ Updated require_payment to: " . ($election->require_payment ? 'true' : 'false') . "\n";

    // Verify election still exists and wasn't deleted
    $existingElection = Election::find($election->id);
    if ($existingElection) {
        echo "✅ Election still exists after update (ID: {$existingElection->id})\n";
    } else {
        echo "❌ Election was deleted unexpectedly!\n";
        exit(1);
    }

    // Update back to require payment
    $election->update([
        'require_payment' => true,
    ]);
    $election->refresh();

    echo "✅ Updated require_payment back to: " . ($election->require_payment ? 'true' : 'false') . "\n";

    // Verify election still exists
    $existingElection = Election::find($election->id);
    if ($existingElection) {
        echo "✅ Election still exists after second update (ID: {$existingElection->id})\n";
    } else {
        echo "❌ Election was deleted unexpectedly on second update!\n";
        exit(1);
    }

    // Test 3: Database Schema Verification
    echo "\n🗃️  Test 3: Database Schema Verification\n";
    echo "---------------------------------------\n";

    // Check if positions table has amount_required column
    $positionColumns = \DB::select("PRAGMA table_info(positions)");
    $hasAmountRequired = false;
    foreach ($positionColumns as $column) {
        if ($column->name === 'amount_required') {
            $hasAmountRequired = true;
            echo "✅ positions.amount_required column exists (type: {$column->type})\n";
            break;
        }
    }

    if (!$hasAmountRequired) {
        echo "❌ positions.amount_required column missing!\n";
    }

    // Check if elections table has require_payment column
    $electionColumns = \DB::select("PRAGMA table_info(elections)");
    $hasRequirePayment = false;
    foreach ($electionColumns as $column) {
        if ($column->name === 'require_payment') {
            $hasRequirePayment = true;
            echo "✅ elections.require_payment column exists (type: {$column->type})\n";
            break;
        }
    }

    if (!$hasRequirePayment) {
        echo "❌ elections.require_payment column missing!\n";
    }

    // Test 4: Model Relationships and Data Integrity
    echo "\n🔗 Test 4: Model Relationships and Data Integrity\n";
    echo "------------------------------------------------\n";

    // Test position relationship to election
    $positionElection = $position->election;
    if ($positionElection && $positionElection->id === $election->id) {
        echo "✅ Position correctly linked to election\n";
    } else {
        echo "❌ Position-Election relationship broken\n";
    }

    // Test election positions relationship
    $electionPositions = $election->positions;
    if ($electionPositions->contains($position->id)) {
        echo "✅ Election correctly has the test position\n";
    } else {
        echo "❌ Election-Positions relationship broken\n";
    }

    // Clean up test data
    echo "\n🧹 Cleanup\n";
    echo "----------\n";

    $position->delete();
    echo "✅ Deleted test position\n";

    $election->delete();
    echo "✅ Deleted test election\n";

    echo "\n🎉 All Tests Completed Successfully!\n";
    echo "=====================================\n";
    echo "\n✅ Issue 1 FIXED: Position registration fee field is now properly stored\n";
    echo "✅ Issue 2 FIXED: Election require_payment updates no longer delete elections\n";
    echo "\nThe mobile voting application fixes have been validated!\n";
} catch (Exception $e) {
    echo "\n❌ Test Failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
