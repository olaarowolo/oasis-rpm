@props([
    'title',
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'space-y-1']) }}>
  <h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">{{ $title }}</h2>
  @if($subtitle)
    <p class="text-sm text-slate-600 dark:text-slate-300">{{ $subtitle }}</p>
  @endif
</div>
