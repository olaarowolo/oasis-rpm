@extends('layouts.supervisor')

@section('title', 'Student Roster')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Student Roster</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-blue-50 rounded-lg p-6">
            <div class="text-3xl font-bold text-blue-600">{{ $totalStudents }}</div>
            <div class="text-gray-600">Total Students</div>
        </div>
        <div class="bg-green-50 rounded-lg p-6">
            <div class="text-3xl font-bold text-green-600">{{ $activeStudents }}</div>
            <div class="text-gray-600">Active Students</div>
        </div>
        <div class="bg-purple-50 rounded-lg p-6">
            <div class="text-3xl font-bold text-purple-600">{{ $graduatedStudents }}</div>
            <div class="text-gray-600">Graduated Students</div>
        </div>
    </div>

    @if (session('import_result'))
        @php($import = session('import_result'))
        <div class="mb-6 rounded-lg border border-slate-200 bg-slate-50 p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h2 class="text-lg font-semibold text-slate-800">Bulk import results</h2>
                <span class="text-sm text-slate-600">
                    {{ $import['created'] ?? 0 }} created, {{ $import['skipped'] ?? 0 }} skipped
                    ({{ $import['duplicates'] ?? 0 }} duplicate), {{ count($import['errors'] ?? []) }} error(s)
                </span>
            </div>

            @if (!empty($import['errors']))
                <div class="mt-3 overflow-x-auto rounded-md border border-slate-200 bg-white">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium text-gray-600">CSV row</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-600">Identifier</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-600">Reason</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($import['errors'] as $error)
                                <tr>
                                    <td class="px-3 py-2 text-gray-800">{{ $error['row'] ?? '—' }}</td>
                                    <td class="px-3 py-2 text-gray-700 font-mono">{{ $error['identifier'] ?? '—' }}</td>
                                    <td class="px-3 py-2 text-gray-600">{{ $error['reason'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if (($import['created'] ?? 0) > 0)
                <p class="mt-3 text-sm text-emerald-700">Successfully onboarded {{ $import['created'] }} student(s). Their temporary passwords are not shown here; students sign in using their university code, matric number and surname.</p>
            @endif
        </div>
    @endif

    <div class="mb-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-800">Bulk import students (CSV)</h2>
        <p class="mt-1 text-sm text-slate-500">
            Upload a CSV to create multiple students at once. Each row is assigned to the
            default supervisor (you) unless a resolvable <code>supervisor_email</code> is provided.
            For structured universities (e.g. LASU), <code>faculty</code> and <code>department</code>
            are validated against the official department list.
        </p>

        <form method="POST" action="{{ route('supervisor.students.import') }}" enctype="multipart/form-data" class="mt-4 grid grid-cols-1 sm:grid-cols-2 items-end gap-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">CSV file</label>
                <input type="file" name="csv_file" accept=".csv,.txt,text/csv" required
                       class="block w-full text-sm text-slate-700 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-academic-600 file:text-white hover:file:bg-academic-700">
            </div>
            <div class="flex items-end gap-3">
                <button type="submit"
                        class="px-4 py-2.5 rounded-xl bg-academic-600 text-white text-sm font-semibold shadow hover:bg-academic-700 focus:outline-none focus:ring-2 focus:ring-academic-200">
                    Process CSV
                </button>
                <a href="{{ route('supervisor.students.import.template') }}"
                   class="text-sm text-academic-700 hover:underline">Download template</a>
            </div>
        </form>

        <details class="mt-4 group">
            <summary class="cursor-pointer text-sm font-medium text-slate-600 hover:text-slate-800">
                Required columns &amp; sample rows
            </summary>
            <pre class="mt-2 overflow-x-auto rounded-md bg-slate-50 p-3 text-xs text-slate-700">
full_name,lastname,matric_number,email,degree_level,phone,supervisor_email,faculty,department,programme,research_topic
Adewale,Ogunsiji,CS/2020/001,adewale.ogunsiji@universe.edu.ng,BSc,08030000001,,Science,Computer Science,BSc Computer Science,Machine Learning in Agriculture
            </pre>
        </details>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <input type="text" placeholder="Search students..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matric No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stage</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($students as $student)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $student->full_name }}</div>
                        <div class="text-sm text-gray-500">{{ $student->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->matric_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        Stage {{ $student->current_stage }}
                        <span class="text-xs text-gray-400">({{ $student->progress_percentage }}%)</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $student->status === 'active' ? 'bg-green-100 text-green-800' :
                               ($student->status === 'suspended' ? 'bg-red-100 text-red-800' :
                               'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($student->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ url('/api/supervisor/students/' . $student->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
