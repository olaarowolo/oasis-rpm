<?php

namespace App\Http\Controllers;

use App\Mail\PortalEmail;
use App\Models\Proposal;
use App\Models\Student;
use App\Models\TopicHistory;
use App\Models\Supervisor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ProposalController extends BaseController
{
    public function submitProposal(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'abstract' => 'required|string',
        ]);

        $proposal = Proposal::create([
            'university_id' => session('university_id'),
            'student_id' => session('student_id'),
            'proposal_id' => 'PROP-' . Str::upper(Str::random(8)),
            'title' => $validated['title'],
            'location' => $validated['location'],
            'abstract' => $validated['abstract'],
            'date_submitted' => now(),
            'status' => 'pending',
        ]);

        TopicHistory::create([
            'university_id' => session('university_id'),
            'student_id' => session('student_id'),
            'proposal_id' => $proposal->id,
            'topic_title' => $validated['title'],
            'action' => 'submitted',
        ]);

        $student = Student::with(['user', 'supervisor.user'])->find(session('student_id'));
        $supervisor = $student?->supervisor;

        if ($supervisor?->user?->email) {
            $mail = new PortalEmail('topic-submitted', [
                'studentName' => $student->full_name,
                'studentEmail' => $student->user?->email ?? $student->email,
                'topic' => $validated['title'],
                'matric' => $student->matric_number,
                'proposalId' => $proposal->proposal_id,
                'abstract' => $validated['abstract'],
                'url' => route('supervisor.proposals'),
            ]);

            try {
                Mail::to($supervisor->user->email)->send($mail);
            } catch (\Throwable $exception) {
                Log::warning('Supervisor proposal submission email failed', [
                    'proposal_id' => $proposal->proposal_id,
                    'student_id' => $student?->id,
                    'supervisor_id' => $supervisor->id,
                    'recipient' => $supervisor->user->email,
                    'error' => $exception->getMessage(),
                ]);
            }
        } else {
            Log::warning('Supervisor proposal submission email skipped', [
                'proposal_id' => $proposal->proposal_id,
                'student_id' => $student?->id,
                'supervisor_id' => $student?->supervisor_id,
                'reason' => 'missing_supervisor_or_email',
            ]);
        }

        return $this->success($proposal, 'Proposal submitted successfully', 201);
    }

    public function listStudentProposals(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $proposals = Proposal::where([
            ['university_id', '=', session('university_id')],
            ['student_id', '=', session('student_id')],
        ])->orderByDesc('date_submitted')
            ->paginate($perPage);

        return $this->success($proposals, 'Proposals retrieved successfully');
    }

    public function getProposal(Request $request, $id)
    {
        $proposal = Proposal::find($id);
        if (!$proposal || $proposal->university_id != session('university_id') || ($proposal->student && $proposal->student->supervisor_id != session('supervisor_id'))) {
            return $this->error('Proposal not found', 404);
        }
        return $this->success($proposal, 'Proposal retrieved successfully');
    }

    public function updateProposal(Request $request, $id)
    {
        $proposal = Proposal::find($id);
        if (!$proposal || $proposal->university_id != session('university_id') || ($proposal->student && $proposal->student->supervisor_id != session('supervisor_id'))) {
            return $this->error('Proposal not found', 404);
        }
        if ($proposal->status !== 'pending') {
            return $this->error('Can only update pending proposals', 400);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'location' => 'sometimes|string|max:255',
            'abstract' => 'sometimes|string',
        ]);

        $proposal->update($validated);
        return $this->success($proposal, 'Proposal updated successfully');
    }

    public function studentGetProposal(Request $request, $id)
    {
        $proposal = Proposal::with(['topicHistory'])->where([
            ['university_id', '=', session('university_id')],
            ['student_id', '=', session('student_id')],
            ['id', '=', $id],
        ])->first();

        if (!$proposal) {
            return $this->error('Proposal not found', 404);
        }

        return $this->success($proposal, 'Proposal retrieved successfully');
    }

    public function studentUpdateProposal(Request $request, $id)
    {
        $proposal = Proposal::where([
            ['university_id', '=', session('university_id')],
            ['student_id', '=', session('student_id')],
            ['id', '=', $id],
        ])->first();

        if (!$proposal) {
            return $this->error('Proposal not found', 404);
        }

        // Students can only edit proposals that are revision_required or conditional
        if (!in_array($proposal->status, ['revision_required', 'conditional'], true)) {
            return $this->error('Can only edit proposals with revision required or conditional status', 400);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'location' => 'sometimes|string|max:255',
            'abstract' => 'sometimes|string',
        ]);

        $oldTitle = $proposal->title;

        $proposal->update($validated);

        TopicHistory::create([
            'university_id' => session('university_id'),
            'student_id' => session('student_id'),
            'proposal_id' => $proposal->id,
            'topic_title' => $proposal->title,
            'action' => 'revised',
            'note' => $oldTitle !== $validated['title'] ? 'Title changed from: ' . $oldTitle : 'Proposal revised by student',
        ]);

        // Notify supervisor of revision
        $student = $proposal->student;
        $supervisor = $student?->supervisor;

        if ($supervisor?->user?->email) {
            $mail = new PortalEmail('topic-revised', [
                'studentName' => $student->full_name,
                'studentEmail' => $student->user?->email ?? $student->email,
                'topic' => $proposal->title,
                'matric' => $student->matric_number,
                'proposalId' => $proposal->proposal_id,
                'url' => route('supervisor.proposals'),
            ]);

            try {
                Mail::to($supervisor->user->email)->send($mail);
            } catch (\Throwable $exception) {
                Log::warning('Supervisor proposal revision email failed', [
                    'proposal_id' => $proposal->proposal_id,
                    'student_id' => $student?->id,
                    'supervisor_id' => $supervisor->id,
                    'recipient' => $supervisor->user->email,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return $this->success($proposal, 'Proposal revised successfully');
    }

    public function studentDeleteProposal(Request $request, $id)
    {
        $proposal = Proposal::where([
            ['university_id', '=', session('university_id')],
            ['student_id', '=', session('student_id')],
            ['id', '=', $id],
        ])->first();

        if (!$proposal) {
            return $this->error('Proposal not found', 404);
        }

        // Students can only delete proposals that are not approved
        if ($proposal->status === 'approved') {
            return $this->error('Cannot delete approved proposal', 400);
        }

        $proposalId = $proposal->proposal_id;
        $proposalTitle = $proposal->title;

        $proposal->delete();

        TopicHistory::create([
            'university_id' => session('university_id'),
            'student_id' => session('student_id'),
            'proposal_id' => $id,
            'topic_title' => $proposalTitle,
            'action' => 'deleted',
            'note' => 'Proposal deleted by student',
        ]);

        return $this->success(null, 'Proposal deleted successfully');
    }

    public function listPendingProposals(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $proposals = Proposal::where([
            ['university_id', '=', session('university_id')],
            ['status', '=', 'pending'],
        ])->whereHas('student', function ($query) {
            $query->where('supervisor_id', session('supervisor_id'));
        })->with('student')->orderByDesc('date_submitted')
            ->paginate($perPage);

        return $this->success($proposals, 'Pending proposals retrieved successfully');
    }

    public function approveProposal(Request $request, $id)
    {
        $validated = $request->validate([
            'comment' => 'sometimes|string|nullable',
        ]);

        $proposal = Proposal::find($id);
        if (!$proposal || $proposal->university_id != session('university_id') || ($proposal->student && $proposal->student->supervisor_id != session('supervisor_id'))) {
            return $this->error('Proposal not found', 404);
        }

        $proposal->update([
            'status' => 'approved',
            'supervisor_comment' => $validated['comment'] ?? null,
        ]);

        $student = $proposal->student;
        $student->update([
            'research_topic' => $proposal->title,
            'research_topic_approved_date' => now(),
        ]);

        TopicHistory::create([
            'university_id' => session('university_id'),
            'student_id' => $proposal->student_id,
            'proposal_id' => $proposal->id,
            'topic_title' => $proposal->title,
            'action' => 'approved',
            'note' => $validated['comment'] ?? null,
        ]);

        // Send email notification to student
        $user = $student->user;
        $mail = new PortalEmail('topic-approved', [
            'studentName' => $student->full_name,
            'topic' => $proposal->title,
            'comment' => $validated['comment'] ?? 'Your proposal has been approved.',
            'approvedDate' => now()->format('F d, Y'),
            'url' => route('student.proposals'),
        ]);

        Mail::to($user->email)->send($mail);

        return $this->success($proposal, 'Proposal approved successfully');
    }

    public function rejectProposal(Request $request, $id)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
        ]);

        $proposal = Proposal::find($id);
        if (!$proposal || $proposal->university_id != session('university_id') || ($proposal->student && $proposal->student->supervisor_id != session('supervisor_id'))) {
            return $this->error('Proposal not found', 404);
        }

        $proposal->update([
            'status' => 'revision_required',
            'supervisor_comment' => $validated['comment'],
        ]);

        TopicHistory::create([
            'university_id' => session('university_id'),
            'student_id' => $proposal->student_id,
            'proposal_id' => $proposal->id,
            'topic_title' => $proposal->title,
            'action' => 'rejected',
            'note' => $validated['comment'],
        ]);

        // Send email notification to student
        $student = $proposal->student;
        $user = $student->user;
        $mail = new PortalEmail('topic-revision', [
            'studentName' => $student->full_name,
            'topic' => $proposal->title,
            'comment' => $validated['comment'],
            'url' => route('student.proposals'),
        ]);

        Mail::to($user->email)->send($mail);

        return $this->success($proposal, 'Proposal rejected with feedback');
    }

    public function requestRevision(Request $request, $id)
    {
        $validated = $request->validate([
            'conditions' => 'required|array',
        ]);

        $proposal = Proposal::find($id);
        if (!$proposal || $proposal->university_id != session('university_id') || ($proposal->student && $proposal->student->supervisor_id != session('supervisor_id'))) {
            return $this->error('Proposal not found', 404);
        }

        $proposal->update([
            'status' => 'conditional',
            'conditions' => $validated['conditions'],
        ]);

        // Send email notification to student
        $student = $proposal->student;
        $user = $student->user;
        $mail = new PortalEmail('topic-conditionally-approved', [
            'studentName' => $student->full_name,
            'topic' => $proposal->title,
            'conditions' => implode(', ', $validated['conditions']),
            'comment' => 'Your proposal has been conditionally approved with the following conditions.',
            'url' => route('student.proposals'),
        ]);

        Mail::to($user->email)->send($mail);

        return $this->success($proposal, 'Revision requested successfully');
    }
}
