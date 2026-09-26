<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Mail\ErrorAlertMail;
use App\Services\ErrorAlertService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class ErrorAlertTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'error_reporting.enabled' => true,
            'error_reporting.environments' => ['testing'],
            'error_reporting.to' => 'tech@olaarowolo.com',
            'error_reporting.throttle_minutes' => 10,
            'error_reporting.max_per_hour' => 0,
        ]);
    }

    public function test_a_reported_exception_emails_the_support_mailbox(): void
    {
        Mail::fake();

        $service = app(ErrorAlertService::class);
        $service->handle(new RuntimeException('Payment gateway exploded'));

        Mail::assertSentCount(1);
        Mail::assertSent(ErrorAlertMail::class, function (ErrorAlertMail $mail) {
            return $mail->hasTo('tech@olaarowolo.com')
                && str_contains($mail->resolveSubject(), 'SUP-')
                && str_contains($mail->resolveSubject(), 'Payment gateway exploded');
        });
    }

    public function test_alerts_are_suppressed_outside_configured_environments(): void
    {
        Mail::fake();
        config(['error_reporting.environments' => ['production']]);

        app(ErrorAlertService::class)->handle(new RuntimeException('Local noise'));

        Mail::assertNothingSent();
    }

    public function test_ignored_exception_classes_are_never_emailed(): void
    {
        Mail::fake();

        app(ErrorAlertService::class)->handle(
            new NotFoundHttpException('Missing page')
        );

        Mail::assertNothingSent();
    }

    public function test_the_kill_switch_suppresses_alerts(): void
    {
        Mail::fake();
        config(['error_reporting.enabled' => false]);

        app(ErrorAlertService::class)->handle(new RuntimeException('Suppressed'));

        Mail::assertNothingSent();
    }

    /**
     * Exception file and line are what the fingerprint is built from, so a
     * test needs to place the throw at a chosen line to simulate distinct
     * failures.
     */
    protected function exceptionAt(string $message, int $line): RuntimeException
    {
        $exception = new RuntimeException($message);

        foreach (['file' => __FILE__, 'line' => $line] as $property => $value) {
            $reflection = new \ReflectionProperty(\Exception::class, $property);
            $reflection->setAccessible(true);
            $reflection->setValue($exception, $value);
        }

        return $exception;
    }

    public function test_repeated_identical_exceptions_are_throttled(): void
    {
        Mail::fake();
        config(['error_reporting.throttle_minutes' => 10, 'error_reporting.max_per_hour' => 0]);

        $service = app(ErrorAlertService::class);
        $exception = $this->exceptionAt('Same failure', 42);

        $service->handle($exception);
        $service->handle($exception);

        Mail::assertSentCount(1);
    }

    public function test_a_throttled_exception_alerts_again_after_the_window(): void
    {
        Mail::fake();
        config(['error_reporting.throttle_minutes' => 1, 'error_reporting.max_per_hour' => 0]);

        $service = app(ErrorAlertService::class);
        $exception = $this->exceptionAt('Recurring failure', 43);

        $service->handle($exception);
        $this->travel(2)->minutes();
        $service->handle($exception);

        Mail::assertSentCount(2);
    }

    public function test_the_hourly_ceiling_caps_alert_volume(): void
    {
        Mail::fake();
        config(['error_reporting.throttle_minutes' => 0, 'error_reporting.max_per_hour' => 2]);

        $service = app(ErrorAlertService::class);

        for ($i = 0; $i < 5; $i++) {
            $service->handle($this->exceptionAt('Failure '.$i, 100 + $i));
        }

        Mail::assertSentCount(2);
    }

    public function test_sensitive_request_input_is_redacted_in_the_alert(): void
    {
        Mail::fake();

        Route::post('/error-alert-test', function () {
            throw new RuntimeException('Form submission failed');
        })
            ->middleware('web')
            ->withoutMiddleware([VerifyCsrfToken::class]);

        $response = $this->post('/error-alert-test', [
            'topic' => 'AI and student supervision',
            'password' => 'SuperSecret123',
            'otp' => '998877',
            '_token' => 'csrf-token-value',
        ]);

        $response->assertStatus(500);

        Mail::assertSent(ErrorAlertMail::class, function (ErrorAlertMail $mail) {
            $html = $mail->renderHtml();

            return str_contains($html, 'AI and student supervision')
                && str_contains($html, '[redacted]')
                && ! str_contains($html, 'SuperSecret123')
                && ! str_contains($html, '998877')
                && ! str_contains($html, 'csrf-token-value');
        });
    }

    public function test_the_reference_code_is_stable_for_the_same_failure(): void
    {
        $service = app(ErrorAlertService::class);

        $first = $this->exceptionAt('One', 200);
        $second = $this->exceptionAt('Two', 201);
        $third = $this->exceptionAt('Three', 202);

        $this->assertSame($service->referenceCode($first), $service->referenceCode($first));
        $this->assertNotSame($service->referenceCode($first), $service->referenceCode($second));
        $this->assertNotSame($service->referenceCode($first), $service->referenceCode($third));
        $this->assertMatchesRegularExpression('/^SUP-[A-Z0-9]{4,12}$/', $service->referenceCode($first));
    }

    public function test_alert_context_is_cached_for_later_user_reports(): void
    {
        $service = app(ErrorAlertService::class);
        $context = $service->buildContext(new RuntimeException('Cached failure'));

        $service->storeContext($context);

        $stored = $service->storedContext($context['reference_code']);

        $this->assertIsArray($stored);
        $this->assertSame('Cached failure', $stored['message']);
    }

    public function test_a_failing_mailer_never_surfaces_to_the_caller(): void
    {
        Mail::shouldReceive('to')->andThrow(new RuntimeException('SMTP is down'));

        $service = app(ErrorAlertService::class);
        $service->handle(new RuntimeException('Original failure'));

        $this->assertTrue(true);
    }

    public function test_alerts_stop_when_no_recipient_is_configured(): void
    {
        Mail::fake();
        config(['error_reporting.to' => '']);

        app(ErrorAlertService::class)->handle(new RuntimeException('Nowhere to send this'));

        Mail::assertNothingSent();
    }

    public function test_the_test_command_sends_a_synthetic_alert(): void
    {
        Mail::fake();
        config(['mail.default' => 'array']);

        $this->artisan('errors:test')
            ->expectsOutputToContain('tech@olaarowolo.com')
            ->assertSuccessful();

        Mail::assertSent(ErrorAlertMail::class, function (ErrorAlertMail $mail) {
            return $mail->hasTo('tech@olaarowolo.com')
                && str_contains($mail->resolveSubject(), 'ERROR');
        });
    }

    public function test_the_test_command_can_send_a_user_report(): void
    {
        Mail::fake();
        config(['mail.default' => 'array']);

        $this->artisan('errors:test', [
            '--user-report' => true,
            '--note' => 'Editing my proposal',
        ])->assertSuccessful();

        Mail::assertSent(ErrorAlertMail::class, function (ErrorAlertMail $mail) {
            return str_contains($mail->resolveSubject(), 'USER REPORT')
                && str_contains($mail->renderHtml(), 'Editing my proposal');
        });
    }
}
