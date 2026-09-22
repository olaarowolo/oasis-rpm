@extends('layouts.app')

@section('title', 'Meeting Logs')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Meeting Logs</h1>

    <div class="mb-6">
        <button onclick="window.location.href='{{ route('supervisor.meetings.create') }}'" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            Schedule New Meeting
        </button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meeting #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($meetings as $meeting)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $meeting->meeting_date->format('Y-m-d') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $meeting->student->full_name ?? 'Unknown' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#{{ $meeting->meeting_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $meeting->meeting_mode)) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $meeting->status === 'approved' ? 'bg-green-100 text-green-800' :
                               $meeting->status === 'draft' ? 'bg-yellow-100 text-yellow-800' :
                               'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst(str_replace('_', ' ', $meeting->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('supervisor.meetings.view', $meeting->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
