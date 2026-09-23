<?php $__env->startSection('title', $mode === 'edit' ? 'Edit User' : 'Add User'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
    <?php if (isset($component)) { $__componentOriginala82f7a7d3bf6cd10b34d4facad756ea6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala82f7a7d3bf6cd10b34d4facad756ea6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.super-admin.breadcrumbs','data' => ['breadcrumbs' => [
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard'), 'icon' => 'fa-gauge-high'],
        ['label' => 'Users', 'url' => route('super-admin.users'), 'icon' => 'fa-users-gear'],
        ['label' => $mode === 'edit' ? 'Edit User' : 'Add User', 'url' => $mode === 'edit' ? route('super-admin.users.edit', $user) : route('super-admin.users.create'), 'icon' => 'fa-user-pen'],
    ]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('super-admin.breadcrumbs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['breadcrumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
        ['label' => 'Dashboard', 'url' => route('super-admin.dashboard'), 'icon' => 'fa-gauge-high'],
        ['label' => 'Users', 'url' => route('super-admin.users'), 'icon' => 'fa-users-gear'],
        ['label' => $mode === 'edit' ? 'Edit User' : 'Add User', 'url' => $mode === 'edit' ? route('super-admin.users.edit', $user) : route('super-admin.users.create'), 'icon' => 'fa-user-pen'],
    ])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala82f7a7d3bf6cd10b34d4facad756ea6)): ?>
<?php $attributes = $__attributesOriginala82f7a7d3bf6cd10b34d4facad756ea6; ?>
<?php unset($__attributesOriginala82f7a7d3bf6cd10b34d4facad756ea6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala82f7a7d3bf6cd10b34d4facad756ea6)): ?>
<?php $component = $__componentOriginala82f7a7d3bf6cd10b34d4facad756ea6; ?>
<?php unset($__componentOriginala82f7a7d3bf6cd10b34d4facad756ea6); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $currentRole = old('role', $user->role);
        $studentProfile = $user->student;
        $supervisorProfile = $user->supervisor;
    ?>

<div class="space-y-6 max-w-6xl">
    <div class="rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-violet-900 p-6 text-white shadow-2xl lg:p-8">
        <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
            <div class="max-w-3xl">
                <a href="<?php echo e(route('super-admin.users')); ?>" class="inline-flex items-center gap-2 text-sm font-medium text-violet-200 hover:text-white transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                    Back to Users
                </a>
                <h1 class="mt-4 text-3xl font-black tracking-tight sm:text-4xl"><?php echo e($mode === 'edit' ? 'Edit User' : 'Add User'); ?></h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base"><?php echo e($mode === 'edit' ? 'Update identity details, tenant assignment, and any attached academic profile from one operator workflow.' : 'Create the account with name, email, role, and tenant assignment. The user receives an email invitation and completes the remaining details themselves.'); ?></p>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Mode</p>
                    <p class="mt-2 text-lg font-bold"><?php echo e(ucfirst($mode)); ?></p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Role</p>
                    <p class="mt-2 text-lg font-bold"><?php echo e(ucfirst(str_replace('_', ' ', $currentRole ?: 'user'))); ?></p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur-sm col-span-2 sm:col-span-1">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">Status</p>
                    <p class="mt-2 text-lg font-bold"><?php echo e($mode === 'create' ? 'Pending Invite' : (old('is_active', $user->is_active ?? true) ? 'Active' : 'Suspended')); ?></p>
                </div>
            </div>
        </div>
    </div>

    <?php if(session('error')): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/50 dark:bg-rose-900/20 dark:text-rose-300">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <?php if(isset($errors) && $errors->any()): ?>
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/50 dark:bg-rose-900/20 dark:text-rose-300">
            <ul class="list-inside list-disc space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e($mode === 'edit' ? route('super-admin.users.update', $user) : route('super-admin.users.store')); ?>" method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>
        <?php if($mode === 'edit'): ?>
            <?php echo method_field('PUT'); ?>
        <?php endif; ?>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-700">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Identity & Access</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400"><?php echo e($mode === 'create' ? 'Capture the minimum identity data required to send an onboarding invitation.' : 'Set the account name, email, role, tenant, and activation state.'); ?></p>
            </div>
            <div class="space-y-6 px-6 py-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Name</label>
                        <input type="text" name="name" value="<?php echo e(old('name', $user->name)); ?>" required class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Email</label>
                        <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" required class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Role</label>
                        <select id="role" name="role" required class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                            <?php $__currentLoopData = ['super_admin' => 'Super Admin', 'admin' => 'Admin', 'supervisor' => 'Supervisor', 'student' => 'Student']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($value); ?>" <?php echo e(old('role', $user->role) === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">University</label>
                        <select id="university_id" name="university_id" required class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                            <option value="">Select university</option>
                            <?php $__currentLoopData = $universities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $university): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($university->id); ?>" <?php echo e((string) old('university_id', $user->university_id) === (string) $university->id ? 'selected' : ''); ?>><?php echo e($university->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <?php if($mode === 'create'): ?>
                    <div class="rounded-2xl border border-dashed border-violet-200 bg-violet-50/80 px-5 py-4 text-sm text-violet-900 dark:border-violet-900/40 dark:bg-violet-900/20 dark:text-violet-100">
                        The invite email will prompt the user to provide their password, phone, department, and any role-specific profile details before access is activated.
                    </div>

                    <div id="invite-supervisor-card" class="rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 shadow-sm dark:border-slate-700 dark:bg-slate-900/40 <?php echo e($currentRole === 'student' ? '' : 'hidden'); ?>">
                        <div class="flex flex-col gap-4">
                            <div>
                                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Student Supervisor Assignment</h3>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">If you do not require a manual choice, the platform will assign the least-loaded active supervisor in the selected university before the invite is sent.</p>
                            </div>
                            <label class="inline-flex items-center gap-3 text-sm font-medium text-slate-700 dark:text-slate-300">
                                <input type="hidden" name="require_supervisor_selection" value="0">
                                <input type="checkbox" id="require_supervisor_selection" name="require_supervisor_selection" value="1" <?php echo e(old('require_supervisor_selection') ? 'checked' : ''); ?> class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                                Require supervisor selection before sending this student invite
                            </label>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Assigned Supervisor</label>
                                <select id="invite_supervisor_id" name="supervisor_id" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                                    <option value="">Auto-assign least-loaded supervisor</option>
                                    <?php $__currentLoopData = $supervisors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supervisor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($supervisor->id); ?>" data-university-id="<?php echo e($supervisor->university_id); ?>" <?php echo e((string) old('supervisor_id') === (string) $supervisor->id ? 'selected' : ''); ?>>
                                                <?php echo e($supervisor->user->name ?? 'Supervisor'); ?> - <?php echo e($supervisor->department ?: 'Pending supervisor setup'); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Department</label>
                            <input type="text" name="department" value="<?php echo e(old('department', $user->department)); ?>" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Phone</label>
                            <input type="text" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                        </div>
                    </div>

                    <label class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-300">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="is_active" name="is_active" value="1" <?php echo e(old('is_active', $user->is_active ?? true) ? 'checked' : ''); ?> class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                        Active account
                    </label>
                <?php endif; ?>
            </div>
        </div>

        <?php if($mode === 'edit'): ?>
        <div id="supervisor-profile-card" class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden <?php echo e($currentRole === 'supervisor' ? '' : 'hidden'); ?>">
            <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-700">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Supervisor Profile</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Define the supervisor identity, credentials, and booking profile.</p>
            </div>
            <div class="space-y-6 px-6 py-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Title</label>
                        <input type="text" name="supervisor_title" value="<?php echo e(old('supervisor_title', $supervisorProfile->title ?? 'Dr.')); ?>" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Supervisor Department</label>
                        <input type="text" name="supervisor_department" value="<?php echo e(old('supervisor_department', $supervisorProfile->department ?? $user->department)); ?>" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Security PIN</label>
                        <input type="text" name="pin_code" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white" placeholder="<?php echo e($mode === 'edit' ? 'Leave blank to keep current PIN' : 'Enter PIN'); ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Passphrase</label>
                        <input type="text" name="passphrase" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white" placeholder="<?php echo e($mode === 'edit' ? 'Leave blank to keep current passphrase' : 'Enter passphrase'); ?>">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Research Areas</label>
                    <textarea name="research_areas" rows="3" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white"><?php echo e(old('research_areas', $supervisorProfile->research_areas ?? null)); ?></textarea>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Booking URL</label>
                        <input type="url" name="booking_url" value="<?php echo e(old('booking_url', $supervisorProfile->booking_url ?? null)); ?>" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                    </div>
                    <label class="inline-flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 md:mt-7 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-300">
                        <input type="hidden" name="supervisor_is_active" value="0">
                        <input type="checkbox" id="supervisor_is_active" name="supervisor_is_active" value="1" <?php echo e(old('supervisor_is_active', $supervisorProfile->is_active ?? true) ? 'checked' : ''); ?> class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                        Supervisor profile active
                    </label>
                </div>
            </div>
        </div>

        <div id="student-profile-card" class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden <?php echo e($currentRole === 'student' ? '' : 'hidden'); ?>">
            <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-700">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">Student Profile</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Create the academic profile and map the student to a registered supervisor.</p>
            </div>
            <div class="space-y-6 px-6 py-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Matric Number</label>
                        <input type="text" name="matric_number" value="<?php echo e(old('matric_number', $studentProfile->matric_number ?? null)); ?>" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Degree Level</label>
                        <select name="degree_level" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                            <?php $__currentLoopData = ['BSc', 'MSc', 'PhD']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $degree): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($degree); ?>" <?php echo e(old('degree_level', $studentProfile->degree_level ?? 'BSc') === $degree ? 'selected' : ''); ?>><?php echo e($degree); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Last Name</label>
                        <input type="text" name="lastname" value="<?php echo e(old('lastname', $studentProfile->lastname ?? null)); ?>" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Full Name</label>
                        <input type="text" name="full_name" value="<?php echo e(old('full_name', $studentProfile->full_name ?? $user->name)); ?>" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Student Email</label>
                        <input type="email" name="student_email" value="<?php echo e(old('student_email', $studentProfile->email ?? $user->email)); ?>" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Mapped Supervisor</label>
                        <select name="supervisor_id" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                            <option value="">Assign later</option>
                            <?php $__currentLoopData = $supervisors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supervisor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($supervisor->id); ?>" data-university-id="<?php echo e($supervisor->university_id); ?>" <?php echo e((string) old('supervisor_id', $studentProfile->supervisor_id ?? null) === (string) $supervisor->id ? 'selected' : ''); ?>>
                                    <?php echo e($supervisor->user->name ?? 'Supervisor'); ?> - <?php echo e($supervisor->department ?: 'Pending supervisor setup'); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Student Status</label>
                        <select name="student_status" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                            <?php $__currentLoopData = ['active', 'suspended', 'completed', 'graduated']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($status); ?>" <?php echo e(old('student_status', $studentProfile->status ?? 'active') === $status ? 'selected' : ''); ?>><?php echo e(ucfirst($status)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Account Status</label>
                        <select name="student_account_status" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                            <?php $__currentLoopData = ['active', 'archived']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($status); ?>" <?php echo e(old('student_account_status', $studentProfile->account_status ?? 'active') === $status ? 'selected' : ''); ?>><?php echo e(ucfirst($status)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Current Stage</label>
                        <input type="number" min="1" max="12" name="current_stage" value="<?php echo e(old('current_stage', $studentProfile->current_stage ?? 1)); ?>" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Progress %</label>
                        <input type="number" min="0" max="100" name="progress_percentage" value="<?php echo e(old('progress_percentage', $studentProfile->progress_percentage ?? 0)); ?>" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Points Earned</label>
                        <input type="number" min="0" name="points_earned" value="<?php echo e(old('points_earned', $studentProfile->points_earned ?? 0)); ?>" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Research Topic</label>
                        <input type="text" name="research_topic" value="<?php echo e(old('research_topic', $studentProfile->research_topic ?? null)); ?>" class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Student Drive URL</label>
                    <input type="url" name="personal_drive_url" value="<?php echo e(old('personal_drive_url', $studentProfile->personal_drive_url ?? null)); ?>" placeholder="https://drive.google.com/..." class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                </div>
            </div>
        </div>

        <div id="password-reset" class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-700">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white"><?php echo e($mode === 'edit' ? 'Reset Password' : 'Initial Password'); ?></h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400"><?php echo e($mode === 'edit' ? 'Leave password fields blank to keep the current password.' : 'Set a secure password for the new user account.'); ?></p>
            </div>
            <div class="grid grid-cols-1 gap-4 px-6 py-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Password</label>
                    <input type="password" name="password" <?php echo e($mode === 'create' ? 'required' : ''); ?> class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Confirm Password</label>
                    <input type="password" name="password_confirmation" <?php echo e($mode === 'create' ? 'required' : ''); ?> class="mt-1.5 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:border-slate-700 dark:bg-slate-700 dark:text-white">
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-5 py-2.5 font-semibold text-white hover:bg-violet-700 transition-colors">
                <i class="fa-solid fa-save"></i>
                <?php echo e($mode === 'edit' ? 'Save Changes' : 'Send Invite'); ?>

            </button>
            <a href="<?php echo e(route('super-admin.users')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 font-semibold text-slate-700 hover:bg-slate-50 transition-colors dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-700/50">
                <i class="fa-solid fa-xmark"></i>
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
    (function () {
        var roleSelect = document.getElementById('role');
        var universitySelect = document.getElementById('university_id');
        var supervisorSelect = document.querySelector('select[name="supervisor_id"]');
        var inviteSupervisorSelect = document.getElementById('invite_supervisor_id');
        var inviteSupervisorCard = document.getElementById('invite-supervisor-card');
        var requireSupervisorCheckbox = document.getElementById('require_supervisor_selection');
        var supervisorCard = document.getElementById('supervisor-profile-card');
        var studentCard = document.getElementById('student-profile-card');

        function syncRolePanels() {
            if (!roleSelect) return;
            var role = roleSelect.value;
            if (supervisorCard && studentCard) {
                supervisorCard.classList.toggle('hidden', role !== 'supervisor');
                studentCard.classList.toggle('hidden', role !== 'student');
            }

            if (inviteSupervisorCard) {
                inviteSupervisorCard.classList.toggle('hidden', role !== 'student');
            }

            syncInviteSupervisorState();
        }

        function syncSupervisorOptions() {
            if (!universitySelect) return;
            var universityId = universitySelect.value;
            if (supervisorSelect) {
                Array.prototype.forEach.call(supervisorSelect.options, function (option) {
                    if (!option.value) {
                        option.hidden = false;
                        return;
                    }

                    var matches = !universityId || option.dataset.universityId === universityId;
                    option.hidden = !matches;

                    if (!matches && option.selected) {
                        supervisorSelect.value = '';
                    }
                });
            }

            if (inviteSupervisorSelect) {
                Array.prototype.forEach.call(inviteSupervisorSelect.options, function (option) {
                    if (!option.value) {
                        option.hidden = false;
                        return;
                    }

                    var matches = !universityId || option.dataset.universityId === universityId;
                    option.hidden = !matches;

                    if (!matches && option.selected) {
                        inviteSupervisorSelect.value = '';
                    }
                });
            }
        }

        function syncInviteSupervisorState() {
            if (!roleSelect || !inviteSupervisorSelect || !requireSupervisorCheckbox) return;

            var requireSelection = roleSelect.value === 'student' && requireSupervisorCheckbox.checked;
            inviteSupervisorSelect.required = requireSelection;

            if (!requireSelection) {
                inviteSupervisorSelect.value = '';
            }
        }

        if (roleSelect) {
            roleSelect.addEventListener('change', syncRolePanels);
            syncRolePanels();
        }

        if (universitySelect) {
            universitySelect.addEventListener('change', syncSupervisorOptions);
            syncSupervisorOptions();
        }

        if (requireSupervisorCheckbox) {
            requireSupervisorCheckbox.addEventListener('change', syncInviteSupervisorState);
            syncInviteSupervisorState();
        }
    })();
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super-admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/super-admin/user-form.blade.php ENDPATH**/ ?>