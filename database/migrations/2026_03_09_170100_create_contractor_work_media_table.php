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
        Schema::create('contractor_work_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contractor_work_sample_id')->constrained()->cascadeOnDelete();
            $table->enum('media_type', ['image', 'video', 'external_video']);
            $table->string('original_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('external_url', 500)->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['contractor_work_sample_id', 'media_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_work_media');
    }
};
