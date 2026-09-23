<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UniversityLoadingDebugTest extends DuskTestCase
{
    public function test_universities_load(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->pause(3000)  // Give time for universities to load
                ->screenshot("01-page-loaded");

            // Check if universities are in the cache
            $cacheInfo = $browser->script("return {cache: window.universitiesCache, type: typeof window.universitiesCache}");
            echo "\nUniversities cache info:\n";
            echo json_encode($cacheInfo, JSON_PRETTY_PRINT) . "\n";

            // Check if universities are in the dropdown
            $browser->click('#tab-btn-supervisor')
                ->pause(500);

            $browser->click('#login-university-code-supervisor')
                ->pause(1000)
                ->screenshot("02-dropdown-opened");

            // Check dropdown status
            $dropdownStatus = $browser->script("
                const dropdown = document.getElementById('university-dropdown-supervisor');
                const list = document.getElementById('university-list-supervisor');
                return {
                    dropdownHasHidden: dropdown?.classList.contains('hidden'),
                    listChildren: list?.children?.length || 0,
                    listHTMLStart: list?.innerHTML?.substring(0, 150) || 'NO LIST'
                };
            ");
            
            echo "\nDropdown status:\n";
            echo json_encode($dropdownStatus, JSON_PRETTY_PRINT) . "\n";
        });
    }
}
