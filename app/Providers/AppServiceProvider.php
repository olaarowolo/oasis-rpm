<?php

namespace App\Providers;

use App\Models\Student;
use App\Models\Supervisor;
use App\Models\User;
use Doctrine\DBAL\Types\Type;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (DB::connection() instanceof MySqlConnection && class_exists(Type::class)) {
            DB::connection()
                ->getDoctrineConnection()
                ->getDatabasePlatform()
                ->registerDoctrineTypeMapping('enum', 'string');
        }

        if ($this->app->environment('local') && config('mail.simulation.enabled', false)) {
            Mail::alwaysTo(config('mail.simulation.address', 'olasunkanmiarowolo@gmail.com'));
        }

        $resolveSupervisorSidebarData = function (): array {
            $profile = [
                'bookingUrl' => null,
                'supervisorName' => 'Supervisor',
                'supervisorTitle' => 'Research Supervisor',
                'supervisorDepartment' => 'Department',
                'supervisorEmail' => 'supervisor@lasu.edu.ng',
                'supervisorInitials' => 'SV',
                'researchAreas' => null,
                'universityName' => null,
            ];

            $supervisor = null;
            $studentId = session('student_id');
            $supervisorId = session('supervisor_id');

            if ($studentId) {
                $student = Student::query()
                    ->select(['id', 'supervisor_id'])
                    ->with([
                        'supervisor:id,user_id,university_id,title,department,research_areas,booking_url',
                        'supervisor.user:id,name,email',
                        'supervisor.university:id,name',
                    ])
                    ->find($studentId);

                $supervisor = $student?->supervisor;
            } elseif ($supervisorId) {
                $supervisor = Supervisor::query()
                    ->select(['id', 'user_id', 'university_id', 'title', 'department', 'research_areas', 'booking_url'])
                    ->with(['user:id,name,email', 'university:id,name'])
                    ->find($supervisorId);
            }

            if (!$supervisor) {
                return $profile;
            }

            $profile['bookingUrl'] = $supervisor->booking_url;
            $profile['supervisorName'] = $supervisor->user?->name ?: $profile['supervisorName'];
            $profile['supervisorTitle'] = $supervisor->title ?: $profile['supervisorTitle'];
            $profile['supervisorDepartment'] = $supervisor->department ?: $profile['supervisorDepartment'];
            $profile['supervisorEmail'] = $supervisor->user?->email ?: $profile['supervisorEmail'];
            $profile['researchAreas'] = $supervisor->research_areas;
            $profile['universityName'] = $supervisor->university?->name;

            $parts = preg_split('/\s+/', trim($profile['supervisorName'])) ?: [];
            $letters = collect($parts)
                ->filter()
                ->take(2)
                ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                ->implode('');

            if ($letters !== '') {
                $profile['supervisorInitials'] = $letters;
            }

            return $profile;
        };

        View::composer('partials.dashboards.student-sidebar', function ($view) use ($resolveSupervisorSidebarData) {
            $profile = $resolveSupervisorSidebarData();

            $view->with([
                'studentSidebarBookingUrl' => $profile['bookingUrl'],
                'studentSidebarSupervisorName' => $profile['supervisorName'],
                'studentSidebarSupervisorTitle' => $profile['supervisorTitle'],
                'studentSidebarSupervisorDepartment' => $profile['supervisorDepartment'],
                'studentSidebarSupervisorEmail' => $profile['supervisorEmail'],
                'studentSidebarSupervisorInitials' => $profile['supervisorInitials'],
            ]);
        });

        View::composer(['components.sidebar', 'supervisor.partials.sidebar'], function ($view) use ($resolveSupervisorSidebarData) {
            $view->with('sidebarSupervisorProfile', $resolveSupervisorSidebarData());
        });

        $resolveAdminShellData = function (): array {
            $profile = [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'initials' => 'AD',
                'roleLabel' => 'Admin',
                'universityName' => 'All universities',
            ];

            $user = User::query()->with('university')->find(session('user_id'));
            if (! $user) {
                return $profile;
            }

            $profile['name'] = $user->name ?: $profile['name'];
            $profile['email'] = $user->email ?: $profile['email'];
            $profile['roleLabel'] = $user->role === 'super_admin' ? 'Super Admin' : 'Admin';
            $profile['universityName'] = $user->university?->name ?: $profile['universityName'];

            $parts = preg_split('/\s+/', trim($profile['name'])) ?: [];
            $letters = collect($parts)
                ->filter()
                ->take(2)
                ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                ->implode('');

            if ($letters !== '') {
                $profile['initials'] = $letters;
            }

            return $profile;
        };

        View::composer(['layouts.admin', 'admin.partials.sidebar'], function ($view) use ($resolveAdminShellData) {
            $view->with('adminShellProfile', $resolveAdminShellData());
        });
    }
}
