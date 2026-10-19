<?php

namespace Tests\Feature;

use App\Models\Portfolio;
use App\Models\User;
use Tests\TestCase;

class PortfolioDesignTest extends TestCase
{
    public function test_theme_picker_uses_distinct_styles_with_john_doe_as_the_sample_name(): void
    {
        $portfolio = new Portfolio([
            'full_name' => 'Actual Portfolio Owner',
            'template' => 'simple',
        ]);
        $portfolio->setAttribute('id', 123);

        $this->view('templates', compact('portfolio'))
            ->assertSee('John Doe')
            ->assertSee('ALEX MORGAN')
            ->assertSee('AM / 26')
            ->assertSee('Deep navy, electric blue')
            ->assertSee('Punchy color')
            ->assertDontSee('Actual Portfolio Owner');
    }

    public function test_owner_preview_has_one_prominent_toolbar_with_dashboard_back_link_and_contact_email(): void
    {
        $user = new User;
        $user->setAttribute('id', 42);
        $portfolio = new Portfolio([
            'user_id' => 42,
            'full_name' => 'John Doe',
            'email' => 'john@example.test',
            'template' => 'modern',
        ]);
        $portfolio->setAttribute('id', 123);
        $portfolio->setAttribute('slug', 'john-doe');

        $this->actingAs($user)
            ->view('portfolio', compact('portfolio'))
            ->assertSee('My portfolios')
            ->assertSee('Copy share link')
            ->assertSee('Change style')
            ->assertSee('Edit details')
            ->assertSee('href="#contact"', false)
            ->assertSee('id="contact"', false)
            ->assertSee('mailto:john@example.test')
            ->assertSee('john@example.test')
            ->assertDontSee('Back to my portfolios')
            ->assertDontSee('Let’s talk')
            ->assertDontSee('portfolio-contact-email');
    }
}
