@extends('layouts.app')

@section('title', 'Learning Resources')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Learning Resources</h1>

    <div class="mb-6">
        <div class="bg-blue-50 rounded-lg p-6 inline-block">
            <div class="text-2xl font-bold text-blue-600">{{ $completedResources }}</div>
            <div class="text-gray-600">Resources Completed</div>
        </div>
        <div class="bg-green-50 rounded-lg p-6 inline-block ml-4">
            <div class="text-2xl font-bold text-green-600">{{ $totalPoints }}</div>
            <div class="text-gray-600">Points Earned</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Section</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stage</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($resources as $resource)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $resource['title'] }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $resource['section'] }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Stage {{ $resource['stage'] }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($resource['type']) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="w-24 bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" 
                                @if($resource['progress_status'] === 'approved') style="width: 100%" 
                                @elseif($resource['progress_status'] === 'submitted') style="width: 75%" 
                                @elseif($resource['progress_status'] === 'in_progress') style="width: 50%" 
                                @else style="width: 25%" @endif></div>
                        </div>
                        <span class="text-xs text-gray-500">{{ $resource['progress_status'] }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ $resource['url'] }}" target="_blank" class="text-blue-600 hover:text-blue-900">Access</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
