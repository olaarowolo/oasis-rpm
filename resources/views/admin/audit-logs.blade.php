@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Audit Logs</h1>
        <a href="{{ route('admin.audit-logs.export', request()->only(['action_type', 'start_date', 'end_date', 'university_id'])) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded inline-block">
            Export Logs
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="flex space-x-4">
                <select name="action_type" class="block w-48 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Actions</option>
                    <option value="created" {{ request('action_type') === 'created' ? 'selected' : '' }}>Created</option>
                    <option value="updated" {{ request('action_type') === 'updated' ? 'selected' : '' }}>Updated</option>
                    <option value="deleted" {{ request('action_type') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                    <option value="viewed" {{ request('action_type') === 'viewed' ? 'selected' : '' }}>Viewed</option>
                    <option value="exported" {{ request('action_type') === 'exported' ? 'selected' : '' }}>Exported</option>
                </select>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="block w-48 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="block w-48 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">Filter</button>
            </form>
        </div>

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Changes</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($auditLogs as $log)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ optional($log->created_at)->format('Y-m-d H:i:s') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $log->user->name ?? 'Unknown' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $log->action === 'created' ? 'bg-green-100 text-green-800' :
                               ($log->action === 'updated' ? 'bg-blue-100 text-blue-800' :
                               ($log->action === 'deleted' ? 'bg-red-100 text-red-800' :
                               'bg-gray-100 text-gray-800')) }}">
                            {{ ucfirst($log->action) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->model_type }} #{{ $log->model_id }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        @php($changedKeys = array_keys((array) ($log->new_values ?? $log->old_values ?? [])))
                        {{ count($changedKeys) ? implode(', ', array_slice($changedKeys, 0, 5)) : '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-400">No audit log entries match the current filters.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $auditLogs->links() }}
        </div>
    </div>
</div>
@endsection
