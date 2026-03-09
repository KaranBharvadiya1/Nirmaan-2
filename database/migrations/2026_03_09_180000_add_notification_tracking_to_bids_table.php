<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bids', function (Blueprint $table) {
            $table->timestamp('owner_viewed_at')->nullable()->after('status');
            $table->timestamp('contractor_status_viewed_at')->nullable()->after('owner_viewed_at');
        });

        DB::table('bids')->update([
            'owner_viewed_at' => now(),
            'contractor_status_viewed_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bids', function (Blueprint $table) {
            $table->dropColumn(['owner_viewed_at', 'contractor_status_viewed_at']);
        });
    }
};
