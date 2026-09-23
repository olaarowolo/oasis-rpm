@extends('layouts.app')

@section('title', $mode === 'edit' ? 'Edit Resource' : 'Add Resource')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            {{ $mode === 'edit' ? 'Edit Resource' : 'Add Resource' }}
        </h1>
        <a href="{{ route('admin.resources', ['university_id' => $selectedUniversityId]) }}" class="text-sm text-blue-600 hover:text-blue-900">&larr; Back to Resources</a>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ $mode === 'edit' ? route('admin.resources.update', $resource->id) : route('admin.resources.store') }}"
        method="POST"
        class="bg-white rounded-lg shadow p-6 space-y-5">
        @csrf
        @if ($mode === 'edit')
            @method('PUT')
        @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">University</label>
            <select name="university_id" required
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Select university</option>
                @foreach ($universities as $university)
                    <option value="{{ $university->id }}"
                        {{ (string) old('university_id', $resource->university_id ?? $selectedUniversityId) === (string) $university->id ? 'selected' : '' }}>
                        {{ $university->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input type="text" name="title" value="{{ old('title', $resource->title) }}" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                <input type="text" name="section" value="{{ old('section', $resource->section) }}" required
                    placeholder="e.g. Research Methods"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">URL</label>
            <input type="url" name="url" value="{{ old('url', $resource->url) }}" required
                placeholder="https://..."
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="4" required
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $resource->description) }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach (['video', 'document', 'link', 'quiz', 'assignment'] as $type)
                        <option value="{{ $type }}" {{ old('type', $resource->type) === $type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stage</label>
                <input type="number" name="stage" min="1" max="12" value="{{ old('stage', $resource->stage ?? 1) }}" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Points</label>
                <input type="number" name="points" min="0" value="{{ old('points', $resource->points ?? 0) }}"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sort order</label>
                <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $resource->sort_order ?? 0) }}"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_mandatory" value="1" id="is_mandatory"
                {{ old('is_mandatory', $resource->is_mandatory) ? 'checked' : '' }}
                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <label for="is_mandatory" class="text-sm font-medium text-gray-700">Mandatory resource</label>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded font-semibold">
                {{ $mode === 'edit' ? 'Save changes' : 'Create resource' }}
            </button>
            <a href="{{ route('admin.resources', ['university_id' => $selectedUniversityId]) }}"
                class="px-5 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold">Cancel</a>
        </div>
    </form>
</div>
@endsection
