<?php

namespace Tests\Feature;

use App\Models\Bid;
use App\Models\Project;
use App\Models\Shortlist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerShortlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_shortlist_contractor_from_bid()
    {
        $owner = User::factory()->create(['role' => 'Owner']);
        $contractor = User::factory()->create(['role' => 'Contractor']);

        $project = $this->createProject($owner, 'Demo project', 'TEST-001');

        $bid = Bid::query()->create([
            'project_id' => $project->id,
            'contractor_id' => $contractor->id,
            'quote_amount' => 60000,
            'proposed_timeline_days' => 30,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($owner)->post(route('owner.shortlist.store'), [
            'contractor_id' => $contractor->id,
            'project_id' => $project->id,
            'bid_id' => $bid->id,
            'note' => 'Great communicator',
            'priority' => 2,
        ]);

        $response->assertRedirect(route('owner.shortlist.index'));
        $this->assertDatabaseHas('shortlists', [
            'owner_id' => $owner->id,
            'contractor_id' => $contractor->id,
            'project_id' => $project->id,
            'bid_id' => $bid->id,
            'priority' => 2,
        ]);
        $this->assertEquals('shortlisted', $bid->fresh()->status);
    }

    public function test_owner_notifications_include_recent_activity()
    {
        $owner = User::factory()->create(['role' => 'Owner']);
        $contractor = User::factory()->create(['role' => 'Contractor']);

        $project = $this->createProject($owner, 'Notification job', 'TEST-002');

        Bid::query()->create([
            'project_id' => $project->id,
            'contractor_id' => $contractor->id,
            'quote_amount' => 75000,
            'proposed_timeline_days' => 45,
            'status' => 'pending',
        ]);

        Shortlist::query()->create([
            'owner_id' => $owner->id,
            'contractor_id' => $contractor->id,
            'project_id' => $project->id,
            'priority' => 4,
            'note' => 'Prioritize',
        ]);

        $response = $this->actingAs($owner)->get(route('owner.notifications'));

        $response->assertOk();
        $response->assertSee('Bid alerts');
        $response->assertSee('Favorites guide');
    }

    private function createProject(User $owner, string $title, string $reference): Project
    {
        return Project::query()->create([
            'owner_id' => $owner->id,
            'reference_code' => $reference,
            'title' => $title,
            'project_type' => 'Residential',
            'work_category' => 'Masonry',
            'description' => 'Testing',
            'site_address' => '123 Test Lane',
            'area_locality' => 'Test Locality',
            'city' => 'Test Town',
            'district' => 'Test District',
            'state' => 'Test State',
            'postal_code' => '500001',
            'budget_currency' => 'INR',
            'budget_min' => 50000,
            'budget_max' => 100000,
            'deadline' => now()->addDays(30)->toDateString(),
            'visibility' => 'public',
            'status' => 'open',
            'preferred_language' => 'English',
        ]);
    }
}
