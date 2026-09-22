@extends('layouts.app')

@section('title', 'My Proposals')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">My Proposals</h1>

    <div class="mb-6">
        <button onclick="window.location.href='{{ route('student.proposals.create') }}'" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            Create New Proposal
        </button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Submitted</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($proposals as $proposal)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $proposal->title }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $proposal->date_submitted->format('Y-m-d') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $proposal->location }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $proposal->status === 'approved' ? 'bg-green-100 text-green-800' :
                               $proposal->status === 'revision_required' ? 'bg-yellow-100 text-yellow-800' :
                               $proposal->status === 'pending' ? 'bg-blue-100 text-blue-800' :
                               'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst(str_replace('_', ' ', $proposal->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('student.proposals.view', $proposal->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
