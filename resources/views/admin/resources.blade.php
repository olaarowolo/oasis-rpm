@extends('layouts.admin')

@section('title', 'Manage Resources')

@section('content')
<div class="space-y-6">
    <div class="rounded-3xl p-6 sm:p-8 text-white shadow-xl bg-gradient-to-br from-slate-950 via-academic-900 to-academic-800 relative overflow-hidden">
        <div class="relative flex flex-col lg:flex-row lg:items-end justify-between gap-6">
            <div class="space-y-3 max-w-2xl">
                <p class="text-xs uppercase tracking-[0.3em] text-amber-300 font-semibold">Resource library</p>
                <h1 class="text-3xl sm:text-4xl font-black leading-tight">Manage learning resources across research stages.</h1>
                <p class="text-slate-300 text-sm sm:text-base">Add, update, and retire the articles, videos, and assignments students use through the portal.</p>
            </div>
            <button onclick="window.location.href='{{ route('admin.resources.create', ['university_id' => $selectedUniversityId ?? null]) }}'" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-academic-800 shadow-sm transition hover:bg-slate-100">
                <i class="fa-solid fa-plus"></i>
                Add Resource
            </button>
        </div>
    </div>

    @if (session('status'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-900/60 dark:bg-green-900/20 dark:text-green-300">
            {{ session('status') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-900/40">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Stage</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Points</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Mandatory</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                @forelse($resources as $resource)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">{{ $resource->title }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $resource->type === 'video' ? 'bg-red-100 text-red-800' :
                               ($resource->type === 'document' ? 'bg-gray-100 text-gray-800' :
                               ($resource->type === 'link' ? 'bg-blue-100 text-blue-800' :
                               'bg-purple-100 text-purple-800')) }}">
                            {{ ucfirst($resource->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">Stage {{ $resource->stage }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">{{ $resource->points }} pts</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                        @if($resource->is_mandatory)
                            <span class="text-green-600 font-semibold">Yes</span>
                        @else
                            <span class="text-slate-400 dark:text-slate-500">No</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.resources.edit', $resource->id) }}" class="text-academic-700 hover:text-academic-900 dark:text-academic-300 dark:hover:text-academic-100">Edit</a>
                        <form class="inline-block" action="{{ route('admin.resources.destroy', $resource->id) }}" method="POST" onsubmit="return confirm('Delete this resource? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 ml-2">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-sm text-slate-400 dark:text-slate-500">No resources yet. Click "Add Resource" to create one.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
