@extends('layouts.app')

@section('title', 'Manage Resources')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Manage Resources</h1>
        <button onclick="window.location.href='{{ route('admin.resources.create', ['university_id' => $selectedUniversityId ?? null]) }}'" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            Add Resource
        </button>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stage</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Points</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mandatory</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($resources as $resource)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $resource->title }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $resource->type === 'video' ? 'bg-red-100 text-red-800' :
                               ($resource->type === 'document' ? 'bg-gray-100 text-gray-800' :
                               ($resource->type === 'link' ? 'bg-blue-100 text-blue-800' :
                               'bg-purple-100 text-purple-800')) }}">
                            {{ ucfirst($resource->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Stage {{ $resource->stage }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $resource->points }} pts</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($resource->is_mandatory)
                            <span class="text-green-600 font-semibold">Yes</span>
                        @else
                            <span class="text-gray-400">No</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.resources.edit', $resource->id) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                        <form class="inline-block" action="{{ route('admin.resources.destroy', $resource->id) }}" method="POST" onsubmit="return confirm('Delete this resource? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 ml-2">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-400">No resources yet. Click "Add Resource" to create one.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
