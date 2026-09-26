@php
    $sanitizer = new \App\Support\HtmlSanitizer();
    $isHalted = $document->status === 'halted';
    $sectionId = $section->id;
    $sectionTitle = $section->title;
    $isGroup = $section->isGroup();
    $isEditable = $section->isEditable();
    $canReview = in_array($section->status, ['submitted'], true);
    $pendingReview = $canReview;
    $haltedSection = $isHalted && $document->halted_section_id == $section->id;
    $canReleaseHalt = $haltedSection;
@endphp

<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden" data-section-id="{{ $section->id }}">
    <div class="p-4 border-b border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <h3 class="font-semibold text-slate-900 dark:text-white">{{ $section->title }}</h3>
                @if($section->key === 'methodology' && $section->word_count > 0)
                    <span class="text-xs text-slate-500 dark:text-slate-400">({{ $section->word_count }} words total)</span>
                @endif
                @if($isGroup)
                    <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400">Group</span>
                @endif
            </div>
            <span class="shrink-0 px-2.5 py-1 rounded-full text-[10px] font-bold
                @if($section->status === 'accepted') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300
                @elseif($section->status === 'conditional') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
                @elseif($section->status === 'submitted') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300
                @elseif($section->status === 'revision_requested') bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300
                @elseif($section->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                @else bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400
                @endif">
                {{ config('defense_readiness.section_status_labels.' . $section->status) }}
            </span>
        </div>

        @if($section->guidance && !$isGroup)
            <div class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                {{ $section->guidance }}
            </div>
        @endif

        @if($section->status === 'conditional' && $section->reviews)
            @php
                $latestConditional = $section->reviews->firstWhere('action', 'conditional');
            @endphp
            @if($latestConditional && $latestConditional->conditions)
                <div class="mt-2 p-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                    <div class="font-semibold text-xs text-blue-800 dark:text-blue-300">Conditions:</div>
                    <ul class="text-xs text-blue-700 dark:text-blue-400 list-disc list-inside mt-1">
                        @foreach(explode("\n", trim($latestConditional->conditions)) as $cond)
                            @if(trim($cond))<li>{{ trim($cond) }}</li>@endif
                        @endforeach
                    </ul>
                    @if($section->conditions_acknowledged_at)
                        <div class="text-xs text-emerald-700 dark:text-emerald-400 mt-1">✓ Conditions addressed on {{ $section->conditions_acknowledged_at->format('M j, Y') }}</div>
                    @endif
                </div>
            @endif
        @endif
    </div>

    @if($isGroup)
        @foreach($section->children ?? [] as $child)
            <div class="border-t border-slate-200 dark:border-slate-700">
                @include('supervisor.manuscripts.partials.section-card', [
                    'section' => $child,
                    'document' => $document,
                    'isGroup' => false,
                ])
            </div>
        @endforeach
    @else
        <div class="p-4">
            @php
                $version = $section->versions ? $section->versions->sortByDesc('version_number')->first() : null;
                $content = $version?->content ?? $section->content ?? '';
                $wordCount = $version?->word_count ?? $section->word_count ?? 0;
            @endphp

            @if($content)
                <div class="prose prose-sm dark:prose-invert max-w-none">
                    {!! $sanitizer->clean($content) !!}
                </div>
            @else
                <div class="text-slate-400 dark:text-slate-500 text-sm italic">No content yet.</div>
            @endif

            <div class="mt-3 flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400">
                @if($section->target_min_words !== null && $section->target_max_words !== null)
                    <span>Target: {{ $section->target_min_words }}–{{ $section->target_max_words }} words</span>
                @endif
                <span>Words: {{ $wordCount }}</span>
                @if($section->current_version)
                    <span>Last version: v{{ $section->current_version->version_number }}</span>
                @endif
            </div>

            @if($section->reviews && $section->reviews->isNotEmpty())
                <div class="mt-4 space-y-3">
                    @foreach($section->reviews as $review)
                        <div class="p-3 bg-slate-50 dark:bg-slate-900/30 rounded-lg border border-slate-200 dark:border-slate-700">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2 text-xs">
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $review->reviewer?->name ?? 'Supervisor' }}</span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-medium
                                        @if($review->action === 'accepted') bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30
                                        @elseif($review->action === 'conditional') bg-blue-100 text-blue-800 dark:bg-blue-900/30
                                        @elseif($review->action === 'revision_requested') bg-orange-100 text-orange-800 dark:bg-orange-900/30
                                        @elseif($review->action === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30
                                        @else bg-slate-100 text-slate-600 dark:bg-slate-700
                                        @endif">
                                        {{ config('defense_readiness.section_status_labels.' . $review->action) }}
                                    </span>
                                </div>
                                <span class="text-[10px] text-slate-400 dark:text-slate-500">{{ optional($review->created_at)->diffForHumans() }}</span>
                            </div>
                            @if($review->comment)
                                <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">{{ $review->comment }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    @if(!$isGroup)
        <div class="p-3 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/20 flex flex-wrap gap-2">
            @php
                $canAccept = $section->status === 'submitted' || $canReleaseHalt;
                $showComment = true;
            @endphp

            @if($canReleaseHalt)
                <button data-decision-btn data-section-id="{{ $sectionId }}" data-decision-action="revision_requested" data-section-title="{{ $sectionTitle }}"
                    class="px-3 py-1.5 rounded-lg bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300 text-xs font-semibold hover:bg-orange-200">
                    Release Halt (Request Revision)
                </button>
            @endif

            @if($section->status === 'submitted')
                <button data-decision-btn data-section-id="{{ $sectionId }}" data-decision-action="accepted" data-section-title="{{ $sectionTitle }}"
                    class="px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 text-xs font-semibold hover:bg-emerald-200">
                    Accept
                </button>
                <button data-decision-btn data-section-id="{{ $sectionId }}" data-decision-action="conditional" data-section-title="{{ $sectionTitle }}"
                    class="px-3 py-1.5 rounded-lg bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 text-xs font-semibold hover:bg-blue-200">
                    Conditional Approval
                </button>
                <button data-decision-btn data-section-id="{{ $sectionId }}" data-decision-action="revision_requested" data-section-title="{{ $sectionTitle }}"
                    class="px-3 py-1.5 rounded-lg bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 text-xs font-semibold hover:bg-amber-200">
                    Request Revision
                </button>
                <button data-decision-btn data-section-id="{{ $sectionId }}" data-decision-action="rejected" data-section-title="{{ $sectionTitle }}"
                    class="px-3 py-1.5 rounded-lg bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 text-xs font-semibold hover:bg-red-200">
                    Reject
                </button>
            @endif

            <button data-comment-btn data-section-id="{{ $sectionId }}" data-section-title="{{ $sectionTitle }}"
                class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300 text-xs font-semibold hover:bg-slate-200">
                Add Comment
            </button>
        </div>
    @endif
</div>
