@php
    $pageTitle = trim($__env->yieldContent('title', 'Supervisor Hub'));
@endphp

<x-layouts.app :title="$pageTitle">
    <x-app-header role="supervisor" :page-title="$pageTitle" />

    <div class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 flex flex-col md:flex-row gap-4 sm:gap-6">
        @include('supervisor.partials.sidebar')

        <main class="flex-1 min-w-0 space-y-6">
            @yield('content')
        </main>
    </div>
</x-layouts.app>