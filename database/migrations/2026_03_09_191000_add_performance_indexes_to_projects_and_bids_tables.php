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
        Schema::table('projects', function (Blueprint $table) {
            // These composite indexes match the two highest-traffic project list patterns.
            $table->index(['owner_id', 'created_at'], 'projects_owner_created_at_idx');
            $table->index(['status', 'visibility', 'created_at'], 'projects_status_visibility_created_at_idx');
        });

        Schema::table('bids', function (Blueprint $table) {
            // Unread badge checks run on every panel page, so the notification filters get dedicated indexes.
            $table->index(['project_id', 'owner_viewed_at'], 'bids_project_owner_viewed_at_idx');
            $table->index(['contractor_id', 'status', 'contractor_status_viewed_at'], 'bids_contractor_status_viewed_at_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bids', function (Blueprint $table) {
            $table->dropIndex('bids_project_owner_viewed_at_idx');
            $table->dropIndex('bids_contractor_status_viewed_at_idx');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('projects_owner_created_at_idx');
            $table->dropIndex('projects_status_visibility_created_at_idx');
        });
    }
};
