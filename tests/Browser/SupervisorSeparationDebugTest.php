<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SupervisorSeparationDebugTest extends DuskTestCase
{
    public function test_check_login_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->pause(2000)
                ->screenshot("01-login-page-loaded");

            // Check if we can find the tab button by trying to see error
            try {
                $browser->assertPresent('#tab-btn-supervisor');
                echo "\n=== Supervisor Tab Found! ===\n";
            } catch (\Exception $e) {
                echo "\n=== Supervisor Tab NOT Found ===\n";
                echo "Error: " . $e->getMessage() . "\n";
            }

            try {
                $browser->assertPresent('#tab-btn-student');
                echo "\n=== Student Tab Found! ===\n";
            } catch (\Exception $e) {
                echo "\n=== Student Tab NOT Found ===\n";
            }

            try {
                $browser->assertPresent('#login-gate');
                echo "\n=== Login Gate Found! ===\n";
            } catch (\Exception $e) {
                echo "\n=== Login Gate NOT Found ===\n";
            }

            $browser->screenshot("02-after-checks");
        });
    }
}
