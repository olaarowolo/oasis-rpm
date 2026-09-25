<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\BaseController;
use Illuminate\View\View;

class PublicPageController extends BaseController
{
    public function features(): View
    {
        return $this->renderPage('features');
    }

    public function pricing(): View
    {
        return $this->renderPage('pricing');
    }

    public function integrations(): View
    {
        return $this->renderPage('integrations');
    }

    public function changelog(): View
    {
        return $this->renderPage('changelog');
    }

    public function roadmap(): View
    {
        return $this->renderPage('roadmap');
    }

    public function solutionsSupervisors(): View
    {
        return $this->renderPage('solutions.supervisors');
    }

    public function solutionsStudents(): View
    {
        return $this->renderPage('solutions.students');
    }

    public function solutionsAdministrators(): View
    {
        return $this->renderPage('solutions.administrators');
    }

    public function solutionsInstitutions(): View
    {
        return $this->renderPage('solutions.institutions');
    }

    public function docs(): View
    {
        return $this->renderPage('docs');
    }

    public function blog(): View
    {
        return $this->renderPage('blog');
    }

    public function webinars(): View
    {
        return $this->renderPage('webinars');
    }

    public function caseStudies(): View
    {
        return $this->renderPage('case-studies');
    }

    public function apiReference(): View
    {
        return $this->renderPage('api-reference');
    }

    public function about(): View
    {
        return $this->renderPage('about');
    }

    public function careers(): View
    {
        return $this->renderPage('careers');
    }

    public function press(): View
    {
        return $this->renderPage('press');
    }

    public function contact(): View
    {
        return $this->renderPage('contact');
    }

    public function security(): View
    {
        return $this->renderPage('security');
    }

    public function privacy(): View
    {
        return $this->renderPage('privacy');
    }

    protected function renderPage(string $key): View
    {
        $page = $this->pageCatalog()[$key] ?? abort(404);

        return view('public.page', $page);
    }

    protected function pageCatalog(): array
    {
        return [
            'features' => [
                'pageTitle' => 'Features | TheOAsis Research Portal',
                'pageDescription' => 'Discover all features of TheOAsis Research Supervision Portal: 12-stage lifecycle tracking, meeting logs, supervisor analytics, proposal governance, AI research assistant, and multi-tenant isolation.',
                'eyebrow' => 'Product',
                'heading' => 'A Supervision Operating System Built For Research Progress',
                'intro' => 'TheOAsis connects proposals, stage progression, resources, meetings, and feedback into one academic workflow instead of five disconnected tools.',
                'sections' => [
                    ['icon' => 'fa-road', 'title' => '12-stage lifecycle tracking', 'description' => 'Every milestone is visible from topic approval to sign-off.', 'icon_bg' => 'bg-blue-100', 'icon_color' => 'text-blue-700'],
                    ['icon' => 'fa-comments', 'title' => 'Structured meeting logs', 'description' => 'Students and supervisors keep a durable review trail instead of scattered chats.', 'icon_bg' => 'bg-amber-100', 'icon_color' => 'text-amber-700'],
                    ['icon' => 'fa-chart-line', 'title' => 'Actionable analytics', 'description' => 'Identify stagnation early and manage cohorts with less manual reporting.', 'icon_bg' => 'bg-emerald-100', 'icon_color' => 'text-emerald-700'],
                ],
                'status' => 'Designed for universities that want a measurable, governed research supervision workflow.',
            ],
            'pricing' => [
                'pageTitle' => 'Pricing | TheOAsis Research Portal',
                'pageDescription' => 'Simple, transparent pricing for universities and research institutions. Choose the plan that fits your supervision needs.',
                'eyebrow' => 'Pricing',
                'heading' => 'Platform Pricing For Supervision At Department Or Institution Scale',
                'intro' => 'Choose a rollout path that matches your supervision volume, governance needs, and deployment scope.',
                'sections' => [
                    ['icon' => 'fa-building', 'title' => 'Department pilots', 'description' => 'Start with a focused faculty or department before scaling platform-wide.', 'icon_bg' => 'bg-violet-100', 'icon_color' => 'text-violet-700'],
                    ['icon' => 'fa-layer-group', 'title' => 'Institution rollouts', 'description' => 'Support multi-department governance with central controls and shared reporting.', 'icon_bg' => 'bg-blue-100', 'icon_color' => 'text-blue-700'],
                    ['icon' => 'fa-handshake', 'title' => 'Guided onboarding', 'description' => 'Pricing discussions include implementation guidance, migration, and pilot planning.', 'icon_bg' => 'bg-amber-100', 'icon_color' => 'text-amber-700'],
                ],
                'status' => 'Request a demo for a tailored deployment plan and commercial proposal.',
            ],
            'integrations' => [
                'pageTitle' => 'Integrations | TheOAsis Research Portal',
                'pageDescription' => 'Connect TheOAsis with your existing tools: LMS, SIS, authentication providers, and productivity suites.',
                'eyebrow' => 'Integrations',
                'heading' => 'Connect The Platform To The Systems Your University Already Uses',
                'intro' => 'TheOAsis is built to fit into existing academic operations instead of forcing teams to rebuild everything around a new tool.',
                'sections' => [
                    ['icon' => 'fa-key', 'title' => 'Authentication', 'description' => 'Support institutional identity flows and role-scoped access patterns.', 'icon_bg' => 'bg-sky-100', 'icon_color' => 'text-sky-700'],
                    ['icon' => 'fa-database', 'title' => 'Records and exports', 'description' => 'Bridge academic records, progress reports, and resource histories into downstream workflows.', 'icon_bg' => 'bg-emerald-100', 'icon_color' => 'text-emerald-700'],
                    ['icon' => 'fa-robot', 'title' => 'AI-assisted workflows', 'description' => 'Augment supervision with guided prompts and structured academic assistance.', 'icon_bg' => 'bg-indigo-100', 'icon_color' => 'text-indigo-700'],
                ],
                'status' => 'Use the API reference and integration discussions during rollout planning.',
            ],
            'changelog' => [
                'pageTitle' => 'Changelog | TheOAsis Research Portal',
                'pageDescription' => 'Track all updates, new features, and improvements to TheOAsis Research Supervision Portal.',
                'eyebrow' => 'Changelog',
                'heading' => 'Track Product Updates And Platform Improvements',
                'intro' => 'Follow changes to the supervision workflow, analytics surfaces, administration tools, and public product experience.',
                'sections' => [
                    ['icon' => 'fa-bell', 'title' => 'Workflow changes', 'description' => 'New approval flows, notification behaviors, and progression controls are documented here.', 'icon_bg' => 'bg-amber-100', 'icon_color' => 'text-amber-700'],
                    ['icon' => 'fa-chart-area', 'title' => 'Reporting updates', 'description' => 'Supervisor and admin analytics improvements are tracked with each release.', 'icon_bg' => 'bg-blue-100', 'icon_color' => 'text-blue-700'],
                    ['icon' => 'fa-wand-magic-sparkles', 'title' => 'Experience polish', 'description' => 'UI, performance, and accessibility improvements ship as part of the product cadence.', 'icon_bg' => 'bg-violet-100', 'icon_color' => 'text-violet-700'],
                ],
            ],
            'roadmap' => [
                'pageTitle' => 'Roadmap | TheOAsis Research Portal',
                'pageDescription' => 'See what\'s coming next for TheOAsis. Upcoming features, planned improvements, and community requests.',
                'eyebrow' => 'Roadmap',
                'heading' => 'What We Are Prioritizing Next',
                'intro' => 'The roadmap focuses on academic governance, better visibility for supervision teams, and tighter workflow automation.',
                'sections' => [
                    ['icon' => 'fa-mobile-screen', 'title' => 'Mobile-first experiences', 'description' => 'Improve student and supervisor access on smaller screens and low-friction devices.', 'icon_bg' => 'bg-sky-100', 'icon_color' => 'text-sky-700'],
                    ['icon' => 'fa-shield-halved', 'title' => 'Governance depth', 'description' => 'Expand auditability, approvals, and university-level policy enforcement.', 'icon_bg' => 'bg-emerald-100', 'icon_color' => 'text-emerald-700'],
                    ['icon' => 'fa-plug', 'title' => 'Platform integrations', 'description' => 'Continue work on operational integrations and reporting interoperability.', 'icon_bg' => 'bg-blue-100', 'icon_color' => 'text-blue-700'],
                ],
            ],
            'solutions.supervisors' => [
                'pageTitle' => 'For Supervisors | TheOAsis Research Portal',
                'pageDescription' => 'Supervisor-focused tools: review proposals faster, provide structured feedback, track supervisee progress, and detect stagnation early.',
                'eyebrow' => 'Solutions',
                'heading' => 'Designed For Supervisors Managing Real Academic Load',
                'intro' => 'Reduce context-switching and keep every supervisee moving with clearer signals, structured reviews, and faster intervention.',
                'sections' => [
                    ['icon' => 'fa-users', 'title' => 'Cohort visibility', 'description' => 'See who is progressing, stalled, or awaiting review without manual spreadsheets.', 'icon_bg' => 'bg-blue-100', 'icon_color' => 'text-blue-700'],
                    ['icon' => 'fa-file-signature', 'title' => 'Proposal governance', 'description' => 'Review and approve submissions with status history and comment context.', 'icon_bg' => 'bg-amber-100', 'icon_color' => 'text-amber-700'],
                    ['icon' => 'fa-comments', 'title' => 'Meeting accountability', 'description' => 'Keep structured meeting records instead of unsearchable messaging threads.', 'icon_bg' => 'bg-emerald-100', 'icon_color' => 'text-emerald-700'],
                ],
            ],
            'solutions.students' => [
                'pageTitle' => 'For Students | TheOAsis Research Portal',
                'pageDescription' => 'Student-focused experience: submit proposals, log meetings, complete stage resources, and follow an explicit roadmap to completion.',
                'eyebrow' => 'Solutions',
                'heading' => 'Give Students A Clear Path From Topic To Completion',
                'intro' => 'Students get one portal for stage progress, proposal status, meeting histories, resource work, and readiness tracking.',
                'sections' => [
                    ['icon' => 'fa-list-check', 'title' => 'Roadmap clarity', 'description' => 'Students always know the next required stage and what is blocking progress.', 'icon_bg' => 'bg-emerald-100', 'icon_color' => 'text-emerald-700'],
                    ['icon' => 'fa-upload', 'title' => 'Structured submissions', 'description' => 'Topic proposals and resource tasks stay attached to the right stage and review loop.', 'icon_bg' => 'bg-blue-100', 'icon_color' => 'text-blue-700'],
                    ['icon' => 'fa-user-clock', 'title' => 'Supervision rhythm', 'description' => 'Meeting logs and feedback build a durable record of supervision support.', 'icon_bg' => 'bg-amber-100', 'icon_color' => 'text-amber-700'],
                ],
            ],
            'solutions.administrators' => [
                'pageTitle' => 'For Administrators | TheOAsis Research Portal',
                'pageDescription' => 'Admin tools: manage users, configure institutions, monitor audit logs, and maintain governance across departments.',
                'eyebrow' => 'Solutions',
                'heading' => 'Administrative Control Without Heavy Operational Drag',
                'intro' => 'Manage the platform, enforce standards, and monitor delivery without building a parallel reporting process.',
                'sections' => [
                    ['icon' => 'fa-user-shield', 'title' => 'Role governance', 'description' => 'Manage students, supervisors, and admins with role-aware access and lifecycle control.', 'icon_bg' => 'bg-violet-100', 'icon_color' => 'text-violet-700'],
                    ['icon' => 'fa-clipboard-list', 'title' => 'Auditability', 'description' => 'Review interventions, workflow changes, and sensitive actions with clearer history.', 'icon_bg' => 'bg-rose-100', 'icon_color' => 'text-rose-700'],
                    ['icon' => 'fa-gears', 'title' => 'Configuration management', 'description' => 'Control platform defaults, AI settings, and shared operational behaviors centrally.', 'icon_bg' => 'bg-slate-100', 'icon_color' => 'text-slate-700'],
                ],
            ],
            'solutions.institutions' => [
                'pageTitle' => 'By Institution Type | TheOAsis Research Portal',
                'pageDescription' => 'Tailored solutions for universities, research institutes, and academic consortia.',
                'eyebrow' => 'Solutions',
                'heading' => 'Flexible Enough For Different Academic Structures',
                'intro' => 'The platform can support focused pilots, departmental rollouts, and broader multi-unit governance models.',
                'sections' => [
                    ['icon' => 'fa-building-columns', 'title' => 'Universities', 'description' => 'Support broad supervision governance across faculties or departments.', 'icon_bg' => 'bg-blue-100', 'icon_color' => 'text-blue-700'],
                    ['icon' => 'fa-flask', 'title' => 'Research institutes', 'description' => 'Adapt workflows around more focused review structures and specialist programs.', 'icon_bg' => 'bg-emerald-100', 'icon_color' => 'text-emerald-700'],
                    ['icon' => 'fa-network-wired', 'title' => 'Academic networks', 'description' => 'Create shared operating patterns for distributed oversight and reporting.', 'icon_bg' => 'bg-violet-100', 'icon_color' => 'text-violet-700'],
                ],
            ],
            'docs' => [
                'pageTitle' => 'Documentation | TheOAsis Research Portal',
                'pageDescription' => 'Complete documentation for TheOAsis Research Supervision Portal: getting started, user guides, API reference, and best practices.',
                'eyebrow' => 'Resources',
                'heading' => 'Documentation For Operators, Supervisors, And Students',
                'intro' => 'Use the documentation hub to onboard users, understand workflows, and navigate the platform more efficiently.',
                'sections' => [
                    ['icon' => 'fa-book-open', 'title' => 'Getting started', 'description' => 'Learn the key setup steps for students, supervisors, and administrators.', 'icon_bg' => 'bg-blue-100', 'icon_color' => 'text-blue-700'],
                    ['icon' => 'fa-route', 'title' => 'Workflow guides', 'description' => 'Understand proposals, meetings, resource approvals, and stage progression.', 'icon_bg' => 'bg-amber-100', 'icon_color' => 'text-amber-700'],
                    ['icon' => 'fa-code', 'title' => 'Reference material', 'description' => 'Explore developer-facing behavior, routes, and integration surfaces.', 'icon_bg' => 'bg-violet-100', 'icon_color' => 'text-violet-700'],
                ],
            ],
            'blog' => [
                'pageTitle' => 'Blog | TheOAsis Research Portal',
                'pageDescription' => 'Latest insights on research supervision, academic productivity, and EdTech innovation from TheOAsis team.',
                'eyebrow' => 'Resources',
                'heading' => 'Ideas And Field Notes From The Research Supervision Workflow',
                'intro' => 'We publish articles on academic process design, supervision operations, and technology-enabled research support.',
                'sections' => [
                    ['icon' => 'fa-pen-nib', 'title' => 'Practice insights', 'description' => 'Lessons from supervising better across student cohorts and departments.', 'icon_bg' => 'bg-sky-100', 'icon_color' => 'text-sky-700'],
                    ['icon' => 'fa-lightbulb', 'title' => 'Product thinking', 'description' => 'How we make governance and research progress easier to manage.', 'icon_bg' => 'bg-amber-100', 'icon_color' => 'text-amber-700'],
                    ['icon' => 'fa-graduation-cap', 'title' => 'Academic operations', 'description' => 'Perspectives on structure, feedback, and institutional delivery.', 'icon_bg' => 'bg-emerald-100', 'icon_color' => 'text-emerald-700'],
                ],
            ],
            'webinars' => [
                'pageTitle' => 'Webinars | TheOAsis Research Portal',
                'pageDescription' => 'Free and on-demand webinars on research supervision best practices, platform tutorials, and academic workflows.',
                'eyebrow' => 'Resources',
                'heading' => 'Live And On-demand Sessions For Research Supervision Teams',
                'intro' => 'Use webinars to train supervisors, onboard admins, and share better process patterns across departments.',
                'sections' => [
                    ['icon' => 'fa-video', 'title' => 'Platform walkthroughs', 'description' => 'Product sessions that explain the workflow end to end.', 'icon_bg' => 'bg-rose-100', 'icon_color' => 'text-rose-700'],
                    ['icon' => 'fa-chalkboard-user', 'title' => 'Practice sessions', 'description' => 'Conversations on supervision quality, intervention timing, and governance.', 'icon_bg' => 'bg-blue-100', 'icon_color' => 'text-blue-700'],
                    ['icon' => 'fa-play-circle', 'title' => 'Replay library', 'description' => 'Keep a reusable enablement archive for new stakeholders.', 'icon_bg' => 'bg-violet-100', 'icon_color' => 'text-violet-700'],
                ],
            ],
            'case-studies' => [
                'pageTitle' => 'Case Studies | TheOAsis Research Portal',
                'pageDescription' => 'Real-world stories of universities and supervisors transforming research supervision with TheOAsis.',
                'eyebrow' => 'Resources',
                'heading' => 'How Academic Teams Are Improving Supervision Delivery',
                'intro' => 'See examples of the operational changes institutions make when they move from manual tracking to a structured portal.',
                'sections' => [
                    ['icon' => 'fa-school', 'title' => 'Pilot rollouts', 'description' => 'Focused deployments that build confidence before wider adoption.', 'icon_bg' => 'bg-emerald-100', 'icon_color' => 'text-emerald-700'],
                    ['icon' => 'fa-timeline', 'title' => 'Workflow transformation', 'description' => 'Moving from fragmented oversight to more visible stage-based management.', 'icon_bg' => 'bg-blue-100', 'icon_color' => 'text-blue-700'],
                    ['icon' => 'fa-award', 'title' => 'Governance wins', 'description' => 'Better approval history, reporting clarity, and cross-role accountability.', 'icon_bg' => 'bg-amber-100', 'icon_color' => 'text-amber-700'],
                ],
            ],
            'api-reference' => [
                'pageTitle' => 'API Reference | TheOAsis Research Portal',
                'pageDescription' => 'Complete API documentation for integrating with TheOAsis Research Supervision Portal.',
                'eyebrow' => 'Resources',
                'heading' => 'Developer Reference For Integrating With TheOAsis',
                'intro' => 'The API surface supports role-aware data flows around students, supervision, proposals, meetings, resources, and platform operations.',
                'sections' => [
                    ['icon' => 'fa-key', 'title' => 'Authentication patterns', 'description' => 'Understand protected routes, session behaviors, and role access expectations.', 'icon_bg' => 'bg-slate-100', 'icon_color' => 'text-slate-700'],
                    ['icon' => 'fa-diagram-project', 'title' => 'Core entities', 'description' => 'Work with proposals, meeting logs, resources, notifications, and lifecycle states.', 'icon_bg' => 'bg-blue-100', 'icon_color' => 'text-blue-700'],
                    ['icon' => 'fa-plug-circle-check', 'title' => 'Integration planning', 'description' => 'Use the reference to shape data sync and reporting integrations responsibly.', 'icon_bg' => 'bg-emerald-100', 'icon_color' => 'text-emerald-700'],
                ],
            ],
            'about' => [
                'pageTitle' => 'About Us | TheOAsis Research Portal',
                'pageDescription' => 'Learn about TheOAsis mission, team, and the story behind the research supervision platform.',
                'eyebrow' => 'Company',
                'heading' => 'Why We Built TheOAsis',
                'intro' => 'TheOAsis exists to make academic supervision more visible, measurable, and manageable for institutions under real operational pressure.',
                'sections' => [
                    ['icon' => 'fa-bullseye', 'title' => 'Mission', 'description' => 'Help institutions run research supervision with more clarity and less friction.', 'icon_bg' => 'bg-blue-100', 'icon_color' => 'text-blue-700'],
                    ['icon' => 'fa-people-group', 'title' => 'Audience', 'description' => 'Built for students, supervisors, admins, and platform operators.', 'icon_bg' => 'bg-emerald-100', 'icon_color' => 'text-emerald-700'],
                    ['icon' => 'fa-sitemap', 'title' => 'Approach', 'description' => 'Combine workflow rigor, product design, and academic context into one platform.', 'icon_bg' => 'bg-violet-100', 'icon_color' => 'text-violet-700'],
                ],
            ],
            'careers' => [
                'pageTitle' => 'Careers | TheOAsis Research Portal',
                'pageDescription' => 'Join the team building the future of research supervision. Open positions in engineering, design, and academic partnerships.',
                'eyebrow' => 'Company',
                'heading' => 'Help Build Better Research Infrastructure',
                'intro' => 'We care about academic workflows, product quality, and tools that reduce operational drag for real institutions.',
                'sections' => [
                    ['icon' => 'fa-code', 'title' => 'Engineering', 'description' => 'Ship practical tools that improve clarity, reporting, and workflow reliability.', 'icon_bg' => 'bg-blue-100', 'icon_color' => 'text-blue-700'],
                    ['icon' => 'fa-pen-ruler', 'title' => 'Design', 'description' => 'Make complex academic workflows easier to operate under pressure.', 'icon_bg' => 'bg-amber-100', 'icon_color' => 'text-amber-700'],
                    ['icon' => 'fa-handshake-angle', 'title' => 'Partnerships', 'description' => 'Work with institutions rolling out modern research supervision practices.', 'icon_bg' => 'bg-emerald-100', 'icon_color' => 'text-emerald-700'],
                ],
                'status' => 'For current openings and partnership conversations, request a demo or contact the team directly.',
            ],
            'press' => [
                'pageTitle' => 'Press | TheOAsis Research Portal',
                'pageDescription' => 'Press kit, media coverage, and brand assets for TheOAsis Research Supervision Portal.',
                'eyebrow' => 'Company',
                'heading' => 'Press Materials And Product Narrative',
                'intro' => 'Use this page as the starting point for media context, platform background, and positioning language.',
                'sections' => [
                    ['icon' => 'fa-newspaper', 'title' => 'Media summary', 'description' => 'Understand what the platform solves and who it is built for.', 'icon_bg' => 'bg-slate-100', 'icon_color' => 'text-slate-700'],
                    ['icon' => 'fa-icons', 'title' => 'Brand context', 'description' => 'Access product framing, naming, and visual identity cues for coverage.', 'icon_bg' => 'bg-violet-100', 'icon_color' => 'text-violet-700'],
                    ['icon' => 'fa-microphone-lines', 'title' => 'Story angles', 'description' => 'Focus on research supervision quality, governance, and academic operations.', 'icon_bg' => 'bg-amber-100', 'icon_color' => 'text-amber-700'],
                ],
            ],
            'contact' => [
                'pageTitle' => 'Contact Us | TheOAsis Research Portal',
                'pageDescription' => 'Get in touch with TheOAsis team. Sales, support, partnerships, and general inquiries.',
                'eyebrow' => 'Company',
                'heading' => 'Talk To The Team Behind The Platform',
                'intro' => 'Use this route when you want a rollout conversation, implementation guidance, or a more direct discussion about fit.',
                'sections' => [
                    ['icon' => 'fa-calendar-check', 'title' => 'Demo requests', 'description' => 'Book a walkthrough for institutional stakeholders and rollout teams.', 'icon_bg' => 'bg-amber-100', 'icon_color' => 'text-amber-700'],
                    ['icon' => 'fa-life-ring', 'title' => 'Support conversations', 'description' => 'Reach out for operational questions and product clarification.', 'icon_bg' => 'bg-blue-100', 'icon_color' => 'text-blue-700'],
                    ['icon' => 'fa-handshake', 'title' => 'Partnership inquiries', 'description' => 'Discuss pilots, deployments, and ecosystem collaboration.', 'icon_bg' => 'bg-emerald-100', 'icon_color' => 'text-emerald-700'],
                ],
                'status' => 'The fastest path is still the Request Demo CTA for product and rollout conversations.',
            ],
            'security' => [
                'pageTitle' => 'Security | TheOAsis Research Portal',
                'pageDescription' => 'Our security practices, compliance certifications, and vulnerability disclosure policy.',
                'eyebrow' => 'Company',
                'heading' => 'Security And Operational Trust',
                'intro' => 'The platform is designed around institutional boundaries, role-based access, and accountable workflow history.',
                'sections' => [
                    ['icon' => 'fa-shield-halved', 'title' => 'Access control', 'description' => 'Role-aware permissions help keep student, supervisor, and admin workflows properly scoped.', 'icon_bg' => 'bg-emerald-100', 'icon_color' => 'text-emerald-700'],
                    ['icon' => 'fa-clock-rotate-left', 'title' => 'Traceability', 'description' => 'Approval flows and operator activity retain historical context for audits and reviews.', 'icon_bg' => 'bg-blue-100', 'icon_color' => 'text-blue-700'],
                    ['icon' => 'fa-bug', 'title' => 'Disclosure posture', 'description' => 'Security concerns can be raised through the product team for review and remediation.', 'icon_bg' => 'bg-rose-100', 'icon_color' => 'text-rose-700'],
                ],
            ],
            'privacy' => [
                'pageTitle' => 'Privacy Policy | TheOAsis Research Portal',
                'pageDescription' => 'How we collect, use, and protect your data. Your privacy rights and our commitments.',
                'eyebrow' => 'Company',
                'heading' => 'Privacy Expectations For A Multi-role Academic Platform',
                'intro' => 'TheOAsis is built around institutional context, role-scoped access, and the practical need to keep academic workflow data organized and controlled.',
                'sections' => [
                    ['icon' => 'fa-database', 'title' => 'Purpose-bound data use', 'description' => 'Workflow data exists to support research supervision, governance, and role-specific operations.', 'icon_bg' => 'bg-slate-100', 'icon_color' => 'text-slate-700'],
                    ['icon' => 'fa-user-lock', 'title' => 'Scoped visibility', 'description' => 'Access to student and supervision records is constrained by role and institutional context.', 'icon_bg' => 'bg-blue-100', 'icon_color' => 'text-blue-700'],
                    ['icon' => 'fa-file-shield', 'title' => 'Operational responsibility', 'description' => 'Privacy is approached as part of platform administration, not as an afterthought.', 'icon_bg' => 'bg-emerald-100', 'icon_color' => 'text-emerald-700'],
                ],
            ],
        ];
    }
}