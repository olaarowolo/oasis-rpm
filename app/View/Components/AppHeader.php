<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppHeader extends Component
{
    protected mixed $primaryActionConfig;

    public function __construct(
        public string $role,
        public ?string $pageTitle = null,
        public mixed $currentUser = null,
        public ?array $navigation = null,
        public ?string $subtitle = null,
        public mixed $breadcrumbs = null,
        mixed $primaryAction = null,
    ) {
        $this->primaryActionConfig = $primaryAction;
    }

    public function render(): View
    {
        $currentRoute = Route::currentRouteName();
        $currentTab = request()->query('tab');
        $currentUser = $this->currentUser ?? auth()->user();

        $roleConfig = [
            'student' => [
                'badge' => 'Student Portal',
                'roleLabel' => 'Research Student',
                'subtitle' => 'Student research workspace',
                'accent' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                'icon' => 'fa-user-graduate',
                'sidebar_id' => 'app-sidebar',
                'toggle_hidden_class' => 'md:hidden',
                'view_all_notifications_href' => route('student.dashboard', ['tab' => 'student-dashboard']),
                'navigation' => [
                    ['label' => 'Dashboard', 'href' => route('student.dashboard', ['tab' => 'student-dashboard']), 'icon' => 'fa-book-open-reader', 'match' => ['student.dashboard'], 'tab' => 'student-dashboard'],
                    ['label' => 'Roadmap', 'href' => route('student.dashboard', ['tab' => 'student-roadmap']), 'icon' => 'fa-list-check', 'match' => ['student.dashboard'], 'tab' => 'student-roadmap'],
                    ['label' => 'Proposals', 'href' => route('student.proposals'), 'icon' => 'fa-file-signature', 'match' => ['student.proposals'], 'fallback_tab' => 'student-proposals'],
                    ['label' => 'Meetings', 'href' => route('student.meetings'), 'icon' => 'fa-comments', 'match' => ['student.meetings']],
                    ['label' => 'Resources', 'href' => route('student.resources'), 'icon' => 'fa-graduation-cap', 'match' => ['student.resources']],
                    ['label' => 'Defense', 'href' => route('student.defense-readiness'), 'icon' => 'fa-shield-halved', 'match' => ['student.defense-readiness']],
                ],
                'menu_links' => [
                    ['label' => 'My Portal', 'href' => route('student.dashboard', ['tab' => 'student-dashboard']), 'icon' => 'fa-book-open-reader'],
                    ['label' => 'Profile Settings', 'href' => route('student.profile'), 'icon' => 'fa-user-gear'],
                ],
                'primary_actions' => [
                    'student.proposals' => ['label' => 'Open Proposal Lane', 'href' => route('student.dashboard', ['tab' => 'student-proposals']), 'icon' => 'fa-plus'],
                    'student.meetings' => ['label' => 'Open Meeting Workspace', 'href' => route('student.dashboard', ['tab' => 'student-meetings']), 'icon' => 'fa-calendar-plus'],
                    'student.resources' => ['label' => 'Track Progress', 'href' => route('student.dashboard', ['tab' => 'student-roadmap']), 'icon' => 'fa-chart-line'],
                    'student.defense-readiness' => ['label' => 'Review Roadmap', 'href' => route('student.dashboard', ['tab' => 'student-roadmap']), 'icon' => 'fa-list-check'],
                ],
            ],
            'supervisor' => [
                'badge' => 'Supervisor Hub',
                'roleLabel' => 'Authorized Supervisor',
                'subtitle' => 'Supervisor research management',
                'accent' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                'icon' => 'fa-user-shield',
                'sidebar_id' => 'app-sidebar',
                'toggle_hidden_class' => 'md:hidden',
                'view_all_notifications_href' => route('supervisor.dashboard'),
                'navigation' => [
                    ['label' => 'Dashboard', 'href' => route('supervisor.dashboard'), 'icon' => 'fa-chart-line', 'match' => ['supervisor.dashboard']],
                    ['label' => 'Students', 'href' => route('supervisor.students'), 'icon' => 'fa-users', 'match' => ['supervisor.students']],
                    ['label' => 'Proposals', 'href' => route('supervisor.proposals'), 'icon' => 'fa-file-signature', 'match' => ['supervisor.proposals']],
                    ['label' => 'Meetings', 'href' => route('supervisor.meetings'), 'icon' => 'fa-comments', 'match' => ['supervisor.meetings', 'supervisor.meetings.*']],
                    ['label' => 'Analytics', 'href' => route('supervisor.analytics'), 'icon' => 'fa-chart-pie', 'match' => ['supervisor.analytics']],
                    ['label' => 'Resources', 'href' => route('supervisor.resources.pending'), 'icon' => 'fa-check-to-mark', 'match' => ['supervisor.resources.pending']],
                    ['label' => 'Manuscripts', 'href' => route('supervisor.manuscripts'), 'icon' => 'fa-file-lines', 'match' => ['supervisor.manuscripts*']],
                ],
                'menu_links' => [
                    ['label' => 'Supervisor Dashboard', 'href' => route('supervisor.dashboard'), 'icon' => 'fa-chart-line'],
                    ['label' => 'Student Roster', 'href' => route('supervisor.students'), 'icon' => 'fa-users'],
                    ['label' => 'Meetings', 'href' => route('supervisor.meetings'), 'icon' => 'fa-comments'],
                ],
                'primary_actions' => [
                    'supervisor.meetings' => ['label' => 'Schedule Meeting', 'href' => route('supervisor.meetings.create'), 'icon' => 'fa-calendar-plus'],
                    'supervisor.meetings.create' => ['label' => 'Meeting Queue', 'href' => route('supervisor.meetings'), 'icon' => 'fa-comments'],
                    'supervisor.resources.pending' => ['label' => 'Review Submissions', 'href' => route('supervisor.resources.pending'), 'icon' => 'fa-check-to-mark'],
                    'supervisor.manuscripts' => ['label' => 'Review Manuscripts', 'href' => route('supervisor.manuscripts'), 'icon' => 'fa-file-lines'],
                    'supervisor.manuscripts.show' => ['label' => 'Review Manuscripts', 'href' => route('supervisor.manuscripts'), 'icon' => 'fa-file-lines'],
                    'supervisor.students' => ['label' => 'View Analytics', 'href' => route('supervisor.analytics'), 'icon' => 'fa-chart-pie'],
                ],
            ],
            'super-admin' => [
                'badge' => 'Super Admin',
                'roleLabel' => 'Platform Command Center',
                'subtitle' => 'Platform operations, governance, and configuration',
                'accent' => 'bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300',
                'icon' => 'fa-crown',
                'sidebar_id' => 'super-admin-sidebar',
                'toggle_hidden_class' => 'lg:hidden',
                'view_all_notifications_href' => route('super-admin.audit-logs'),
                'navigation' => [
                    ['label' => 'Dashboard', 'href' => route('super-admin.dashboard'), 'icon' => 'fa-gauge-high', 'match' => ['super-admin.dashboard']],
                    ['label' => 'Universities', 'href' => route('super-admin.universities'), 'icon' => 'fa-building-columns', 'match' => ['super-admin.universities*']],
                    ['label' => 'Users', 'href' => route('super-admin.users'), 'icon' => 'fa-users-gear', 'match' => ['super-admin.users*']],
                    ['label' => 'Audit Logs', 'href' => route('super-admin.audit-logs'), 'icon' => 'fa-clipboard-list', 'match' => ['super-admin.audit-logs']],
                    ['label' => 'System Status', 'href' => route('super-admin.system-status'), 'icon' => 'fa-server', 'match' => ['super-admin.system-status']],
                    ['label' => 'Config', 'href' => route('super-admin.config'), 'icon' => 'fa-gears', 'match' => ['super-admin.config']],
                    ['label' => 'Resources', 'href' => route('super-admin.resources'), 'icon' => 'fa-database', 'match' => ['super-admin.resources']],
                ],
                'menu_links' => [
                    ['label' => 'Dashboard', 'href' => route('super-admin.dashboard'), 'icon' => 'fa-gauge-high'],
                    ['label' => 'Universities', 'href' => route('super-admin.universities'), 'icon' => 'fa-building-columns'],
                    ['label' => 'System Config', 'href' => route('super-admin.config'), 'icon' => 'fa-gears'],
                ],
                'primary_actions' => [
                    'super-admin.universities' => ['label' => 'Add University', 'href' => route('super-admin.universities.create'), 'icon' => 'fa-building-circle-plus'],
                    'super-admin.users' => ['label' => 'Create User', 'href' => route('super-admin.users.create'), 'icon' => 'fa-user-plus'],
                    'super-admin.dashboard' => ['label' => 'Add University', 'href' => route('super-admin.universities.create'), 'icon' => 'fa-building-circle-plus'],
                ],
            ],
            'admin' => [
                'badge' => 'Admin',
                'roleLabel' => 'University Administrator',
                'subtitle' => 'University administration workspace',
                'accent' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                'icon' => 'fa-building-columns',
                'sidebar_id' => 'app-sidebar',
                'toggle_hidden_class' => 'md:hidden',
                'view_all_notifications_href' => route('admin.dashboard'),
                'navigation' => [
                    ['label' => 'Dashboard', 'href' => route('admin.dashboard'), 'icon' => 'fa-gauge-high', 'match' => ['admin.dashboard']],
                    ['label' => 'Users', 'href' => route('admin.users'), 'icon' => 'fa-users-gear', 'match' => ['admin.users']],
                    ['label' => 'Resources', 'href' => route('admin.resources'), 'icon' => 'fa-book-open', 'match' => ['admin.resources', 'admin.resources.*']],
                    ['label' => 'Audit Logs', 'href' => route('admin.audit-logs'), 'icon' => 'fa-clipboard-list', 'match' => ['admin.audit-logs', 'admin.audit-logs.*']],
                    ['label' => 'Configuration', 'href' => route('admin.config'), 'icon' => 'fa-sliders', 'match' => ['admin.config']],
                ],
                'menu_links' => [
                    ['label' => 'Dashboard', 'href' => route('admin.dashboard'), 'icon' => 'fa-gauge-high'],
                    ['label' => 'User Management', 'href' => route('admin.users'), 'icon' => 'fa-users-gear'],
                    ['label' => 'Audit Logs', 'href' => route('admin.audit-logs'), 'icon' => 'fa-clipboard-list'],
                ],
                'primary_actions' => [
                    'admin.users' => ['label' => 'Add User', 'href' => route('admin.users'), 'icon' => 'fa-user-plus'],
                    'admin.resources' => ['label' => 'Add Resource', 'href' => route('admin.resources.create'), 'icon' => 'fa-plus'],
                    'admin.dashboard' => ['label' => 'Manage Users', 'href' => route('admin.users'), 'icon' => 'fa-users-gear'],
                ],
            ],
        ];

        $config = $roleConfig[$this->role] ?? $roleConfig['student'];
        $navigation = $this->navigation ?: $config['navigation'];
        $navigation = array_map(function (array $item) use ($currentRoute, $currentTab) {
            $isActive = false;

            if (isset($item['tab'])) {
                $isActive = $currentRoute === ($item['match'][0] ?? null) && $currentTab === $item['tab'];
            } elseif (isset($item['fallback_tab'])) {
                $isActive = in_array($currentRoute, $item['match'], true) || ($currentRoute === 'student.dashboard' && $currentTab === $item['fallback_tab']);
            } else {
                foreach ($item['match'] as $pattern) {
                    if (request()->routeIs($pattern)) {
                        $isActive = true;
                        break;
                    }
                }
            }

            $item['active'] = $isActive;

            return $item;
        }, $navigation);

        $displayName = trim((string) ($currentUser?->name ?? ucfirst($this->role)));
        $parts = preg_split('/\s+/', $displayName, -1, PREG_SPLIT_NO_EMPTY) ?: [$displayName];
        $initials = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
        }

        if ($initials === '') {
            $initials = strtoupper(substr($displayName, 0, 2));
        }

        $resolvedPrimaryAction = $this->primaryActionConfig;
        if (is_string($resolvedPrimaryAction)) {
            $resolvedPrimaryAction = ['label' => $resolvedPrimaryAction, 'href' => '#', 'icon' => 'fa-bolt'];
        }

        if (!$resolvedPrimaryAction) {
            $resolvedPrimaryAction = $config['primary_actions'][$currentRoute] ?? null;
        }

        return view('components.app-header', [
            'config' => $config,
            'title' => $this->pageTitle ?: 'Research Supervision Portal',
            'subtitle' => $this->subtitle ?: $config['subtitle'],
            'currentUser' => $currentUser,
            'navigation' => $navigation,
            'menuLinks' => $config['menu_links'],
            'sidebarId' => $config['sidebar_id'] ?? 'app-sidebar',
            'mobileToggleHiddenClass' => $config['toggle_hidden_class'] ?? 'md:hidden',
            'notificationsViewAllHref' => $config['view_all_notifications_href'] ?? '#',
            'displayName' => $displayName,
            'initials' => $initials,
            'breadcrumbs' => $this->breadcrumbs,
            'resolvedPrimaryAction' => $resolvedPrimaryAction,
        ]);
    }
}