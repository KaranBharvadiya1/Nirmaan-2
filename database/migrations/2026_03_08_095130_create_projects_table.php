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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('reference_code')->unique();
            $table->string('title');
            $table->string('project_type', 50);
            $table->string('work_category', 150)->nullable();
            $table->text('description');

            $table->string('site_address');
            $table->string('area_locality', 120);
            $table->string('city', 80);
            $table->string('district', 80);
            $table->string('state', 80);
            $table->string('postal_code', 10);
            $table->string('landmark', 120)->nullable();

            $table->string('budget_currency', 3)->default('INR');
            $table->decimal('budget_min', 12, 2);
            $table->decimal('budget_max', 12, 2)->nullable();

            $table->date('required_start_date')->nullable();
            $table->date('deadline');
            $table->unsignedInteger('expected_duration_days')->nullable();

            $table->unsignedInteger('labor_strength_required')->nullable();
            $table->enum('material_supply_mode', ['owner', 'contractor', 'shared'])->default('shared');
            $table->enum('visibility', ['public', 'invite_only'])->default('public');
            $table->enum('status', ['open', 'in_progress', 'completed', 'cancelled'])->default('open');
            $table->string('preferred_language', 50)->default('English');
            $table->text('safety_requirements')->nullable();
            $table->text('quality_expectations')->nullable();
            $table->timestamps();

            $table->index(['owner_id', 'status']);
            $table->index(['city', 'state', 'postal_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
