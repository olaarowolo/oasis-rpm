@extends('layouts.supervisor')

@section('title', 'Log a Meeting')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Log a Meeting</h1>
        <a href="{{ route('supervisor.meetings') }}" class="text-sm text-blue-600 hover:text-blue-900">&larr; Back to Meeting Logs</a>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('supervisor.meetings.store') }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-5">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Student</label>
                <select name="student_id" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Select student</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}" {{ (string) old('student_id') === (string) $student->id ? 'selected' : '' }}>
                            {{ $student->full_name }} ({{ $student->matric_number }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Meeting number</label>
                <input type="number" name="meeting_number" min="1" value="{{ old('meeting_number', 1) }}" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Meeting date</label>
                <input type="date" name="meeting_date" value="{{ old('meeting_date') }}" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mode</label>
                <select name="meeting_mode" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach (['in_person' => 'In person', 'virtual' => 'Virtual', 'hybrid' => 'Hybrid'] as $value => $label)
                        <option value="{{ $value }}" {{ old('meeting_mode') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes)</label>
                <input type="number" name="duration" min="1" value="{{ old('duration', 30) }}" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Previous actions</label>
            <textarea name="previous_actions" rows="2" required
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('previous_actions') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Progress since last meeting</label>
            <textarea name="progress_since" rows="2" required
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('progress_since') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Discussion points</label>
            <textarea name="discussion_points" rows="3" required
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('discussion_points') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Work reviewed</label>
            <textarea name="work_reviewed" rows="2" required
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('work_reviewed') }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Chapter focus</label>
                <input type="text" name="chapter_focus" value="{{ old('chapter_focus') }}" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Next meeting focus</label>
                <input type="text" name="next_meeting_focus" value="{{ old('next_meeting_focus') }}" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Risks (optional)</label>
                <textarea name="risks" rows="2"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('risks') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Support required (optional)</label>
                <textarea name="support_required" rows="2"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('support_required') }}</textarea>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Next meeting date</label>
            <input type="date" name="next_meeting_date" value="{{ old('next_meeting_date') }}" required
                class="block w-full md:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded font-semibold">Save meeting log</button>
            <a href="{{ route('supervisor.meetings') }}" class="px-5 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold">Cancel</a>
        </div>
    </form>
</div>
@endsection
