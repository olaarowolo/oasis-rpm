@props([
    'items' => [],
])

<nav class="hidden lg:flex flex-1 min-w-0 justify-center" aria-label="Header navigation">
    <div class="flex items-center gap-2 overflow-x-auto rounded-2xl border border-slate-200/80 bg-white/85 px-2 py-2 shadow-sm dark:border-slate-700 dark:bg-slate-800/85">
        @foreach ($items as $item)
            @php
                $itemClasses = $item['active']
                    ? 'bg-academic-50 text-academic-700 shadow-sm ring-1 ring-academic-100 dark:bg-academic-900/40 dark:text-academic-100 dark:ring-academic-700/40'
                    : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-700/60 dark:hover:text-white';
            @endphp

            <a href="{{ $item['href'] }}" class="inline-flex items-center gap-2 whitespace-nowrap rounded-xl px-3 py-2 text-sm font-medium transition {{ $itemClasses }}">
                <i class="fa-solid {{ $item['icon'] }} text-xs {{ $item['active'] ? 'text-academic-600 dark:text-academic-300' : 'text-slate-400 dark:text-slate-500' }}"></i>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>