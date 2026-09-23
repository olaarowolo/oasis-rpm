@extends('layouts.app')

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
