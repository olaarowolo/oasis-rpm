@php
  $eyebrow = $eyebrow ?? null;
  $intro = $intro ?? null;
  $sections = $sections ?? [];
  $status = $status ?? null;
@endphp

<x-layouts.public
  :title="$pageTitle"
  :description="$pageDescription"
>
  <section class="bg-slate-50 py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="max-w-3xl">
        @if($eyebrow)
          <p class="text-xs font-bold tracking-wider text-academic-700 uppercase">{{ $eyebrow }}</p>
        @endif
        <h1 class="mt-2 font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-academic-900">{{ $heading }}</h1>
        @if($intro)
          <p class="mt-4 text-base sm:text-lg text-slate-600 max-w-2xl leading-relaxed">{{ $intro }}</p>
        @endif
      </div>

      @if($sections && count($sections) > 0)
        <div class="mt-10 grid md:grid-cols-2 xl:grid-cols-3 gap-4">
          @foreach($sections as $section)
            <article class="elevate rounded-2xl p-5 border border-slate-200 bg-slate-50">
              @if(isset($section['icon']))
                <p class="w-10 h-10 rounded-xl {{ $section['icon_bg'] ?? 'bg-blue-100' }} {{ $section['icon_color'] ?? 'text-blue-700' }} grid place-content-center">
                  <i class="fa-solid {{ $section['icon'] }}"></i>
                </p>
              @endif
              <h3 class="mt-4 font-semibold text-lg text-academic-900">{{ $section['title'] }}</h3>
              @if(isset($section['description']))
                <p class="mt-2 text-sm text-slate-600">{{ $section['description'] }}</p>
              @endif
            </article>
          @endforeach
        </div>
      @endif

      <div class="mt-10 flex flex-col sm:flex-row gap-3">
        <a href="{{ route('login') }}#request-demo" class="btn-animate inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-semibold transition">
          <i class="fa-solid fa-calendar-check"></i>
          Request Demo
        </a>
        <a href="{{ route('login') }}" class="btn-animate inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-white border border-slate-200 text-slate-700 hover:border-slate-300 font-semibold transition">
          <i class="fa-solid fa-sign-in-alt"></i>
          Go to Login
        </a>
      </div>

      @if($status)
        <div class="mt-10 p-4 rounded-xl bg-slate-100 border border-slate-200 text-center">
          <p class="text-sm text-slate-600">{{ $status }}</p>
        </div>
      @endif
    </div>
  </section>
</x-layouts.public>