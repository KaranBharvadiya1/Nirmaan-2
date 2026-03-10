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
        if (Schema::hasTable('email_templates')) {
            return;
        }

        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('subject');
            $table->text('body');
            $table->timestamps();
        });

        DB::table('email_templates')->insert([
            [
                'name' => 'welcome',
                'subject' => 'Welcome to Nirmaan, {{name}}',
                'body' => '<p>Hello {{name}},</p><p>We are glad you joined Nirmaan—please verify your account using this link:</p><p><a href="{{verification_url}}">Verify email</a></p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'bid_received',
                'subject' => 'New bid on {{project_reference}}',
                'body' => '<p>Hello {{owner_name}},</p><p>{{contractor_name}} submitted a new bid for {{project_title}} ({{project_reference}}). Quote: ₹{{quote_amount}}.</p><p><a href="{{project_url}}">Review bid</a></p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'bid_status',
                'subject' => 'Your bid has been {{status}}',
                'body' => '<p>Hello {{contractor_name}},</p><p>Your bid for {{project_title}} is now marked as <strong>{{status}}</strong>.</p><p><a href="{{bid_url}}">View update</a></p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'hire_confirmation',
                'subject' => 'You were hired for {{project_title}}',
                'body' => '<p>Hello {{recipient_name}},</p><p>The owner has confirmed the hire for {{project_title}} ({{project_reference}}). Quote: ₹{{quote_amount}}.</p><p><a href="{{hire_url}}">View details</a></p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'bid_submission',
                'subject' => '{{contractor_name}} submitted a bid for {{project_reference}}',
                'body' => '<p>Hello {{owner_name}},</p><p>{{contractor_name}} has submitted a bid for {{project_title}}.</p><p><a href="{{project_url}}">View project</a></p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'shortlist_update',
                'subject' => 'Shortlist note updated for {{contractor_name}}',
                'body' => '<p>Hello {{owner_name}},</p><p>You or your team added a note on {{contractor_name}} for {{project_title}}.</p><p><a href="{{shortlist_url}}">Review shortlist</a></p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'weekly_digest',
                'subject' => 'Nirmaan weekly activity digest',
                'body' => '<p>Hello {{name}},</p><p>This week you have {{open_projects}} open projects, {{pending_bids}} pending bids, and {{active_hires}} active hires.</p><p><a href="{{dashboard_url}}">Go to dashboard</a></p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
