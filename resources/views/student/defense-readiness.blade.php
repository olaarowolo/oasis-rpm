@extends('layouts.app')

@section('title', 'Defense Readiness')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Defense Readiness Checklist</h1>

    <div class="mb-6">
        <div class="bg-purple-50 rounded-lg p-6 inline-block">
            <div class="text-2xl font-bold text-purple-600">{{ $defenseScore }}%</div>
            <div class="text-gray-600">Defense Readiness Score</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requirement</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Topic Approval</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($hasApprovedTopic)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Pending</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($hasApprovedTopic) Research topic has been approved by supervisor @else No approved topic yet @endif
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Progress Completion</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($progressPercentage >= 80)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Ready</span>
                        @elseif($progressPercentage >= 50)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">In Progress</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Not Ready</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $progressPercentage }}% complete</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Final Document</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($hasFinalDocument)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Uploaded</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Pending</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($hasFinalDocument) Final manuscript uploaded @else Upload your final document @endif
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Meeting Logs</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($completedMeetings >= $requiredMeetings)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $completedMeetings }} / {{ $requiredMeetings }} required</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Resource Completion</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($completedResources >= $totalResources * 0.8)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">In Progress</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $completedResources }} / {{ $totalResources }} resources</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mt-6 flex items-center gap-4">
        <button onclick="window.location.href='{{ route('student.dashboard') }}'"
            class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
            @if($defenseScore < 70) disabled @endif>
            Submit for Defense
        </button>
        <a href="{{ route('student.dashboard') }}" class="text-sm text-blue-600 hover:text-blue-900">&larr; Back to Dashboard</a>
    </div>
</div>
@endsection
