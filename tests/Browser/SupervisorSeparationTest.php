<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SupervisorSeparationTest extends DuskTestCase
{
    /**
     * Test that two supervisors can login separately and see only their own students
     */
    public function test_supervisors_see_only_their_own_students(): void
    {
        // Test NG Supervisor (has 20 students)
        $this->loginSupervisorAndVerifyStudents(
            'olaarowolo.ng@gmail.com',
            '2026',
            'LASU-Arowolo-2026',
            20,
            'NG Supervisor'
        );

        // Add delay to avoid rate limiting
        sleep(5);

        // Test UK Supervisor (has 0 students)
        $this->loginSupervisorAndVerifyStudents(
            'olaarowolo.uk@gmail.com',
            '2026',
            'UK-Arowolo-2026',
            0,
            'UK Supervisor'
        );
    }

    protected function loginSupervisorAndVerifyStudents(
        string $email,
        string $pin,
        string $passphrase,
        int $expectedStudentCount,
        string $label
    ): void {
        $this->browse(function (Browser $browser) use ($email, $pin, $passphrase, $expectedStudentCount, $label) {
            // Ensure each scenario starts from a clean auth state.
            $browser->driver->manage()->deleteAllCookies();

            // Navigate to login page
            $browser->visit('/login')
                ->pause(2000)
                ->screenshot("01-{$label}-start");

            // Click on Supervisor Login tab using JS to avoid click interception flakiness.
            $browser->script("const supervisorTab = document.getElementById('tab-btn-supervisor'); if (supervisorTab) { supervisorTab.click(); }");
            $browser->pause(1000)
                ->screenshot("02-{$label}-supervisor-tab");

            // Click on university input to open dropdown
            $browser->click('#login-university-code-supervisor')
                ->pause(1000)
                ->screenshot("03-{$label}-university-dropdown-opened");

            // Type LASU to filter
            $browser->type('#university-search-supervisor', 'LASU')
                ->pause(1000)
                ->screenshot("04-{$label}-after-typing-lasu");

            // Find and click the LASU option using JavaScript to call selectUniversity
            $browser->script("
                const button = Array.from(document.querySelectorAll('#university-list-supervisor button'))
                    .find(btn => btn.textContent.includes('LASU'));
                if (button) {
                    button.click();
                    console.log('LASU button clicked');
                } else {
                    console.log('LASU button not found');
                    console.log('Available buttons:', document.querySelectorAll('#university-list-supervisor button').length);
                }
            ");
            
            $browser->pause(500)
                ->screenshot("05-{$label}-university-selected");

            // Verify university was selected
            $selected = $browser->inputValue('#login-university-code-supervisor');
            echo "\n{$label}: Selected university: {$selected}\n";

            // Fill in email
            $browser->type('#login-supervisor-email', $email)
                ->pause(300)
                ->screenshot("06-{$label}-email-filled");

            // Fill in PIN
            $browser->type('#login-supervisor-pin', $pin)
                ->pause(300)
                ->screenshot("07-{$label}-pin-filled");

            // Fill in Passphrase
            $browser->type('#login-supervisor-passphrase', $passphrase)
                ->pause(300)
                ->screenshot("08-{$label}-passphrase-filled");

            // Submit form by calling the handler directly
            $browser->script("
                handleSupervisorLoginDirect({ preventDefault: function() {} });
                console.log('handleSupervisorLoginDirect called directly');
            ");
            
            $browser->pause(3000)
                ->screenshot("09-{$label}-after-submit");

            // Check if we got an error message
            try {
                $browser->assertVisible('#supervisor-login-error');
                $errorText = $browser->text('#supervisor-login-error-text');
                throw new \Exception("Login failed with error: {$errorText}");
            } catch (\Exception $e) {
                if (strpos($e->getMessage(), 'not present') === false && strpos($e->getMessage(), 'Login failed') !== false) {
                    throw $e;
                }
                // No error visible, continue
            }

            // Check if still on login page
            try {
                $browser->assertPresent('#login-gate');
                echo "\n{$label}: Still on login page after form submission\n";
                $browser->screenshot("09a-{$label}-still-on-login");
                // Check if the gate is visible (not redirected)
                $stillVisible = $browser->script("return document.getElementById('login-gate').style.display !== 'none' && !document.getElementById('login-gate').classList.contains('hidden')");
                echo "{$label}: Login gate still visible: " . ($stillVisible ? 'YES' : 'NO') . "\n";
            } catch (\Exception $e) {
                echo "\n{$label}: Redirected away from login gate\n";
            }

            // Wait for dashboard to load
            try {
                // Wait for the supervisor dashboard page to load (look for key element)
                $browser->waitFor('[data-metric="total_students"]', 30)
                    ->pause(2000)
                    ->screenshot("10-{$label}-dashboard-loaded");

                // Get the total students metric displayed on the page
                $totalStudentsText = $browser->text('[data-metric="total_students"]');
                $totalStudents = (int) trim($totalStudentsText);
                
                echo "{$label} ({$email}):\n";
                echo "  Expected Students: {$expectedStudentCount}\n";
                echo "  Actual Students: {$totalStudents}\n";

                $this->assertEquals(
                    $expectedStudentCount,
                    $totalStudents,
                    "{$label} should have {$expectedStudentCount} students, but found {$totalStudents}"
                );

                $browser->screenshot("11-{$label}-students-confirmed");
            } catch (\Exception $e) {
                echo "\n{$label}: Error waiting for dashboard: " . $e->getMessage() . "\n";
                // Take a screenshot to see what state the page is in
                $browser->screenshot("10-{$label}-error-state");
                throw $e;
            }

            // Take final screenshot
            $browser->screenshot("12-{$label}-final");

            // Cleanup auth state for the next scenario
            $browser->driver->manage()->deleteAllCookies();
        });
    }
}
