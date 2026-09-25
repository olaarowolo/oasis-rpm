<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SupervisorLoginDebugTest extends DuskTestCase
{
    public function test_supervisor_login_flow(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->pause(1000)
                ->screenshot("01-login-page");

            // Click supervisor tab
            $browser->click('#tab-btn-supervisor')
                ->pause(500)
                ->screenshot("02-supervisor-tab-clicked");

            // Check if supervisor box is visible
            try {
                $browser->assertVisible('#gate-supervisor-box');
                echo "\n✓ Supervisor box is visible\n";
            } catch (\Exception $e) {
                echo "\n✗ Supervisor box not visible: " . $e->getMessage() . "\n";
            }

            // Check form elements
            try {
                $browser->assertVisible('#login-university-code-supervisor');
                echo "✓ University input is visible\n";
            } catch (\Exception $e) {
                echo "✗ University input not visible: " . $e->getMessage() . "\n";
            }

            try {
                $browser->assertVisible('#login-supervisor-email');
                echo "✓ Email input is visible\n";
            } catch (\Exception $e) {
                echo "✗ Email input not visible: " . $e->getMessage() . "\n";
            }

            // Try to select university
            $browser->click('#login-university-code-supervisor')
                ->pause(1500)
                ->type('#university-search-supervisor', 'LASU')
                ->pause(500)
                ->screenshot("03-university-search");

            // Click the LASU option
            $browser->script("
                const options = document.querySelectorAll('#university-list-supervisor button');
                console.log('Found options:', options.length);
                const lasuOption = Array.from(options).find(el => el.textContent.includes('LASU'));
                if (lasuOption) {
                    console.log('Clicking LASU option');
                    lasuOption.click();
                } else {
                    console.log('LASU option not found');
                }
            ");
            
            $browser->pause(500)
                ->screenshot("04-after-university-select");

            // Check if university was selected
            $university = $browser->inputValue('#login-university-code-supervisor');
            echo "\nUniversity value: {$university}\n";

            // Fill in the email (email step is visible by default)
            $browser->type('#login-supervisor-email', 'olaarowolo.ng@gmail.com')
                ->pause(300)
                ->screenshot("05-email-filled");

            // Show credentials step (PIN/passphrase are in a hidden step)
            $browser->script("showSupervisorCredentialsStep();");

            // Fill in PIN and passphrase
            $browser->type('#login-supervisor-pin', '2026')
                ->pause(300)
                ->type('#login-supervisor-passphrase', 'LASU-Arowolo-2026')
                ->pause(300)
                ->screenshot("05-form-filled");

            // Try to submit - check for JavaScript errors first
            $browser->script("
                console.log('Form submit handler exists:', typeof handleSupervisorLoginDirect);
                const form = document.getElementById('gate-supervisor-login-form');
                console.log('Form element:', form);
                console.log('Form onsubmit:', form?.onsubmit);
            ");

            // Submit form by calling the handler directly (same pattern as SupervisorSeparationTest)
            $browser->script("
                handleSupervisorLoginDirect({ preventDefault: function() {} });
                console.log('handleSupervisorLoginDirect called directly');
            ");
            
            $browser->pause(3000)
                ->screenshot("06-after-submit");

            // Check if there's an error message
            try {
                $browser->assertVisible('#supervisor-login-error');
                $errorText = $browser->text('#supervisor-login-error-text');
                echo "\nError message appeared: {$errorText}\n";
            } catch (\Exception $e) {
                echo "\nNo error message visible\n";
            }

            // Check if we're still on the login page or redirected
            try {
                $browser->assertPresent('#login-gate');
                echo "Still on login page (gate visible)\n";
            } catch (\Exception $e) {
                echo "Redirected away from login page\n";
            }
        });
    }
}
