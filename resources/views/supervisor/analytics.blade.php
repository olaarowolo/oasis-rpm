@extends('layouts.supervisor')

@section('title', 'Analytics & Insights | Research Supervision Portal | LASU')

@section('content')
<section class="space-y-6">
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white">Analytics &amp; Insights</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">A live summary of student progress, proposal flow, and meeting activity for your supervised cohort.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Students</p>
            <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ $analytics['student_count'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Pending Proposals</p>
            <p class="mt-2 text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $analytics['pending_proposals'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Approved Proposals</p>
            <p class="mt-2 text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $analytics['approved_proposals'] }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Meetings This Month</p>
            <p class="mt-2 text-3xl font-bold text-academic-700 dark:text-academic-300">{{ $analytics['monthly_meetings'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Students by Stage</h2>
                <span class="text-xs text-slate-500 dark:text-slate-400">Current placement</span>
            </div>
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach ($analytics['stage_counts'] as $stage => $count)
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40 p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">Stage {{ $stage }}</span>
                            <span class="text-lg font-bold text-academic-700 dark:text-academic-300">{{ $count }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Average Progress</h2>
            <p class="mt-4 text-4xl font-bold text-purple-600 dark:text-purple-400">{{ $analytics['average_progress'] }}%</p>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Across all students assigned to your supervision roster.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Recent Proposal Activity</h2>
            <div class="mt-4 space-y-3">
                @forelse ($analytics['recent_proposals'] as $proposal)
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900 dark:text-white">{{ $proposal->title }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $proposal->student->full_name ?? 'Unknown student' }}</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $proposal->status === 'approved' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : ($proposal->status === 'revision_required' ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300') }}">{{ str_replace('_', ' ', ucfirst($proposal->status)) }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">No proposal activity yet.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Recent Meetings</h2>
            <div class="mt-4 space-y-3">
                @forelse ($analytics['recent_meetings'] as $meeting)
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900 dark:text-white">Meeting #{{ $meeting->meeting_number }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $meeting->student->full_name ?? 'Unknown student' }} · {{ optional($meeting->meeting_date)->format('M d, Y') }}</p>
                            </div>
                            <a href="{{ route('supervisor.meetings.view', $meeting->id) }}" class="text-xs font-semibold text-academic-700 dark:text-academic-300 hover:underline">Open</a>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 dark:text-slate-400">No meetings logged yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection
                <span class="text-xs font-bold text-slate-900 dark:text-white">3 (50%)</span>
              </div>
              <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 dark:bg-slate-700/30">
                <div class="flex items-center gap-2">
                  <i class="fa-solid fa-video text-purple-500"></i>
                  <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Google Meet</span>
                </div>
                <span class="text-xs font-bold text-slate-900 dark:text-white">2 (33%)</span>
              </div>
              <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 dark:bg-slate-700/30">
                <div class="flex items-center gap-2">
                  <i class="fa-solid fa-phone text-emerald-500"></i>
                  <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">Phone/WhatsApp</span>
                </div>
                <span class="text-xs font-bold text-slate-900 dark:text-white">1 (17%)</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Risk flags -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-5">
          <h3 class="font-bold text-slate-900 dark:text-white text-base flex items-center gap-2 mb-4">
            <i class="fa-solid fa-triangle-exclamation text-amber-500"></i> Risk &amp; Attention
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <p class="text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2 flex items-center gap-1.5"><i class="fa-solid fa-bell-slash text-amber-500"></i> Quiet students</p>
              <div id="an-risk-quiet" class="space-y-1.5 text-xs">
                <div class="flex items-center gap-2 p-2 rounded-lg bg-amber-50 dark:bg-amber-900/20">
                  <div class="w-6 h-6 rounded-full bg-academic-800 text-amber-400 text-xs font-bold flex items-center justify-center">ST</div>
                  <span class="text-slate-700 dark:text-slate-300">Student Name</span>
                  <span class="ml-auto text-[10px] text-amber-600 dark:text-amber-400">30 days since last log</span>
                </div>
              </div>
            </div>
            <div>
              <p class="text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2 flex items-center gap-1.5"><i class="fa-solid fa-person-digging text-rose-500"></i> Stalled at stage</p>
              <div id="an-risk-stalled" class="space-y-1.5 text-xs">
                <div class="flex items-center gap-2 p-2 rounded-lg bg-rose-50 dark:bg-rose-900/20">
                  <div class="w-6 h-6 rounded-full bg-academic-800 text-amber-400 text-xs font-bold flex items-center justify-center">ST</div>
                  <span class="text-slate-700 dark:text-slate-300">Student Name</span>
                  <span class="ml-auto text-[10px] text-rose-600 dark:text-rose-400">Stage 3 (60 days)</span>
                </div>
              </div>
            </div>
            <div>
              <p class="text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2 flex items-center gap-1.5"><i class="fa-solid fa-clock text-indigo-500"></i> Oldest pending proposals</p>
              <div id="an-risk-proposals" class="space-y-1.5 text-xs">
                <div class="flex items-center gap-2 p-2 rounded-lg bg-indigo-50 dark:bg-indigo-900/20">
                  <i class="fa-solid fa-file-alt text-indigo-500 text-xs"></i>
                  <span class="text-slate-700 dark:text-slate-300">Sample Topic</span>
                  <span class="ml-auto text-[10px] text-indigo-600 dark:text-indigo-400">7 days old</span>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>

  </main>
</div>

@endsection
