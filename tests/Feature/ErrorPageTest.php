<?php

namespace Tests\Feature;

use App\Mail\ErrorAlertMail;
use App\Services\ErrorAlertService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class ErrorPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.debug' => false,
            'error_reporting.enabled' => true,
            'error_reporting.environments' => ['testing'],
            'error_reporting.to' => 'tech@olaarowolo.com',
            'error_reporting.throttle_minutes' => 0,
            'error_reporting.max_per_hour' => 0,
            'error_reporting.page.countdown_seconds' => 10,
        ]);
    }

    protected function registerFailingRoute(string $path = '/boom'): void
    {
        Route::get($path, function () {
            throw new RuntimeException('A very technical failure detail');
        })->middleware('web');
    }

    public function test_a_server_error_renders_the_friendly_page(): void
    {
        Mail::fake();
        $this->registerFailingRoute();

        $response = $this->get('/boom');

        $response->assertStatus(500)
            ->assertSee('Something went wrong on our side')
            ->assertSee('This one is on us, not you')
            ->assertSee('SUP-');
    }

    public function test_the_friendly_page_never_leaks_internals(): void
    {
        Mail::fake();
        $this->registerFailingRoute();

        $response = $this->get('/boom');

        $response->assertDontSee('RuntimeException')
            ->assertDontSee('A very technical failure detail')
            ->assertDontSee(base_path());
    }

    public function test_the_friendly_page_offers_a_countdown_and_a_return_target(): void
    {
        Mail::fake();
        $this->registerFailingRoute();

        $response = $this->get('/boom');

        $response->assertSee('data-error-countdown-root', false)
            ->assertSee('data-error-seconds="10"', false)
            ->assertSee('data-error-return', false)
            ->assertSee('Cancel', false);
    }

    public function test_the_reference_code_matches_the_alert_email(): void
    {
        Mail::fake();
        $this->registerFailingRoute();

        $response = $this->get('/boom');

        preg_match('/SUP-[A-Z0-9]{4,12}/', $response->getContent(), $matches);
        $reference = $matches[0] ?? '';

        $this->assertNotSame('', $reference, 'The error page must show a reference code.');

        Mail::assertSent(ErrorAlertMail::class, function (ErrorAlertMail $mail) use ($reference) {
            return str_contains($mail->renderHtml(), $reference);
        });
    }

    public function test_a_missing_page_renders_its_own_copy(): void
    {
        Mail::fake();

        $response = $this->get('/definitely-not-a-real-page');

        $response->assertStatus(404)
            ->assertSee('We could not find that page');
    }

    public function test_a_forbidden_page_renders_its_own_copy(): void
    {
        Mail::fake();

        Route::get('/forbidden-page', function () {
            abort(403);
        })->middleware('web');

        $response = $this->get('/forbidden-page');

        $response->assertStatus(403)
            ->assertSee('You do not have access to this page');
    }

    public function test_json_requests_keep_the_api_envelope(): void
    {
        Mail::fake();
        $this->registerFailingRoute();

        $response = $this->getJson('/boom');

        $response->assertStatus(500)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Something went wrong on our side. Our support team has been notified.');
    }

    public function test_json_404_keeps_the_api_envelope(): void
    {
        Mail::fake();

        $response = $this->getJson('/api/does-not-exist');

        $response->assertStatus(404)
            ->assertJsonPath('success', false);
    }

    public function test_debug_mode_keeps_the_developer_exception_page(): void
    {
        config(['app.debug' => true]);

        $this->registerFailingRoute();

        $this->get('/boom')->assertStatus(500);
    }

    public function test_the_return_target_falls_back_to_the_landing_page_for_guests(): void
    {
        Mail::fake();
        $this->registerFailingRoute();

        $response = $this->get('/boom');

        $response->assertSee(route('landing'), false);
    }

    public function test_the_return_target_uses_the_referer_when_it_is_safe(): void
    {
        Mail::fake();
        $this->registerFailingRoute();

        $response = $this->withHeaders(['referer' => url('/student/dashboard')])->get('/boom');

        $response->assertSee(url('/student/dashboard'), false);
    }

    public function test_the_return_target_ignores_a_foreign_referer(): void
    {
        Mail::fake();
        $this->registerFailingRoute();

        $response = $this->withHeaders(['referer' => 'https://example.com/evil'])->get('/boom');

        $response->assertDontSee('https://example.com/evil', false);
    }

    public function test_the_return_target_never_points_back_at_the_failing_page(): void
    {
        Mail::fake();
        $this->registerFailingRoute();

        $response = $this->withHeaders(['referer' => url('/boom')])->get('/boom');

        $response->assertDontSee('href="'.url('/boom').'"', false);
    }

    public function test_a_user_report_emails_the_support_mailbox(): void
    {
        Mail::fake();
        $this->registerFailingRoute();

        $page = $this->get('/boom');

        preg_match('/SUP-[A-Z0-9]{4,12}/', $page->getContent(), $matches);
        $reference = $matches[0] ?? 'SUP-UNKNOWN';

        Mail::fake();

        $response = $this->postJson(route('support.error-report'), [
            'reference' => $reference,
            'user_note' => 'I was saving my proposal when the page stopped responding.',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Thank you. Report '.$reference.' is now with our support team.');

        Mail::assertSent(ErrorAlertMail::class, function (ErrorAlertMail $mail) {
            return $mail->hasTo('tech@olaarowolo.com')
                && str_contains($mail->resolveSubject(), 'USER REPORT')
                && str_contains($mail->renderHtml(), 'stopped responding');
        });
    }

    public function test_a_user_report_rejects_a_foreign_page_url(): void
    {
        Mail::fake();

        $response = $this->postJson(route('support.error-report'), [
            'reference' => 'SUP-ABC123',
            'page_url' => 'https://evil.example.com/phish',
        ]);

        $response->assertStatus(422)->assertJsonPath('success', false);

        Mail::assertNothingSent();
    }

    public function test_a_user_report_rejects_a_filled_honeypot(): void
    {
        Mail::fake();

        $response = $this->postJson(route('support.error-report'), [
            'reference' => 'SUP-ABC123',
            'website' => 'https://spam.example.com',
        ]);

        $response->assertStatus(422)->assertJsonPath('success', false);

        Mail::assertNothingSent();
    }

    public function test_a_user_report_rejects_a_malformed_reference(): void
    {
        Mail::fake();

        $response = $this->postJson(route('support.error-report'), [
            'reference' => 'not-a-reference',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('reference');

        Mail::assertNothingSent();
    }

    public function test_repeated_user_reports_are_throttled(): void
    {
        Mail::fake();

        for ($attempt = 0; $attempt < 3; $attempt++) {
            $this->postJson(route('support.error-report'), [
                'reference' => 'SUP-ABC123',
                'user_note' => 'Attempt '.$attempt,
            ])->assertOk();
        }

        $response = $this->postJson(route('support.error-report'), [
            'reference' => 'SUP-ABC123',
            'user_note' => 'Attempt 4',
        ]);

        $response->assertStatus(429);
    }

    public function test_a_missing_page_does_not_flood_the_mailbox(): void
    {
        Mail::fake();

        $this->get('/a-missing-page');
        $this->get('/another-missing-page');

        Mail::assertNothingSent();
    }

    public function test_not_found_exceptions_are_not_reported(): void
    {
        $this->assertFalse(app(ErrorAlertService::class)->shouldAlert(new NotFoundHttpException('Nope')));
    }
}
