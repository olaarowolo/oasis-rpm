<?php

namespace App\Http\Controllers;

use App\Mail\PortalEmail;
use App\Models\Proposal;
use App\Models\Student;
use App\Models\TopicHistory;
use App\Models\Supervisor;
use Illuminate\Http\Request;
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

        // Get student info
        $student = Student::find(session('student_id'));
        $user = $student->user;
        
        // Find supervisor for this student - try to find by matching university
        // and get the first active supervisor
        $supervisor = Supervisor::where('university_id', $student->university_id)
            ->where('is_active', true)
            ->first();
        
        if ($supervisor && $supervisor->user) {
            // Send email notification to supervisor
            $mail = new PortalEmail('topic-submitted', [
                'studentName' => $student->full_name,
                'studentEmail' => $user->email,
                'topic' => $validated['title'],
                'matric' => $student->matric_number,
                'proposalId' => $proposal->proposal_id,
                'abstract' => $validated['abstract'],
                'url' => route('supervisor.proposals'),
            ]);
            
            Mail::to($supervisor->user->email)->send($mail);
        }

        return $this->success($proposal, 'Proposal submitted successfully', 201);
    }

    public function listStudentProposals(Request $request)
    {
        $proposals = Proposal::where([
            ['university_id', '=', session('university_id')],
            ['student_id', '=', session('student_id')],
        ])->get();

        return $this->success($proposals, 'Proposals retrieved successfully');
    }

    public function getProposal(Request $request, $id)
    {
        $proposal = Proposal::find($id);
        if (!$proposal || $proposal->university_id != session('university_id')) {
            return $this->error('Proposal not found', 404);
        }
        return $this->success($proposal, 'Proposal retrieved successfully');
    }

    public function updateProposal(Request $request, $id)
    {
        $proposal = Proposal::find($id);
        if (!$proposal || $proposal->university_id != session('university_id')) {
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

    public function listPendingProposals(Request $request)
    {
        $proposals = Proposal::where([
            ['university_id', '=', session('university_id')],
            ['status', '=', 'pending'],
        ])->with('student')->get();

        return $this->success($proposals, 'Pending proposals retrieved successfully');
    }

    public function approveProposal(Request $request, $id)
    {
        $validated = $request->validate([
            'comment' => 'sometimes|string|nullable',
        ]);

        $proposal = Proposal::find($id);
        if (!$proposal || $proposal->university_id != session('university_id')) {
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
        if (!$proposal || $proposal->university_id != session('university_id')) {
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
        if (!$proposal || $proposal->university_id != session('university_id')) {
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
