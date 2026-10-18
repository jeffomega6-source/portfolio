<?php

namespace Tests\Unit;

use App\Models\Portfolio;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PortfolioProfilePictureUrlTest extends TestCase
{
    public function test_stored_profile_picture_path_resolves_through_the_configured_photo_disk(): void
    {
        config([
            'filesystems.photo_disk' => 'public',
            'filesystems.disks.public.url' => 'https://cdn.example.test/storage',
        ]);
        Storage::forgetDisk('public');

        $portfolio = new Portfolio(['profile_picture' => 'portfolio-photos/portrait.jpg']);

        $this->assertSame(
            'https://cdn.example.test/storage/portfolio-photos/portrait.jpg',
            $portfolio->profile_picture_url,
        );
    }

    public function test_external_profile_picture_urls_remain_unchanged(): void
    {
        $portfolio = new Portfolio(['profile_picture' => 'https://images.example.test/portrait.jpg']);

        $this->assertSame('https://images.example.test/portrait.jpg', $portfolio->profile_picture_url);
    }

    public function test_profile_picture_url_is_null_when_no_picture_is_set(): void
    {
        $portfolio = new Portfolio;

        $this->assertNull($portfolio->profile_picture_url);
    }
}
