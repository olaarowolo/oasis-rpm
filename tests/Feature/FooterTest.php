<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class FooterTest extends TestCase
{
    public function test_public_footer_renders_mega_navigation_and_conversion_actions(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Public footer navigation', false)
            ->assertSee('Request a demo', false)
            ->assertSee('For students', false)
            ->assertSee('Privacy', false)
            ->assertDontSee('Student research workspace', false);
    }

    public function test_authenticated_visitor_gets_role_specific_footer_on_public_page(): void
    {
        $response = $this->withSession([
            'user_id' => 1,
            'role' => 'student',
            'university_id' => 1,
        ])->get('/');

        $response->assertOk()
            ->assertSee('Student research workspace', false)
            ->assertDontSee('Public footer navigation', false);
    }

    public function test_authenticated_footer_renders_role_specific_actions(): void
    {
        $html = view('components.layouts.footer', [
            'variant' => 'authenticated',
            'role' => 'student',
            'scope' => 'University scope',
        ])->render();

        $this->assertStringContainsString('Student research workspace', $html);
        $this->assertStringContainsString('Defence readiness', $html);
        $this->assertStringContainsString('Help and support', $html);
        $this->assertStringNotContainsString('Platform operations', $html);
    }

    public function test_super_admin_footer_renders_platform_status_and_governance_actions(): void
    {
        $html = view('components.layouts.footer', [
            'variant' => 'authenticated',
            'role' => 'super_admin',
            'scope' => 'Platform-wide',
        ])->render();

        $this->assertStringContainsString('Platform operations', $html);
        $this->assertStringContainsString('System status', $html);
        $this->assertStringContainsString('All systems operational', $html);
        $this->assertStringContainsString('Universities', $html);
    }

    public function test_all_configured_footer_routes_are_registered(): void
    {
        $groups = [
            config('footer.public.cta', []),
            config('footer.public.legal', []),
            array_column(config('footer.public.columns', []), 'links'),
            array_column(config('footer.roles', []), 'links'),
            array_column(config('footer.roles', []), 'support'),
        ];

        $links = [];
        foreach ($groups as $group) {
            foreach ($group as $item) {
                if (isset($item['route'])) {
                    $links[] = $item;
                }

                if (isset($item['links'])) {
                    array_push($links, ...$item['links']);
                }
            }
        }

        foreach ($links as $link) {
            $this->assertTrue(Route::has($link['route']), "Missing footer route: {$link['route']}");
        }
    }
}
