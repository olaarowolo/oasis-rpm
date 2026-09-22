@extends('layouts.app')

@section('title', 'System Status')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">System Status</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Database</h3>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                <span class="text-gray-600">Connected</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Mail Server</h3>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                <span class="text-gray-600">Active</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">API Status</h3>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                <span class="text-gray-600">Online</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden mt-6">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Component</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Version</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Laravel</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">Running</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ app()->version() }}</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">PHP</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">Running</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ phpversion() }}</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">MySQL</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">Running</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">8.0</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
