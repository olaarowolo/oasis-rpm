<x-layouts.app title="Profile Settings | TheOAsis Research Supervision System">

  <x-slot:head>
    @include('partials.dashboards.student-styles')
  </x-slot:head>

  <x-app-header role="student" page-title="Profile Settings" />

  <div class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 flex flex-col md:flex-row gap-4 sm:gap-6">

    @include('partials.dashboards.student-sidebar')

    <main class="flex-1 min-w-0 space-y-6">
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm space-y-6">
        <div class="flex items-start justify-between gap-3 border-b border-slate-100 dark:border-slate-700 pb-4">
          <div>
            <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2">
              <i class="fa-solid fa-user-gear text-academic-600"></i>
              Profile Settings
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Update the personal details used across your student portal.</p>
          </div>
          <a href="{{ route('student.dashboard') }}" class="px-3 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i>
            Dashboard
          </a>
        </div>

        @if (session('status'))
          <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-900/20 dark:text-emerald-300">
            {{ session('status') }}
          </div>
        @endif

        @if ($errors->any())
          <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/60 dark:bg-rose-900/20 dark:text-rose-300">
            {{ $errors->first() }}
          </div>
        @endif

        <form method="POST" action="{{ route('student.profile.update') }}" class="space-y-5">
          @csrf

          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Full Name</label>
              <input type="text" name="full_name" value="{{ old('full_name', $student->full_name) }}" required class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-academic-600 focus:outline-none focus:ring-2 focus:ring-academic-600/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Phone</label>
              <input type="text" name="phone" value="{{ old('phone', $student->user?->phone ?? $student->phone) }}" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-academic-600 focus:outline-none focus:ring-2 focus:ring-academic-600/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
            </div>
          </div>

          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Matric Number</label>
              <input type="text" value="{{ $student->matric_number }}" disabled class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-sm text-slate-500 shadow-sm dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-400">
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Degree Level</label>
              <select name="degree_level" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-academic-600 focus:outline-none focus:ring-2 focus:ring-academic-600/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                @foreach (['BSc', 'MSc', 'PhD'] as $degree)
                  <option value="{{ $degree }}" {{ old('degree_level', $student->degree_level ?? 'BSc') === $degree ? 'selected' : '' }}>{{ $degree }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Personal Drive URL</label>
            <input type="url" name="personal_drive_url" value="{{ old('personal_drive_url', $student->personal_drive_url) }}" placeholder="https://drive.google.com/..." class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-academic-600 focus:outline-none focus:ring-2 focus:ring-academic-600/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
          </div>

          <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-academic-700 px-4 py-2.5 text-sm font-semibold text-white shadow-md transition hover:bg-academic-800">
              <i class="fa-solid fa-floppy-disk"></i>
              Save Changes
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>

</x-layouts.app>