<?php

return [
    'brand' => [
        'name' => 'AfriScribe Supervise',
        'tagline' => 'From topic to completion, with clarity.',
        'description' => 'A multi-tenant research supervision platform for universities, students, supervisors and research leaders.',
    ],

    'auth' => [
        'label' => 'Secure access',
        'subtitle' => 'Choose a role and continue to your supervision workspace.',
        'links' => [],
        'support' => [
            ['label' => 'Contact', 'route' => 'public.contact'],
            ['label' => 'Privacy', 'route' => 'public.privacy'],
        ],
    ],

    'public' => [
        'cta' => [
            [
                'label' => 'Log in',
                'route' => 'login',
                'style' => 'secondary',
            ],
            [
                'label' => 'Request a demo',
                'route' => 'login',
                'fragment' => 'request-demo',
                'style' => 'primary',
            ],
        ],
        'columns' => [
            [
                'heading' => 'Product',
                'links' => [
                    ['label' => 'Features', 'route' => 'public.features'],
                    ['label' => 'Pricing', 'route' => 'public.pricing'],
                    ['label' => 'Integrations', 'route' => 'public.integrations'],
                    ['label' => 'Changelog', 'route' => 'public.changelog'],
                    ['label' => 'Roadmap', 'route' => 'public.roadmap'],
                ],
            ],
            [
                'heading' => 'Solutions',
                'links' => [
                    ['label' => 'For students', 'route' => 'public.solutions.students'],
                    ['label' => 'For supervisors', 'route' => 'public.solutions.supervisors'],
                    ['label' => 'For administrators', 'route' => 'public.solutions.administrators'],
                    ['label' => 'For institutions', 'route' => 'public.solutions.institutions'],
                ],
            ],
            [
                'heading' => 'Resources',
                'links' => [
                    ['label' => 'Documentation', 'route' => 'public.docs'],
                    ['label' => 'API reference', 'route' => 'public.api-reference'],
                    ['label' => 'Case studies', 'route' => 'public.case-studies'],
                    ['label' => 'Webinars', 'route' => 'public.webinars'],
                    ['label' => 'Blog', 'route' => 'public.blog'],
                ],
            ],
            [
                'heading' => 'Company',
                'links' => [
                    ['label' => 'About', 'route' => 'public.about'],
                    ['label' => 'Careers', 'route' => 'public.careers'],
                    ['label' => 'Press', 'route' => 'public.press'],
                    ['label' => 'Contact', 'route' => 'public.contact'],
                    ['label' => 'Security', 'route' => 'public.security'],
                ],
            ],
        ],
        'legal' => [
            ['label' => 'Privacy', 'route' => 'public.privacy'],
            ['label' => 'Security', 'route' => 'public.security'],
            ['label' => 'Contact', 'route' => 'public.contact'],
        ],
        'external' => [
            [
                'label' => 'Website',
                'url' => env('WEBSITE_URL', env('APP_URL', 'http://localhost')),
                'icon' => 'fa-solid fa-globe',
            ],
            [
                'label' => 'GitHub',
                'url' => 'https://github.com/OlasunkanmiArowolo/OAsis-RS',
                'icon' => 'fa-brands fa-github',
            ],
        ],
    ],

    'roles' => [
        'student' => [
            'label' => 'Student',
            'subtitle' => 'Student research workspace',
            'links' => [
                ['label' => 'Dashboard', 'route' => 'student.dashboard', 'icon' => 'fa-gauge-high'],
                ['label' => 'Roadmap', 'route' => 'student.dashboard', 'parameters' => ['tab' => 'student-roadmap'], 'icon' => 'fa-list-check'],
                ['label' => 'Proposals', 'route' => 'student.proposals', 'icon' => 'fa-file-signature'],
                ['label' => 'Meetings', 'route' => 'student.meetings', 'icon' => 'fa-comments'],
                ['label' => 'Resources', 'route' => 'student.resources', 'icon' => 'fa-graduation-cap'],
                ['label' => 'Defence readiness', 'route' => 'student.defense-readiness', 'icon' => 'fa-shield-halved'],
                ['label' => 'Profile', 'route' => 'student.profile', 'icon' => 'fa-user-gear'],
            ],
            'support' => [
                ['label' => 'Help and support', 'route' => 'public.contact'],
                ['label' => 'Privacy', 'route' => 'public.privacy'],
            ],
        ],
        'supervisor' => [
            'label' => 'Supervisor',
            'subtitle' => 'Supervisor research management',
            'links' => [
                ['label' => 'Dashboard', 'route' => 'supervisor.dashboard', 'icon' => 'fa-gauge-high'],
                ['label' => 'Students', 'route' => 'supervisor.students', 'icon' => 'fa-users'],
                ['label' => 'Proposals', 'route' => 'supervisor.proposals', 'icon' => 'fa-file-signature'],
                ['label' => 'Meetings', 'route' => 'supervisor.meetings', 'icon' => 'fa-comments'],
                ['label' => 'Analytics', 'route' => 'supervisor.analytics', 'icon' => 'fa-chart-pie'],
                ['label' => 'Resource approvals', 'route' => 'supervisor.resources.pending', 'icon' => 'fa-check-to-mark'],
                ['label' => 'Manuscripts', 'route' => 'supervisor.manuscripts', 'icon' => 'fa-file-lines'],
            ],
            'support' => [
                ['label' => 'Help and support', 'route' => 'public.contact'],
                ['label' => 'Privacy', 'route' => 'public.privacy'],
            ],
        ],
        'admin' => [
            'label' => 'Admin',
            'subtitle' => 'University administration workspace',
            'links' => [
                ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'fa-gauge-high'],
                ['label' => 'Users', 'route' => 'admin.users', 'icon' => 'fa-users-gear'],
                ['label' => 'Resources', 'route' => 'admin.resources', 'icon' => 'fa-book-open'],
                ['label' => 'Audit logs', 'route' => 'admin.audit-logs', 'icon' => 'fa-clipboard-list'],
                ['label' => 'Configuration', 'route' => 'admin.config', 'icon' => 'fa-sliders'],
            ],
            'support' => [
                ['label' => 'Help and support', 'route' => 'public.contact'],
                ['label' => 'Privacy', 'route' => 'public.privacy'],
            ],
        ],
        'super_admin' => [
            'label' => 'Super Admin',
            'subtitle' => 'Platform operations, governance and configuration',
            'links' => [
                ['label' => 'Dashboard', 'route' => 'super-admin.dashboard', 'icon' => 'fa-gauge-high'],
                ['label' => 'Universities', 'route' => 'super-admin.universities', 'icon' => 'fa-building-columns'],
                ['label' => 'Users', 'route' => 'super-admin.users', 'icon' => 'fa-users-gear'],
                ['label' => 'Audit logs', 'route' => 'super-admin.audit-logs', 'icon' => 'fa-clipboard-list'],
                ['label' => 'Configuration', 'route' => 'super-admin.config', 'icon' => 'fa-gears'],
                ['label' => 'Resources', 'route' => 'super-admin.resources', 'icon' => 'fa-database'],
                ['label' => 'System status', 'route' => 'super-admin.system-status', 'icon' => 'fa-server'],
            ],
            'support' => [
                ['label' => 'Help and support', 'route' => 'public.contact'],
                ['label' => 'Privacy', 'route' => 'public.privacy'],
            ],
        ],
    ],
];
