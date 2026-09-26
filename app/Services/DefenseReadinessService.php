<?php

namespace App\Services;

use App\Models\Student;
use App\Models\DefenseReadinessDocument;
use App\Models\DefenseReadinessSection;
use App\Models\DefenseReadinessSectionVersion;
use App\Models\DefenseReadinessReview;
use App\Models\Notification;
use App\Models\User;
use App\Mail\PortalEmail;
use App\Support\HtmlSanitizer;
use App\Support\WordCounter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DefenseReadinessService
{
    protected HtmlSanitizer $sanitizer;
    protected WordCounter $wordCounter;
    protected array $config;

    public function __construct(
        ?HtmlSanitizer $sanitizer = null,
        ?WordCounter $wordCounter = null,
    ) {
        $this->sanitizer = $sanitizer ?? new HtmlSanitizer();
        $this->wordCounter = $wordCounter ?? new WordCounter();
    }

    protected function config(): array
    {
        if (! isset($this->config)) {
            $this->config = config('defense_readiness');
        }
        return $this->config;
    }

    /**
     * Get (or create) the manuscript document for a student, seeding
     * sections from config on first creation.
     */
    public function documentForStudent(Student $student): DefenseReadinessDocument
    {
        $document = DefenseReadinessDocument::firstOrCreate(
            ['student_id' => $student->id],
            [
                'university_id' => $student->university_id,
                'study_approach' => null,
                'primary_data_collection' => false,
                'status' => 'draft',
            ]
        );

        if ($document->relationLoaded('sections') && $document->sections->isNotEmpty()) {
            return $document;
        }

        $document = $document->fresh();

        if ($document->sections()->doesntExist()) {
            $this->seedSections($document, $student);
            $document->load('sections');
        }

        return $document->load('sections');
    }

    /**
     * Seed top-level sections and methodology children from config.
     */
    protected function seedSections(DefenseReadinessDocument $document, Student $student): void
    {
        $config = $this->config();

        $position = 0;
        foreach ($config['sections'] as $sectionConfig) {
            $position++;

            $isGroup = $sectionConfig['is_group'] ?? false;

            $section = $document->sections()->create([
                'document_id' => $document->id,
                'parent_id' => null,
                'key' => $sectionConfig['key'],
                'title' => $sectionConfig['title'],
                'position' => $position,
                'kind' => $isGroup ? 'group' : 'fixed',
                'guidance' => $sectionConfig['guidance'] ?? null,
                'target_min_words' => $sectionConfig['target_min_words'] ?? null,
                'target_max_words' => $sectionConfig['target_max_words'] ?? null,
                'word_count_label' => $sectionConfig['word_count_label'] ?? null,
                'content' => null,
                'word_count' => 0,
                'status' => 'locked',
            ]);

            if ($isGroup && $sectionConfig['key'] === 'methodology') {
                $this->seedMethodologyChildren($document, $section, $student);
            }
        }

        // Unlock the first top-level section.
        $document->sections()->where('position', 1)->update(['status' => 'draft']);
    }

    /**
     * Seed methodology subheadings as children of the methodology group.
     */
    protected function seedMethodologyChildren(DefenseReadinessDocument $document, DefenseReadinessSection $methodology, Student $student): void
    {
        $children = $this->config()['methodology_children'] ?? [];

        foreach ($children as $childConfig) {
            // Ethics sub-section only exists when primary_data_collection is true.
            if (isset($childConfig['conditional_on']) && $childConfig['conditional_on'] === 'primary_data_collection') {
                if (! $document->primary_data_collection) {
                    continue;
                }
            }

            $methodology->children()->create([
                'document_id' => $document->id,
                'parent_id' => $methodology->id,
                'key' => $childConfig['key'],
                'title' => $childConfig['title'],
                'position' => $childConfig['position'],
                'kind' => 'fixed',
                'guidance' => $this->resolveMethodologyGuidance($childConfig['guidance'], $document->study_approach ?? 'default'),
                'target_min_words' => null,
                'target_max_words' => null,
                'word_count_label' => null,
                'content' => null,
                'word_count' => 0,
                'status' => 'locked',
            ]);
        }
    }

    /**
     * Resolve approach-specific guidance for methodology children.
     */
    protected function resolveMethodologyGuidance(mixed $guidance, string $approach): string
    {
        if (is_array($guidance)) {
            if ($approach && isset($guidance[$approach])) {
                return $guidance[$approach];
            }
            return $guidance['default'] ?? '';
        }

        return (string) $guidance;
    }

    /**
     * Check whether a section is unlocked for the student to edit.
     */
    public function isUnlocked(DefenseReadinessDocument $document, DefenseReadinessSection $section): bool
    {
        if ($document->status === 'halted') {
            return false;
        }

        $siblings = $document->sections()
            ->where(function ($query) use ($section) {
                if ($section->parent_id === null) {
                    $query->whereNull('parent_id');
                } else {
                    $query->where('parent_id', $section->parent_id);
                }
            })
            ->orderBy('position')
            ->get();

        // If this is a child, the parent group must not be locked.
        if ($section->parent_id !== null) {
            $parent = $section->parent()->first() ?? $document->sections()->where('id', $section->parent_id)->first();
            if ($parent && $parent->status === 'locked') {
                return false;
            }
        }

        // All preceding siblings must be accepted or conditional.
        foreach ($siblings as $sibling) {
            if ($sibling->id === $section->id) {
                break;
            }
            if (! in_array($sibling->status, ['accepted', 'conditional'], true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Walk all sections and flip locked ⇄ draft according to the gating rule.
     */
    public function refreshLocks(DefenseReadinessDocument $document): void
    {
        $sections = $document->sections()
            ->orderByRaw("CASE WHEN parent_id IS NULL THEN 0 ELSE parent_id END, position")
            ->get();

        foreach ($sections as $section) {
            if (in_array($section->status, ['submitted', 'accepted', 'conditional', 'revision_requested', 'rejected'], true)) {
                continue;
            }

            // At this point status is either 'locked' or 'draft'.
            if ($this->isUnlocked($document, $section)) {
                if ($section->status === 'locked') {
                    $section->update(['status' => 'draft']);
                }
            } else {
                if ($section->status === 'draft') {
                    $section->update(['status' => 'locked']);
                }
            }
        }
    }

    /**
     * Check if the document settings can still be edited.
     */
    public function canUpdateSettings(DefenseReadinessDocument $document): bool
    {
        return $document->canEditSettings();
    }

    /**
     * Update study approach and primary data collection toggle.
     * Only allowed while draft and no section has ever been submitted.
     */
    public function updateSettings(DefenseReadinessDocument $document, string $approach, bool $primaryDataCollection): bool
    {
        if (! $this->canUpdateSettings($document)) {
            return false;
        }

        $document->update([
            'study_approach' => $approach,
            'primary_data_collection' => $primaryDataCollection,
        ]);

        // Rewrite methodology child guidance for approach-specific sub-sections.
        $this->rewriteMethodologyGuidance($document, $approach);

        // Handle ethics sub-section creation/deletion.
        $this->syncEthicsSubsection($document, $primaryDataCollection);

        return true;
    }

    /**
     * Rewrite approach-specific guidance text on methodology children 4, 5, 6.
     */
    protected function rewriteMethodologyGuidance(DefenseReadinessDocument $document, string $approach): void
    {
        $children = $this->config()['methodology_children'] ?? [];

        foreach ($children as $childConfig) {
            if (! is_array($childConfig['guidance'] ?? null)) {
                continue;
            }

            $guidance = $childConfig['guidance'][$approach] ?? $childConfig['guidance']['default'] ?? '';

            $document->sections()
                ->where('parent_id', function ($query) use ($document) {
                    $query->select('id')
                        ->from('defense_readiness_sections')
                        ->where('document_id', $document->id)
                        ->where('key', 'methodology')
                        ->limit(1);
                })
                ->where('key', $childConfig['key'])
                ->update(['guidance' => $guidance]);
        }
    }

    /**
     * Add or remove the ethics sub-section based on primary_data_collection toggle.
     */
    protected function syncEthicsSubsection(DefenseReadinessDocument $document, bool $primaryDataCollection): void
    {
        $ethicsSection = $document->sections()
            ->whereHas('parent', function ($query) {
                $query->where('key', 'methodology');
            })
            ->where('key', 'methodology_ethics')
            ->first();

        if ($primaryDataCollection && ! $ethicsSection) {
            $methodology = $document->sections()->where('key', 'methodology')->first();
            if ($methodology) {
                $maxPosition = $document->sections()
                    ->where('parent_id', $methodology->id)
                    ->max('position');

                $ethicsConfig = collect($this->config()['methodology_children'])
                    ->firstWhere('key', 'methodology_ethics');

                $document->sections()->create([
                    'document_id' => $document->id,
                    'parent_id' => $methodology->id,
                    'key' => 'methodology_ethics',
                    'title' => $ethicsConfig['title'] ?? 'Ethical considerations',
                    'position' => $maxPosition + 1,
                    'kind' => 'fixed',
                    'guidance' => $ethicsConfig['guidance'] ?? null,
                    'target_min_words' => null,
                    'target_max_words' => null,
                    'word_count_label' => null,
                    'content' => null,
                    'word_count' => 0,
                    'status' => 'locked',
                ]);
            }
        }

        if (! $primaryDataCollection && $ethicsSection && $ethicsSection->versions()->doesntExist()) {
            $ethicsSection->delete();
        }
    }

    /**
     * Autosave section content. Returns [saved => bool, word_count => int, conflict => bool].
     */
    public function autosaveSection(
        DefenseReadinessDocument $document,
        DefenseReadinessSection $section,
        ?string $content,
        ?string $lastSavedAt = null
    ): array {
        // Server-side gate: reject edits on locked/submitted sections or halted docs.
        if ($document->status === 'halted') {
            return ['saved' => false, 'error' => 'Document is halted', 'code' => 403];
        }

        if (! $section->isEditable()) {
            return ['saved' => false, 'error' => 'Section is not in an editable state', 'code' => 403];
        }

        // Optimistic concurrency check.
        if ($lastSavedAt !== null) {
            $serverUpdated = $section->updated_at;
            if ($serverUpdated && $serverUpdated->gt($lastSavedAt) && $serverUpdated->diffInSeconds($lastSavedAt) > 5) {
                return [
                    'saved' => false,
                    'conflict' => true,
                    'server_updated_at' => $serverUpdated->toIso8601String(),
                ];
            }
        }

        $clean = $this->sanitizer->clean($content);
        $wordCount = $this->wordCounter->count($clean);

        $section->update([
            'content' => $clean,
            'word_count' => $wordCount,
        ]);

        return [
            'saved' => true,
            'word_count' => $wordCount,
            'saved_at' => $section->updated_at->toIso8601String(),
            'status' => $section->status,
        ];
    }

    /**
     * Submit a section for supervisor review. Creates an immutable version snapshot.
     */
    public function submitSection(
        DefenseReadinessDocument $document,
        DefenseReadinessSection $section,
        User $actor
    ): DefenseReadinessSectionVersion {
        if ($document->status === 'halted') {
            throw new \InvalidArgumentException('Document is halted');
        }

        if ($section->status !== 'draft') {
            throw new \InvalidArgumentException('Section must be in draft status to submit');
        }

        if ($section->isGroup()) {
            throw new \InvalidArgumentException('Group sections cannot be submitted directly');
        }

        $content = $this->sanitizer->clean($section->content);
        $wordCount = $this->wordCounter->count($content);

        // Determine the next version number.
        $lastVersion = $section->versions()->max('version_number');
        $versionNumber = $lastVersion ? $lastVersion + 1 : 1;

        $version = DefenseReadinessSectionVersion::create([
            'section_id' => $section->id,
            'document_id' => $document->id,
            'version_number' => $versionNumber,
            'content' => $content,
            'word_count' => $wordCount,
            'submitted_by_user_id' => $actor->id,
        ]);

        $section->update([
            'current_version_id' => $version->id,
            'content' => $content,
            'word_count' => $wordCount,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $document->update(['status' => 'in_review']);

        // Fire notification + email to the supervisor.
        $this->notifySectionSubmitted($document, $section, $actor);

        return $version;
    }

    /**
     * Process a supervisor decision on a submitted section.
     */
    public function decide(
        DefenseReadinessDocument $document,
        DefenseReadinessSection $section,
        User $reviewer,
        string $action,
        ?string $comment,
        ?string $conditions
    ): void {
        $validActions = ['accepted', 'conditional', 'revision_requested', 'rejected', 'commented'];
        if (! in_array($action, $validActions, true)) {
            throw new \InvalidArgumentException('Invalid decision action');
        }

        // Decisions (except 'commented') require a comment.
        if ($action !== 'commented' && ($comment === null || trim($comment) === '')) {
            throw new \InvalidArgumentException('Comment is required for this action');
        }

        $reviewData = [
            'section_id' => $section->id,
            'document_id' => $document->id,
            'version_id' => $section->current_version_id,
            'reviewer_user_id' => $reviewer->id,
            'action' => $action,
            'comment' => $comment,
            'conditions' => $action === 'conditional' ? $conditions : null,
        ];

        DefenseReadinessReview::create($reviewData);

        if ($action === 'commented') {
            $this->notifySectionCommented($document, $section, $reviewer, $comment);
            return;
        }

        $section->update([
            'status' => $action,
            'decided_at' => now(),
        ]);

        if ($action === 'conditional') {
            $section->update(['conditions_acknowledged_at' => null]);
        }

        // Handle rejection: halt the entire document.
        if ($action === 'rejected') {
            $document->update([
                'status' => 'halted',
                'halted_at' => now(),
                'halted_section_id' => $section->id,
                'halted_reason' => $comment,
            ]);

            $this->notifySectionRejected($document, $section, $reviewer, $comment);
            return;
        }

        // Handle revision_requested on the halted section: release the halt.
        if ($action === 'revision_requested' && $document->status === 'halted' && $document->halted_section_id === $section->id) {
            $this->releaseHalt($document);
        }

        // Accepted and conditional unlock the next section.
        if (in_array($action, ['accepted', 'conditional'], true)) {
            $this->refreshLocks($document);
        }

        // Notify the student.
        $this->notifySectionDecided($document, $section, $reviewer, $action, $comment, $conditions);

        // Check if the last section was accepted → complete the document.
        $this->checkCompletion($document);
    }

    /**
     * Release a halted document. Called when revision is requested on the halted section.
     */
    public function releaseHalt(DefenseReadinessDocument $document): void
    {
        // Find the halted section and make it editable.
        if ($document->halted_section_id) {
            $haltedSection = DefenseReadinessSection::find($document->halted_section_id);
            if ($haltedSection) {
                $haltedSection->update(['status' => 'revision_requested']);
            }
        }

        $document->update([
            'status' => 'in_revision',
            'halted_at' => null,
            'halted_section_id' => null,
            'halted_reason' => null,
        ]);
    }

    /**
     * Check if the document is complete (last section accepted).
     */
    protected function checkCompletion(DefenseReadinessDocument $document): void
    {
        $lastSection = $this->lastSectionInOrder($document);

        if ($lastSection && $lastSection->status === 'accepted' && $document->status !== 'completed') {
            $document->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            $this->notifyDocumentCompleted($document);
        }
    }

    /**
     * Get the last section in document order (flattened).
     */
    protected function lastSectionInOrder(DefenseReadinessDocument $document): ?DefenseReadinessSection
    {
        $sections = $document->sections()
            ->orderByRaw("CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END")
            ->orderBy('parent_id', 'asc')
            ->orderBy('position', 'asc')
            ->get();

        // Flatten: top-level sections, and children nested under their group.
        $flattened = [];
        foreach ($sections as $section) {
            if ($section->parent_id === null) {
                $flattened[] = $section;
                $children = $document->sections()
                    ->where('parent_id', $section->id)
                    ->orderBy('position')
                    ->get();
                foreach ($children as $child) {
                    $flattened[] = $child;
                }
            }
        }

        return $flattened ? $flattened[count($flattened) - 1] : null;
    }

    /**
     * Student acknowledges that conditional approval conditions have been addressed.
     */
    public function acknowledgeConditions(DefenseReadinessDocument $document, DefenseReadinessSection $section): bool
    {
        if ($section->status !== 'conditional') {
            return false;
        }

        $section->update(['conditions_acknowledged_at' => now()]);

        // Notify the supervisor that conditions were addressed.
        $student = $document->student()->first();
        $supervisor = $student?->supervisor()->with('user')->first();

        if ($supervisor?->user) {
            Notification::create([
                'university_id' => $document->university_id,
                'user_id' => $supervisor->user_id,
                'type' => Notification::TYPE_DEFENSE_CONDITIONS_ACKED,
                'title' => 'Conditions Addressed',
                'message' => $student->full_name . ' has indicated that the conditions for ' . $section->title . ' have been addressed.',
                'is_read' => false,
                'metadata' => [
                    'document_id' => $document->id,
                    'section_id' => $section->id,
                    'section_title' => $section->title,
                    'action_url' => route('supervisor.manuscripts.show', $document),
                ],
            ]);
        }

        return true;
    }

    /**
     * Add a custom section before References.
     */
    public function addCustomSection(DefenseReadinessDocument $document, string $title, ?string $guidance = null): DefenseReadinessSection
    {
        // References is always the last top-level section. Insert before it.
        $references = $document->sections()
            ->where('key', 'references')
            ->first();

        if ($references) {
            // Shift positions of sections after the new custom section.
            $document->sections()
                ->where('position', '>', $references->position - 1)
                ->whereNull('parent_id')
                ->increment('position');
        }

        $maxPosition = $document->sections()
            ->whereNull('parent_id')
            ->max('position');

        $sectionCount = $document->sections()->whereNull('parent_id')->count();

        return $document->sections()->create([
            'document_id' => $document->id,
            'parent_id' => null,
            'key' => 'custom_' . \Str::random(6),
            'title' => $title,
            'position' => $maxPosition + 1,
            'kind' => 'custom',
            'guidance' => $guidance,
            'target_min_words' => null,
            'target_max_words' => null,
            'word_count_label' => null,
            'content' => null,
            'word_count' => 0,
            'status' => 'locked',
        ]);
    }

    /**
     * Delete a custom section (only if draft with zero versions).
     */
    public function deleteCustomSection(DefenseReadinessDocument $document, DefenseReadinessSection $section): bool
    {
        if ($section->kind !== 'custom') {
            return false;
        }

        if ($section->status !== 'draft' && $section->status !== 'locked') {
            return false;
        }

        if ($section->versions()->exists()) {
            return false;
        }

        // Re-number positions.
        $document->sections()
            ->whereNull('parent_id')
            ->where('position', '>', $section->position)
            ->decrement('position');

        $section->delete();

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Notification + Email helpers
    |--------------------------------------------------------------------------
    */

    protected function studentRecipient(DefenseReadinessDocument $document): ?User
    {
        $student = $document->student()->first();
        return $student?->user;
    }

    protected function supervisorRecipient(DefenseReadinessDocument $document): ?User
    {
        $student = $document->student()->first();
        $supervisor = $student?->supervisor()->with('user')->first();
        return $supervisor?->user;
    }

    protected function universityCode(DefenseReadinessDocument $document): string
    {
        return strtoupper((string) ($document->university?->code ?? config('universities.default', 'LASU')));
    }

    protected function notifySectionSubmitted(DefenseReadinessDocument $document, DefenseReadinessSection $section, User $actor): void
    {
        $student = $document->student()->first();
        $supervisor = $student?->supervisor()->with('user')->first();

        if (! $supervisor?->user) {
            Log::warning('Defense section submission: no supervisor email to notify', [
                'document_id' => $document->id,
                'student_id' => $document->student_id,
            ]);
            return;
        }

        $url = route('supervisor.manuscripts.show', ['document' => $document->id]) . '?section=' . $section->id;

        Notification::create([
            'university_id' => $document->university_id,
            'user_id' => $supervisor->user->id,
            'type' => Notification::TYPE_DEFENSE_SECTION_SUBMITTED,
            'title' => 'Section submitted for review',
            'message' => $student->full_name . ' has submitted "' . $section->title . '" for your review.',
            'is_read' => false,
            'metadata' => [
                'document_id' => $document->id,
                'section_id' => $section->id,
                'section_title' => $section->title,
                'reviewer_name' => $student->full_name,
                'action' => 'submitted',
                'action_url' => $url,
            ],
        ]);

        $this->sendEmail($supervisor->user->email, 'defense-section-submitted', [
            'universityCode' => $this->universityCode($document),
            'studentName' => $student->full_name,
            'matric' => $student->matric_number ?? '',
            'sectionTitle' => $section->title,
            'comment' => '',
            'url' => $url,
        ]);
    }

    protected function notifySectionDecided(DefenseReadinessDocument $document, DefenseReadinessSection $section, User $reviewer, string $action, ?string $comment, ?string $conditions): void
    {
        $student = $document->student()->first();
        $studentUser = $student?->user;

        if (! $studentUser) {
            return;
        }

        $map = [
            'accepted' => [
                'type' => Notification::TYPE_DEFENSE_SECTION_ACCEPTED,
                'title' => 'Section Accepted',
                'template' => 'defense-section-accepted',
                'label' => 'ACCEPTED',
            ],
            'conditional' => [
                'type' => Notification::TYPE_DEFENSE_SECTION_CONDITIONAL,
                'title' => 'Section Conditionally Approved',
                'template' => 'defense-section-conditional',
                'label' => 'CONDITIONALLY APPROVED',
            ],
            'revision_requested' => [
                'type' => Notification::TYPE_DEFENSE_SECTION_REVISION,
                'title' => 'Revision Requested',
                'template' => 'defense-section-revision',
                'label' => 'REVISION REQUESTED',
            ],
        ];

        $info = $map[$action];
        $url = route('student.defense-readiness', ['section' => $section->id]);

        Notification::create([
            'university_id' => $document->university_id,
            'user_id' => $studentUser->id,
            'type' => $info['type'],
            'title' => $info['title'],
            'message' => 'Your "' . $section->title . '" section has been ' . $info['label'] . ' by ' . $reviewer->name . '.',
            'is_read' => false,
            'metadata' => [
                'document_id' => $document->id,
                'section_id' => $section->id,
                'section_title' => $section->title,
                'reviewer_name' => $reviewer->name,
                'action' => $action,
                'action_url' => $url,
            ],
        ]);

        $this->sendEmail($studentUser->email, $info['template'], [
            'universityCode' => $this->universityCode($document),
            'studentName' => $student->full_name,
            'matric' => $student->matric_number ?? '',
            'sectionTitle' => $section->title,
            'comment' => $comment ?? '',
            'conditions' => $conditions ?? '',
            'reviewerName' => $reviewer->name,
            'statusLabel' => $info['label'],
            'url' => $url,
        ]);
    }

    protected function notifySectionRejected(DefenseReadinessDocument $document, DefenseReadinessSection $section, User $reviewer, string $comment): void
    {
        $student = $document->student()->first();
        $studentUser = $student?->user;

        if (! $studentUser) {
            return;
        }

        $url = route('student.defense-readiness', ['section' => $section->id]);

        Notification::create([
            'university_id' => $document->university_id,
            'user_id' => $studentUser->id,
            'type' => Notification::TYPE_DEFENSE_SECTION_REJECTED,
            'title' => 'Section Rejected — Document Halted',
            'message' => 'Your "' . $section->title . '" section has been rejected by ' . $reviewer->name . '. The manuscript has been halted.',
            'is_read' => false,
            'metadata' => [
                'document_id' => $document->id,
                'section_id' => $section->id,
                'section_title' => $section->title,
                'reviewer_name' => $reviewer->name,
                'action' => 'rejected',
                'action_url' => $url,
            ],
        ]);

        $this->sendEmail($studentUser->email, 'defense-section-rejected', [
            'universityCode' => $this->universityCode($document),
            'studentName' => $student->full_name,
            'matric' => $student->matric_number ?? '',
            'sectionTitle' => $section->title,
            'comment' => $comment ?? '',
            'reviewerName' => $reviewer->name,
            'url' => $url,
        ]);
    }

    protected function notifySectionCommented(DefenseReadinessDocument $document, DefenseReadinessSection $section, User $reviewer, ?string $comment): void
    {
        $student = $document->student()->first();
        $studentUser = $student?->user;

        if (! $studentUser) {
            return;
        }

        $url = route('supervisor.manuscripts.show', ['document' => $document->id]) . '?section=' . $section->id;

        Notification::create([
            'university_id' => $document->university_id,
            'user_id' => $studentUser->id,
            'type' => Notification::TYPE_DEFENSE_COMMENT,
            'title' => 'New Comment',
            'message' => $reviewer->name . ' left a comment on your "' . $section->title . '" section.',
            'is_read' => false,
            'metadata' => [
                'document_id' => $document->id,
                'section_id' => $section->id,
                'section_title' => $section->title,
                'reviewer_name' => $reviewer->name,
                'action' => 'commented',
                'action_url' => route('student.defense-readiness', ['section' => $section->id]),
            ],
        ]);

        $this->sendEmail($studentUser->email, 'defense-comment', [
            'universityCode' => $this->universityCode($document),
            'studentName' => $student->full_name,
            'matric' => $student->matric_number ?? '',
            'sectionTitle' => $section->title,
            'comment' => $comment ?? '',
            'reviewerName' => $reviewer->name,
            'url' => $url,
        ]);
    }

    protected function notifyDocumentCompleted(DefenseReadinessDocument $document): void
    {
        $student = $document->student()->first();
        $studentUser = $student?->user;

        if (! $studentUser) {
            return;
        }

        Notification::create([
            'university_id' => $document->university_id,
            'user_id' => $studentUser->id,
            'type' => Notification::TYPE_DEFENSE_COMPLETED,
            'title' => 'Manuscript Ready',
            'message' => 'All sections of your manuscript have been accepted. You can now proceed to defense submission.',
            'is_read' => false,
            'metadata' => [
                'document_id' => $document->id,
                'action_url' => route('student.defense-readiness'),
            ],
        ]);

        $this->sendEmail($studentUser->email, 'defense-completed', [
            'universityCode' => $this->universityCode($document),
            'studentName' => $student->full_name,
            'matric' => $student->matric_number ?? '',
            'url' => route('student.defense-readiness'),
        ]);
    }

    protected function sendEmail(string $to, string $viewName, array $data): void
    {
        try {
            Mail::to($to)->send(new PortalEmail($viewName, $data));
        } catch (\Throwable $e) {
            Log::warning('Defense readiness email failed: ' . $e->getMessage(), [
                'view' => $viewName,
                'recipient' => $to,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
