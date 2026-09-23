@php
    $pageTitle = trim($__env->yieldContent('title')) ?: 'Super Admin Portal';
    $breadcrumbs = $__env->yieldContent('breadcrumbs') ?: [];
@endphp

<x-layouts.super-admin :title="$pageTitle" :breadcrumbs="$breadcrumbs">
    @yield('content')
</x-layouts.super-admin>