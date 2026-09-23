@extends('layouts.super-admin')

@section('title', 'Platform Resources')

@section('breadcrumbs')
    <x-super-admin.breadcrumbs :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard'), 'icon' => 'fa-gauge-high'],
        ['label' => 'Resources', 'url' => route('super-admin.resources'), 'icon' => 'fa-database'],
    ]" />
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Platform Resources</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">View all learning resources across the platform.</p>
        </div>
    </div>

    <!-- Resources Table -->
    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-sm">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Title</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Type</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Stage</th>
                        <th class="px-5 py-3 text-left font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">University</th>
                        <th class="px-5 py-3 text-right font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @forelse($resources as $resource)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-5 py-4">
                                <p class="font-medium text-slate-900 dark:text-white">{{ $resource->title }}</p>
                                @if ($resource->description)
                                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 truncate max-w-xs">{{ $resource->description }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
                                    {{ ucfirst($resource->type) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300">
                                    Stage {{ $resource->stage }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-500 dark:text-slate-400">{{ $resource->university->name ?? 'Platform' }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="#" class="text-sm font-medium text-violet-600 hover:text-violet-700 dark:text-violet-400 dark:hover:text-violet-300">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">
                                <i class="fa-solid fa-database text-3xl text-slate-300 dark:text-slate-600 mb-2 block"></i>
                                No resources found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($resources->hasPages())
            <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-700">
                {{ $resources->links() }}
            </div>
        @endif
    </div>
</div>
@endsection