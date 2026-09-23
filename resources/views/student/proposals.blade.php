@extends('layouts.app')

@section('title', 'My Proposals')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800">My Proposals</h1>
        <a href="{{ route('student.dashboard') }}" class="text-sm text-blue-600 hover:text-blue-900">&larr; Back to Dashboard</a>
    </div>

    <div class="mb-6">
        <button onclick="window.location.href='{{ route('student.dashboard') }}'" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
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
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($proposals as $proposal)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $proposal->title }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ optional($proposal->date_submitted)->format('Y-m-d') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $proposal->location }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $proposal->status === 'approved' ? 'bg-green-100 text-green-800' :
                               ($proposal->status === 'revision_required' ? 'bg-yellow-100 text-yellow-800' :
                               ($proposal->status === 'pending' ? 'bg-blue-100 text-blue-800' :
                               'bg-gray-100 text-gray-800')) }}">
                            {{ ucfirst(str_replace('_', ' ', $proposal->status)) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-400">No proposals yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
