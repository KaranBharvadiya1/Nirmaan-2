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
        Schema::create('shortlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('contractor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('bid_id')->nullable()->constrained('bids')->nullOnDelete();
            $table->text('note')->nullable();
            $table->unsignedTinyInteger('priority')->default(3);
            $table->string('status', 32)->default('active');
            $table->timestamps();

            $table->unique(['owner_id', 'contractor_id'], 'shortlists_owner_contractor_unique');
            $table->index(['owner_id', 'status'], 'shortlists_owner_status_idx');
            $table->index(['contractor_id', 'priority'], 'shortlists_contractor_priority_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shortlists');
    }
};
