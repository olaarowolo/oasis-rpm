@extends('layouts.app')

@section('title', 'Platform Configuration')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Platform Configuration</h1>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">General Settings</h2>
        </div>
        <div class="px-6 py-4">
            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">App Name</label>
                    <input type="text" value="{{ config('app.name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Default University Code</label>
                    <input type="text" value="{{ config('app.default_university_code', 'LASU') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">AI Configuration</h2>
        </div>
        <div class="px-6 py-4">
            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Gemini API Key</label>
                    <input type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
