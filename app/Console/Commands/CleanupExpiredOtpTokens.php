<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OtpToken;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanupExpiredOtpTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:cleanup
                            {--force : Force the operation to run without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired OTP tokens from the database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!$this->option('force') && !$this->confirm('This will delete all expired OTP tokens. Continue?')) {
            return self::FAILURE;
        }

        $startTime = now();
        $totalDeleted = 0;

        // Delete expired tokens in batches to avoid memory issues
        $batchSize = 1000;
        $batchDeleted = $batchSize;

        while ($batchDeleted >= $batchSize) {
            $expiredTokens = OtpToken::where('expires_at', '<', now())
                ->orWhere('used_at', '!=', null)
                ->take($batchSize)
                ->get();

            $batchDeleted = $expiredTokens->count();

            if ($batchDeleted > 0) {
                $totalDeleted += $batchDeleted;
                OtpToken::whereIn('id', $expiredTokens->pluck('id'))->delete();
            }
        }

        $executionTime = now()->diffInSeconds($startTime);
        
        Log::info('OTP cleanup completed', [
            'total_deleted' => $totalDeleted,
            'execution_time_seconds' => $executionTime,
        ]);

        $this->info("Successfully deleted {$totalDeleted} expired OTP tokens in {$executionTime} seconds");

        return self::SUCCESS;
    }
}
