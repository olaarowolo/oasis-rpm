@props([
    'label',
    'value',
    'note' => null,
    'tone' => 'default',
])

@php
  $toneClass = match ($tone) {
      'success' => 'text-emerald-700 dark:text-emerald-400',
      'warning' => 'text-amber-700 dark:text-amber-400',
      'danger' => 'text-rose-700 dark:text-rose-400',
      default => 'text-slate-900 dark:text-white',
  };
@endphp

<article {{ $attributes->merge(['class' => 'rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-5 shadow-sm']) }}>
  <p class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $label }}</p>
  <p class="mt-2 text-3xl font-black {{ $toneClass }}">{{ $value }}</p>
  @if($note)
    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $note }}</p>
  @endif
</article>
