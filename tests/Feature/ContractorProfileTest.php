<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractorProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_contractor_can_update_profile_metadata()
    {
        $contractor = User::factory()->create([
            'role' => 'Contractor',
            'contractor_bio' => null,
            'trades' => null,
        ]);

        $payload = [
            'first_name' => 'Alex',
            'last_name' => 'Builder',
            'email' => $contractor->email,
            'contractor_bio' => 'Experienced builder',
            'years_experience' => 12,
            'trades' => 'Tiles, Carpentry',
            'service_areas' => 'Hyderabad',
            'languages' => 'English, Telugu',
            'team_size' => 10,
            'availability_status' => 'available',
            'hourly_rate_from' => '600.00',
            'hourly_rate_to' => '1100.00',
            'video_intro_url' => 'https://example.com/intro',
        ];

        $response = $this->actingAs($contractor)->put(route('contractor.settings.save'), $payload);

        $response->assertRedirect(route('contractor.settings'));

        $contractor->refresh();
        $this->assertSame('Experienced builder', $contractor->contractor_bio);
        $this->assertSame('Tiles, Carpentry', $contractor->trades);
        $this->assertSame('Hyderabad', $contractor->service_areas);
        $this->assertSame('English, Telugu', $contractor->languages);
        $this->assertSame('available', $contractor->availability_status);
        $this->assertSame('600.00', number_format((float) $contractor->hourly_rate_from, 2, '.', ''));
        $this->assertSame('1100.00', number_format((float) $contractor->hourly_rate_to, 2, '.', ''));
        $this->assertSame('https://example.com/intro', $contractor->video_intro_url);
    }
}
