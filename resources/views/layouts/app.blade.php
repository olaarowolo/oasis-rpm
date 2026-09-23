@php($pageTitle = trim($__env->yieldContent('title')) ?: 'Research Supervision Portal')

<x-layouts.app :title="$pageTitle">
    @yield('content')
</x-layouts.app>