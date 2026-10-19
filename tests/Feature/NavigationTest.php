<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mockery;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    public function test_guests_see_account_navigation_instead_of_portfolio_management_links(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Sign in')
            ->assertSee('Create account')
            ->assertDontSee('My portfolios')
            ->assertDontSee('New portfolio');
    }

    public function test_authenticated_users_have_a_separate_portfolio_dashboard(): void
    {
        $portfolios = Mockery::mock(HasMany::class);
        $portfolios->shouldReceive('latest')->once()->andReturnSelf();
        $portfolios->shouldReceive('get')->once()->andReturn(collect());

        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('portfolios')->once()->andReturn($portfolios);

        $this->actingAs($user)->get(route('home'))
            ->assertOk()
            ->assertDontSee('THE STUDIO')
            ->assertSee('My portfolios');

        $this->actingAs($user)->get(route('portfolios.index'))
            ->assertOk()
            ->assertSee('My portfolios')
            ->assertSee('Create my first portfolio')
            ->assertDontSee('Make your work');
    }

    public function test_portfolio_dashboard_requires_authentication(): void
    {
        $this->get(route('portfolios.index'))
            ->assertRedirect(route('login'));
    }
}
