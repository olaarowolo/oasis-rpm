<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\DefenseReadinessDocument;
use App\Models\DefenseReadinessSection;
use App\Models\DefenseReadinessSectionVersion;
use App\Models\MeetingLog;
use App\Models\ResourceProgress;
use App\Services\DefenseReadinessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DefenseReadinessController extends BaseController
{
    protected DefenseReadinessService $service;

    public function __construct(DefenseReadinessService $service)
    {
        $this->service = $service;
    }

    /**
     * ── STUDENT API ──────────────────────────────────────────────────────────
     */

    /**
     * Hydrate the whole defense-readiness page: document, settings,
     * sections (with content, word count, status, conditions, review history),
     * plus the existing checklist + score values.
     */
    public function studentIndex(Request $request)
    {
        $student = Student::with(['archiveSubmission', 'supervisor.user'])->find(session('student_id'));

        if (! $student) {
            return response()->json(['success' => false, 'message' => 'Student not found'], 404);
        }

        $document = $this->service->documentForStudent($student);

        // Existing checklist + score calculation (kept verbatim from the web.php closure).
        $hasApprovedTopic = (bool) ($student?->research_topic_approved_date);
        $progressPercentage = (int) ($student?->progress_percentage ?? 0);
        $hasFinalDocument = (bool) ($student?->archiveSubmission);

        $completedMeetings = MeetingLog::where('student_id', session('student_id'))
            ->where('status', 'approved')->count();
        $requiredMeetings = 6;

        $completedResources = ResourceProgress::where('student_id', session('student_id'))
            ->where('status', 'approved')->count();
        $totalResources = ResourceProgress::where('student_id', session('student_id'))->count();

        $resourceRatio = $totalResources > 0 ? ($completedResources / $totalResources) : 0;
        $meetingRatio = $requiredMeetings > 0 ? min($completedMeetings / $requiredMeetings, 1) : 0;
        $defenseScore = (int) round(
            ($hasApprovedTopic ? 20 : 0)
            + (min($progressPercentage, 100) / 100 * 30)
            + ($hasFinalDocument ? 20 : 0)
            + ($meetingRatio * 15)
            + ($resourceRatio * 15)
        );

        $sections = $document->sections()->with(['reviews', 'versions'])->get()->map(function ($section) use ($document) {
            return [
                'id' => $section->id,
                'key' => $section->key,
                'title' => $section->title,
                'position' => $section->position,
                'kind' => $section->kind,
                'guidance' => $section->guidance,
                'parent_id' => $section->parent_id,
                'target_min_words' => $section->target_min_words,
                'target_max_words' => $section->target_max_words,
                'word_count_label' => $section->word_count_label,
                'content' => $section->content,
                'word_count' => $section->word_count,
                'status' => $section->status,
                'conditions_acknowledged_at' => $section->conditions_acknowledged_at,
                'submitted_at' => $section->submitted_at,
                'decided_at' => $section->decided_at,
                'current_version_id' => $section->current_version_id,
                'unlocked' => $this->service->isUnlocked($document, $section),
                'reviews' => $section->reviews->map(fn ($r) => [
                    'id' => $r->id,
                    'reviewer_name' => $r->reviewer?->name ?? 'Supervisor',
                    'action' => $r->action,
                    'comment' => $r->comment,
                    'conditions' => $r->conditions,
                    'created_at' => $r->created_at?->toIso8601String(),
                ])->values(),
            ];
        });

        return response()->json([
            'id' => $document->id,
            'status' => $document->status,
            'study_approach' => $document->study_approach,
            'primary_data_collection' => $document->primary_data_collection,
            'settings_locked' => ! $document->canEditSettings(),
            'halted_at' => $document->halted_at,
            'halted_reason' => $document->halted_reason,
            'completed_at' => $document->completed_at,
            'sections' => $sections,
            'checklist' => [
                'has_approved_topic' => $hasApprovedTopic,
                'progress_percentage' => $progressPercentage,
                'has_final_document' => $hasFinalDocument,
                'completed_meetings' => $completedMeetings,
                'required_meetings' => $requiredMeetings,
                'completed_resources' => $completedResources,
                'total_resources' => $totalResources,
                'defense_score' => $defenseScore,
            ],
        ]);
    }

    /**
     * Get current study approach / primary data collection settings.
     */
    public function studentSettings(Request $request)
    {
        $student = Student::find(session('student_id'));
        if (! $student) {
            return response()->json(['success' => false, 'message' => 'Student not found'], 404);
        }

        $document = DefenseReadinessDocument::where('student_id', $student->id)
            ->where('university_id', session('university_id'))
            ->first();

        if (! $document) {
            return response()->json(['success' => false, 'message' => 'Document not found'], 404);
        }

        return response()->json([
            'study_approach' => $document->study_approach,
            'primary_data_collection' => $document->primary_data_collection,
        ]);
    }

    /**
     * Update study approach / primary data collection toggle.
     */
    public function studentUpdateSettings(Request $request)
    {
        $student = Student::find(session('student_id'));
        if (! $student) {
            return response()->json(['success' => false, 'message' => 'Student not found'], 404);
        }

        $document = DefenseReadinessDocument::where('student_id', $student->id)
            ->where('university_id', session('university_id'))
            ->first();

        if (! $document) {
            return response()->json(['success' => false, 'message' => 'Document not found'], 404);
        }

        $validated = $request->validate([
            'study_approach' => 'nullable|in:quantitative,qualitative,mixed',
            'primary_data_collection' => 'nullable|boolean',
        ]);

        $approach = $validated['study_approach'] ?? $document->study_approach;
        $primaryData = array_key_exists('primary_data_collection', $validated)
            ? (bool) $validated['primary_data_collection']
            : $document->primary_data_collection;

        if (! $this->service->canUpdateSettings($document)) {
            return response()->json(['success' => false, 'message' => 'Settings can no longer be changed after the first submission'], 422);
        }

        $this->service->updateSettings($document, $approach, $primaryData);

        return response()->json(['message' => 'Settings updated successfully']);
    }

    /**
     * Autosave section content.
     */
    public function studentAutosave(Request $request, $sectionId)
    {
        $student = Student::find(session('student_id'));
        if (! $student) {
            return response()->json(['success' => false, 'message' => 'Student not found'], 404);
        }

        $section = DefenseReadinessSection::whereHas('document', function ($q) use ($student) {
            $q->where('student_id', $student->id)
                ->where('university_id', session('university_id'));
        })->find($sectionId);

        if (! $section) {
            return response()->json(['success' => false, 'message' => 'Section not found'], 404);
        }

        $document = $section->document()->first();

        $validated = $request->validate([
            'content' => 'nullable|string',
            'last_saved_at' => 'nullable|string',
        ]);

        $result = $this->service->autosaveSection(
            $document,
            $section,
            $validated['content'] ?? null,
            $validated['last_saved_at'] ?? null
        );

        if (isset($result['conflict']) && $result['conflict']) {
            return response()->json([
                'success' => false,
                'message' => 'Conflict: section was modified in another tab',
                'conflict' => true,
                'server_updated_at' => $result['server_updated_at'],
            ], 409);
        }

        if (! ($result['saved'] ?? false)) {
            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Could not save',
            ], $result['code'] ?? 403);
        }

        return response()->json([
            'saved_at' => $result['saved_at'],
            'word_count' => $result['word_count'],
            'status' => $result['status'],
        ]);
    }

    /**
     * Submit a section for supervisor review.
     */
    public function studentSubmitSection(Request $request, $sectionId)
    {
        $student = Student::find(session('student_id'));
        if (! $student) {
            return response()->json(['success' => false, 'message' => 'Student not found'], 404);
        }

        $section = DefenseReadinessSection::whereHas('document', function ($q) use ($student) {
            $q->where('student_id', $student->id)
                ->where('university_id', session('university_id'));
        })->find($sectionId);

        if (! $section) {
            return response()->json(['success' => false, 'message' => 'Section not found'], 404);
        }

        $document = $section->document()->first();

        if (! $student->supervisor_id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have an assigned supervisor. Please contact your department administrator.',
            ], 422);
        }

        try {
            $version = $this->service->submitSection($document, $section, $student->user);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json([
            'version_id' => $version->id,
            'version_number' => $version->version_number,
            'word_count' => $version->word_count,
        ]);
    }

    /**
     * Acknowledge that conditional approval conditions have been addressed.
     */
    public function studentAcknowledgeConditions(Request $request, $sectionId)
    {
        $student = Student::find(session('student_id'));
        if (! $student) {
            return response()->json(['success' => false, 'message' => 'Student not found'], 404);
        }

        $section = DefenseReadinessSection::whereHas('document', function ($q) use ($student) {
            $q->where('student_id', $student->id)
                ->where('university_id', session('university_id'));
        })->find($sectionId);

        if (! $section) {
            return response()->json(['success' => false, 'message' => 'Section not found'], 404);
        }

        $document = $section->document()->first();

        $ok = $this->service->acknowledgeConditions($document, $section);
        if (! $ok) {
            return response()->json(['success' => false, 'message' => 'Section is not in a conditional state'], 422);
        }

        return response()->json(['message' => 'Conditions acknowledged']);
    }

    /**
     * Add a custom section before References.
     */
    public function studentAddCustomSection(Request $request)
    {
        $student = Student::find(session('student_id'));
        if (! $student) {
            return response()->json(['success' => false, 'message' => 'Student not found'], 404);
        }

        $document = DefenseReadinessDocument::where('student_id', $student->id)
            ->where('university_id', session('university_id'))
            ->first();

        if (! $document) {
            return response()->json(['success' => false, 'message' => 'Document not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'guidance' => 'nullable|string',
        ]);

        $section = $this->service->addCustomSection($document, $validated['title'], $validated['guidance']);

        return response()->json([
            'section' => [
                'id' => $section->id,
                'key' => $section->key,
                'title' => $section->title,
                'position' => $section->position,
                'kind' => $section->kind,
                'status' => $section->status,
            ],
        ], 201);
    }

    /**
     * Delete a custom section (draft, zero versions only).
     */
    public function studentDeleteSection(Request $request, $sectionId)
    {
        $student = Student::find(session('student_id'));
        if (! $student) {
            return response()->json(['success' => false, 'message' => 'Student not found'], 404);
        }

        $section = DefenseReadinessSection::whereHas('document', function ($q) use ($student) {
            $q->where('student_id', $student->id)
                ->where('university_id', session('university_id'));
        })->find($sectionId);

        if (! $section) {
            return response()->json(['success' => false, 'message' => 'Section not found'], 404);
        }

        $document = $section->document()->first();

        $ok = $this->service->deleteCustomSection($document, $section);
        if (! $ok) {
            return response()->json(['success' => false, 'message' => 'This custom section cannot be deleted at this time'], 422);
        }

        return response()->json(['message' => 'Custom section deleted']);
    }

    /**
     * ── SUPERVISOR API ───────────────────────────────────────────────────────
     */

    /**
     * Queue of manuscripts awaiting review.
     */
    public function supervisorIndex(Request $request)
    {
        $documents = DefenseReadinessDocument::where('university_id', session('university_id'))
            ->whereHas('student', function ($q) {
                $q->where('supervisor_id', session('supervisor_id'));
            })
            ->with(['student.user', 'sections' => function ($q) {
                $q->orderByRaw("CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END")
                    ->orderBy('parent_id', 'asc')
                    ->orderBy('position', 'asc');
            }])->get();

        $items = $documents->map(function ($doc) {
            $student = $doc->student;
            $sections = $doc->sections;

            $lastActivity = $sections->max('updated_at') ?? $doc->updated_at;

            $awaitingReview = $sections->filter(fn ($s) => $s->status === 'submitted')->count();

            return [
                'id' => $doc->id,
                'student_name' => $student->full_name ?? 'Unknown',
                'matric' => $student->matric_number ?? '',
                'programme' => $student->programme ?? '',
                'status' => $doc->status,
                'halted_at' => $doc->halted_at,
                'halted_reason' => $doc->halted_reason,
                'awaiting_review_count' => $awaitingReview,
                'section_statuses' => $sections->map(fn ($s) => [
                    'id' => $s->id,
                    'key' => $s->key,
                    'title' => $s->title,
                    'status' => $s->status,
                ])->values()->all(),
                'last_activity' => optional($lastActivity)->diffForHumans(),
            ];
        });

        $filter = $request->query('filter', 'all');
        if ($filter !== 'all') {
            $items = $items->filter(fn ($item) => $item['status'] === $filter);
        }

        return response()->json([
            'documents' => $items->values()->all(),
            'total' => $items->count(),
        ]);
    }

    /**
     * Full read-only manuscript for supervisor review.
     */
    public function supervisorShow(Request $request, $documentId)
    {
        $document = DefenseReadinessDocument::where('university_id', session('university_id'))
            ->whereHas('student', function ($q) {
                $q->where('supervisor_id', session('supervisor_id'));
            })
            ->with([
                'student.user',
                'student.supervisor.user',
                'sections.reviews.reviewer',
                'sections.versions',
            ])->find($documentId);

        if (! $document) {
            return response()->json(['success' => false, 'message' => 'Manuscript not found'], 404);
        }

        $sanitizer = new \App\Support\HtmlSanitizer();

        $sections = $document->sections->map(function ($section) use ($sanitizer) {
            // For the supervisor reader, show the latest submitted version content (sanitised).
            $version = $section->versions->sortByDesc('version_number')->first();

            return [
                'id' => $section->id,
                'key' => $section->key,
                'title' => $section->title,
                'position' => $section->position,
                'kind' => $section->kind,
                'parent_id' => $section->parent_id,
                'guidance' => $section->guidance,
                'target_min_words' => $section->target_min_words,
                'target_max_words' => $section->target_max_words,
                'word_count_label' => $section->word_count_label,
                'content' => $sanitizer->clean($version?->content ?? $section->content),
                'word_count' => $version?->word_count ?? $section->word_count,
                'status' => $section->status,
                'conditions' => $section->reviews
                    ->where('action', 'conditional')
                    ->map(fn ($r) => $r->conditions)
                    ->filter()
                    ->last(),
                'conditions_acknowledged_at' => $section->conditions_acknowledged_at,
                'current_version' => $version?->version_number,
                'reviews' => $section->reviews->map(fn ($r) => [
                    'id' => $r->id,
                    'reviewer_name' => $r->reviewer?->name ?? 'Supervisor',
                    'action' => $r->action,
                    'comment' => $r->comment,
                    'conditions' => $r->conditions,
                    'created_at' => $r->created_at?->toIso8601String(),
                ])->values(),
            ];
        });

        return response()->json([
            'document' => [
                'id' => $document->id,
                'student_name' => $document->student->full_name ?? '',
                'matric' => $document->student->matric_number ?? '',
                'programme' => $document->student->programme ?? '',
                'study_approach' => $document->study_approach,
                'primary_data_collection' => $document->primary_data_collection,
                'status' => $document->status,
                'halted_at' => $document->halted_at,
                'halted_reason' => $document->halted_reason,
                'completed_at' => $document->completed_at,
            ],
            'sections' => $sections,
        ]);
    }

    /**
     * Process a supervisor decision on a section.
     */
    public function supervisorDecision(Request $request, $sectionId)
    {
        $section = DefenseReadinessSection::whereHas('document.student', function ($q) {
            $q->where('supervisor_id', session('supervisor_id'))
                ->where('university_id', session('university_id'));
        })->find($sectionId);

        if (! $section) {
            return response()->json(['success' => false, 'message' => 'Section not found'], 404);
        }

        $document = $section->document()->first();

        $validated = $request->validate([
            'action' => 'required|in:accepted,conditional,revision_requested,rejected,commented',
            'comment' => 'required_if:action,accepted,conditional,revision_requested,rejected|string',
            'conditions' => 'nullable|string',
        ]);

        $reviewerId = session('user_id');
        $reviewer = \App\Models\User::find($reviewerId);

        if (! $reviewer) {
            return response()->json(['success' => false, 'message' => 'Reviewer not found'], 404);
        }

        try {
            $this->service->decide(
                $document,
                $section,
                $reviewer,
                $validated['action'],
                $validated['comment'] ?? null,
                $validated['conditions'] ?? null
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Decision recorded successfully']);
    }
}
