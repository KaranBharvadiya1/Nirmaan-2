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
        Schema::table('users', function (Blueprint $table) {
            $table->text('contractor_bio')->nullable()->after('password');
            $table->unsignedInteger('years_experience')->nullable()->after('contractor_bio');
            $table->string('trades')->nullable()->after('years_experience');
            $table->string('service_areas')->nullable()->after('trades');
            $table->string('languages')->nullable()->after('service_areas');
            $table->unsignedInteger('team_size')->nullable()->after('languages');
            $table->string('availability_status', 32)->nullable()->after('team_size');
            $table->decimal('hourly_rate_from', 10, 2)->nullable()->after('availability_status');
            $table->decimal('hourly_rate_to', 10, 2)->nullable()->after('hourly_rate_from');
            $table->string('video_intro_url')->nullable()->after('hourly_rate_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'contractor_bio',
                'years_experience',
                'trades',
                'service_areas',
                'languages',
                'team_size',
                'availability_status',
                'hourly_rate_from',
                'hourly_rate_to',
                'video_intro_url',
            ]);
        });
    }
};
