@extends('layouts.app')

@section('title', 'Super Admin Portal')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Super Admin Portal</h1>
    
    <div class="bg-gradient-to-r from-slate-900 via-academic-900 to-academic-800 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold">Welcome, Super Admin</h2>
                <p class="text-slate-300 mt-1">Platform-level administration across all universities</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full bg-purple-500 text-white text-xs font-bold">Super Admin Portal</span>
                <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check"></i> Online
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-8">
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider">Total Universities</p>
                    <p class="mt-2 text-3xl font-bold text-academic-900 dark:text-white">{{ \App\Models\University::count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-academic-100 dark:bg-academic-900/30 text-academic-600 dark:text-academic-400 flex items-center justify-center">
                    <i class="fa-solid fa-building-columns text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider">Active Users</p>
                    <p class="mt-2 text-3xl font-bold text-emerald-900 dark:text-emerald-400">{{ \App\Models\User::where('role', '!=', 'student')->count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <i class="fa-solid fa-users text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider">Supervisors</p>
                    <p class="mt-2 text-3xl font-bold text-amber-900 dark:text-amber-400">{{ \App\Models\Supervisor::count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <i class="fa-solid fa-user-shield text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider">Students</p>
                    <p class="mt-2 text-3xl font-bold text-blue-900 dark:text-blue-400">{{ \App\Models\Student::count() }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <i class="fa-solid fa-user-graduate text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="bg-slate-50 dark:bg-slate-700/50 p-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-building-columns text-academic-600 dark:text-academic-400"></i>
                    University Management
                </h3>
            </div>
            <div class="p-4 space-y-3">
                <a href="{{ route('super-admin.universities') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition text-left">
                    <i class="fa-solid fa-building w-8 h-8 rounded-lg bg-academic-100 dark:bg-academic-900/30 text-academic-600 dark:text-academic-400 flex items-center justify-center text-sm"></i>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">Manage Universities</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Add, edit, or configure university tenants</p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-slate-400"></i>
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="bg-slate-50 dark:bg-slate-700/50 p-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-users-gear text-purple-600 dark:text-purple-400"></i>
                    User Access &amp; Roles
                </h3>
            </div>
            <div class="p-4 space-y-3">
                <a href="{{ route('super-admin.users') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition text-left">
                    <i class="fa-solid fa-user-gear w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-sm"></i>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">Manage Users</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Create users and assign roles</p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-slate-400"></i>
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="bg-slate-50 dark:bg-slate-700/50 p-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-server text-emerald-600 dark:text-emerald-400"></i>
                    System Status
                </h3>
            </div>
            <div class="p-4 space-y-3">
                <a href="{{ route('super-admin.system-status') }}" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition text-left">
                    <i class="fa-solid fa-circle-check w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm"></i>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">System Health</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Monitor platform-wide system status</p>
                    </div>
                    <i class="fa-solid fa-chevron-right text-slate-400"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
