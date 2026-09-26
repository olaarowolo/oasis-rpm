@extends('layouts.student')

@section('title', 'Defense Readiness')

@section('content')
<div class="space-y-6">
    <!-- ===== Header ===== -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">Defense Readiness</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Track your progress toward defense submission.</p>
            </div>
            <a href="{{ route('student.dashboard') }}" class="px-3 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i>
                Dashboard
            </a>
        </div>
    </div>

    <!-- ===== Readiness Score panel ===== -->
    <div>
        <div class="inline-block rounded-2xl border border-purple-200 bg-purple-50 px-6 py-5 dark:border-purple-900/50 dark:bg-purple-900/20">
            <div class="text-2xl font-bold text-purple-600 dark:text-purple-300">{{ $defenseScore }}%</div>
            <div class="text-sm text-slate-600 dark:text-slate-300">Defense Readiness Score</div>
        </div>
    </div>

    <!-- ===== 5-item checklist table ===== -->
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-900/40">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Requirement</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Details</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">Topic Approval</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($hasApprovedTopic)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Pending</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                        @if($hasApprovedTopic) Research topic has been approved by supervisor @else No approved topic yet @endif
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">Progress Completion</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($progressPercentage >= 80)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Ready</span>
                        @elseif($progressPercentage >= 50)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">In Progress</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Not Ready</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">{{ $progressPercentage }}% complete</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">Final Document</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($hasFinalDocument)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Uploaded</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Pending</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                        @if($hasFinalDocument) Final manuscript uploaded @else Upload your final document @endif
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">Meeting Logs</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($completedMeetings >= $requiredMeetings)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">{{ $completedMeetings }} / {{ $requiredMeetings }} required</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">Resource Completion</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($completedResources >= $totalResources * 0.8)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">In Progress</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">{{ $completedResources }} / {{ $totalResources }} resources</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- ===== Submit for Defense ===== -->
    <div class="flex items-center gap-4">
        <button id="submit-for-defense-btn"
            onclick="window.location.href='{{ route('student.dashboard') }}'"
            class="rounded-xl bg-purple-600 px-6 py-3 font-semibold text-white transition hover:bg-purple-700 disabled:cursor-not-allowed disabled:opacity-50"
            @if($defenseScore < 70) disabled @endif>
            Submit for Defense
        </button>
        <span id="defense-gate-text" class="text-xs text-slate-500 dark:text-slate-400">
            @if($defenseScore < 70)
                A defense readiness score of 70% is required.
            @endif
        </span>
    </div>

    <!-- ===== Manuscript Readiness Form ===== -->
    <div id="manuscript-form-container" class="hidden">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
            <!-- Header + Progress rail -->
            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Manuscript Readiness Review</h2>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Submit each section for supervisor review. Sections unlock sequentially.</p>
                    </div>
                    <div id="document-status-banner" class="px-3 py-1.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                        Draft
                    </div>
                </div>

                <!-- Study approach selector -->
                <div id="settings-bar" class="mt-4 flex flex-wrap items-center gap-4 text-sm">
                    <label class="flex items-center gap-2">
                        <span class="text-slate-600 dark:text-slate-400">Study approach:</span>
                        <select id="study-approach" class="px-2 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-slate-800 text-slate-900 dark:text-white text-xs">
                            <option value="">Select approach</option>
                            <option value="quantitative">Quantitative</option>
                            <option value="qualitative">Qualitative</option>
                            <option value="mixed">Mixed methods</option>
                        </select>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="primary-data-collection" class="rounded border-slate-300 dark:border-slate-600">
                        <span class="text-slate-600 dark:text-slate-400">Primary data collection</span>
                    </label>
                    <span id="settings-locked-note" class="hidden text-xs text-amber-600 dark:text-amber-400">
                        Settings are locked after the first submission.
                    </span>
                </div>
            </div>

            <!-- Progress rail -->
            <div id="progress-rail" class="px-6 py-3 border-b border-slate-200 dark:border-slate-700 overflow-x-auto">
                <div class="flex items-center gap-2 text-xs">
                    <!-- populated by JS -->
                </div>
            </div>

            <!-- Sections container -->
            <div id="sections-container" class="p-4 space-y-2">
                <!-- populated by defense-readiness.js -->
            </div>
        </div>
    </div>

    <!-- Toast container -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-[60] space-y-2 pointer-events-none"></div>
</div>

@vite(['resources/js/rich-text-editor.js', 'resources/js/defense-readiness.js'])
@endsection
