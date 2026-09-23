@props([
    'role' => 'user',
    'label' => null,
])

@php
  $palette = match ($role) {
      'student' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300',
      'supervisor' => 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300',
      'admin', 'super_admin' => 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300',
      default => 'bg-slate-100 dark:bg-slate-700/60 text-slate-700 dark:text-slate-300',
  };

  $icon = match ($role) {
      'student' => 'fa-user-graduate',
      'supervisor' => 'fa-user-shield',
      'admin', 'super_admin' => 'fa-user-gear',
      default => 'fa-user',
  };

  $resolvedLabel = $label ?: match ($role) {
      'student' => 'Student',
      'supervisor' => 'Supervisor',
      'admin' => 'Admin',
      'super_admin' => 'Super Admin',
      default => 'User',
  };
@endphp

<div {{ $attributes->merge(['class' => "px-3 py-1.5 rounded-lg text-xs font-bold inline-flex items-center gap-2 {$palette}"]) }}>
  <i class="fa-solid {{ $icon }}"></i>
  <span>{{ $resolvedLabel }}</span>
</div>
