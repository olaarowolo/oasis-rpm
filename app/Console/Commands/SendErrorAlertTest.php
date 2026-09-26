<?php

namespace App\Console\Commands;

use App\Services\ErrorAlertService;
use Illuminate\Console\Command;
use RuntimeException;

class SendErrorAlertTest extends Command
{
    protected $signature = 'errors:test
                            {--user-report : Send a user report instead of an automatic alert}
                            {--note=What the user was doing : Note attached to a user report}';

    protected $description = 'Send a test error alert to the configured support mailbox';

    public function handle(ErrorAlertService $errorAlerts): int
    {
        $userReport = (bool) $this->option('user-report');

        $recipients = $errorAlerts->recipients();
        $from = (string) config('error_reporting.from_address');

        $this->components->info('Error alerting configuration');
        $this->line('  Environment : '.app()->environment());
        $this->line('  Enabled     : '.(config('error_reporting.enabled') ? 'yes' : 'no'));
        $this->line('  Mailer      : '.config('mail.default').' ('.config('mail.host').')');
        $this->line('  From        : '.$from);
        $this->line('  To          : '.($recipients === [] ? '(none configured)' : implode(', ', $recipients)));
        $this->line('  Throttle    : '.config('error_reporting.throttle_minutes').' min per fingerprint, '.config('error_reporting.max_per_hour').' per hour');

        if ($recipients === []) {
            $this->components->error('No recipient configured. Set ERROR_ALERT_TO.');

            return self::FAILURE;
        }

        $exception = new RuntimeException($userReport
            ? 'Synthetic user report — no action required.'
            : 'Synthetic error alert — no action required.');

        $context = $errorAlerts->buildContext($exception);

        if ($userReport) {
            $context['source'] = 'user-report';
            $context['user_note'] = (string) $this->option('note');
        }

        $this->line('  Reference   : '.$context['reference_code']);
        $this->components->info('Sending…');

        $errorAlerts->storeContext($context);
        $errorAlerts->send($context);

        $this->components->info('Done. Check '.$recipients[0].' and the mail transport log.');

        return self::SUCCESS;
    }
}
