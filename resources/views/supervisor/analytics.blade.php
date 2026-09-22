@extends('layouts.app')

@section('title', 'Supervisor Analytics')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Supervisor Analytics</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-blue-50 rounded-lg p-6">
            <div class="text-3xl font-bold text-blue-600">{{ $totalStudents }}</div>
            <div class="text-gray-600">Total Students</div>
        </div>
        <div class="bg-green-50 rounded-lg p-6">
            <div class="text-3xl font-bold text-green-600">{{ $completedMeetings }}</div>
            <div class="text-gray-600">Meetings Completed</div>
        </div>
        <div class="bg-purple-50 rounded-lg p-6">
            <div class="text-3xl font-bold text-purple-600">{{ $approvedProposals }}</div>
            <div class="text-gray-600">Proposals Approved</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Student Progress by Stage</h2>
            <div class="space-y-4">
                @foreach($stageDistribution as $stage => $count)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">Stage {{ $stage }}</span>
                        <span class="text-sm font-medium text-gray-700">{{ $count }} students</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ ($count / $totalStudents) * 100 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Student Status Distribution</h2>
            <div class="space-y-4">
                @foreach($statusDistribution as $status => $count)
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                    <div class="flex items-center">
                        <div class="w-32 bg-gray-200 rounded-full h-2.5 mx-4">
                            <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ ($count / $totalStudents) * 100 }}%"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-700">{{ $count }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Recent Activity</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($recentActivity as $activity)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $activity['date'] }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $activity['message'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
