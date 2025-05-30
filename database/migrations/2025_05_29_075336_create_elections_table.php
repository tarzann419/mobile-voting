<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('elections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'published', 'active', 'completed', 'cancelled'])->default('draft');
            $table->datetime('registration_start_date');
            $table->datetime('registration_end_date');
            $table->datetime('voting_start_date');
            $table->datetime('voting_end_date');
            $table->boolean('allow_multiple_votes')->default(false);
            $table->boolean('require_payment')->default(false);
            $table->json('settings')->nullable(); // Additional election settings
            $table->timestamps();

            $table->index(['organization_id', 'status']);
            $table->index(['voting_start_date', 'voting_end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elections');
    }
};
