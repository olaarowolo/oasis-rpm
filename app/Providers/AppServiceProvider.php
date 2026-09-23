<?php

namespace App\Providers;

use App\Models\Student;
use App\Models\Supervisor;
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
    }
}
