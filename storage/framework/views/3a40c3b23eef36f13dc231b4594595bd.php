<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Account Setup</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo e(asset('vendor/fontawesome/css/all.min.css')); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        academic: {
                            700: '#035388',
                            900: '#002744',
                        },
                    },
                },
            },
        };
    </script>
</head>
<body class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(14,116,144,0.18),_transparent_35%),linear-gradient(180deg,_#f8fafc_0%,_#e2e8f0_100%)] font-sans text-slate-900">
    <div class="mx-auto flex min-h-screen w-full max-w-6xl items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid w-full overflow-hidden rounded-[2rem] border border-white/70 bg-white/90 shadow-2xl backdrop-blur xl:grid-cols-[0.95fr_1.05fr]">
            <section class="bg-slate-950 px-8 py-10 text-white sm:px-10 lg:px-12">
                <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-cyan-100">
                    Account Invitation
                </div>
                <h1 class="mt-6 text-3xl font-black tracking-tight sm:text-4xl">Finish your <?php echo e($roleLabel); ?> setup.</h1>
                <p class="mt-4 max-w-xl text-sm leading-7 text-slate-300 sm:text-base">
                    Your account has been created with your email and role assignment. Complete the remaining details below to activate access for <?php echo e($user->university->name ?? 'your university'); ?>.
                </p>

                <div class="mt-8 space-y-4">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Email</p>
                        <p class="mt-2 text-lg font-semibold"><?php echo e($user->email); ?></p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Invitation Expires</p>
                        <p class="mt-2 text-lg font-semibold"><?php echo e($expiresAt->toDayDateTimeString()); ?></p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">What Happens Next</p>
                        <p class="mt-2 text-sm leading-6 text-slate-300">Once submitted, your account is activated and ready for sign-in with the credentials required for your role.</p>
                    </div>
                </div>
            </section>

            <section class="px-8 py-10 sm:px-10 lg:px-12">
                <?php if($errors->any()): ?>
                    <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        <ul class="list-inside list-disc space-y-1">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('account-invitations.complete', $token)); ?>" class="space-y-6">
                    <?php echo csrf_field(); ?>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-slate-700">Full name</label>
                            <input type="text" name="name" value="<?php echo e(old('name', $user->name)); ?>" required class="mt-1.5 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-200">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-slate-700">Email</label>
                            <input type="email" value="<?php echo e($user->email); ?>" readonly class="mt-1.5 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500 shadow-sm">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-slate-700">Phone</label>
                            <input type="text" name="phone" value="<?php echo e(old('phone', $user->phone)); ?>" class="mt-1.5 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-200">
                        </div>

                        <?php if(in_array($user->role, ['admin', 'super_admin'], true)): ?>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-slate-700">Department</label>
                                <input type="text" name="department" value="<?php echo e(old('department', $user->department)); ?>" class="mt-1.5 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-200">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Password</label>
                                <input type="password" name="password" required class="mt-1.5 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-200">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Confirm password</label>
                                <input type="password" name="password_confirmation" required class="mt-1.5 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-200">
                            </div>
                        <?php endif; ?>

                        <?php if($user->role === 'supervisor'): ?>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Title</label>
                                <input type="text" name="supervisor_title" value="<?php echo e(old('supervisor_title')); ?>" required class="mt-1.5 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-200">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Department</label>
                                <input type="text" name="department" value="<?php echo e(old('department', $user->department)); ?>" required class="mt-1.5 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-200">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">PIN</label>
                                <input type="text" name="pin_code" value="<?php echo e(old('pin_code')); ?>" required class="mt-1.5 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-200">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Passphrase</label>
                                <input type="text" name="passphrase" value="<?php echo e(old('passphrase')); ?>" required class="mt-1.5 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-200">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-slate-700">Research areas</label>
                                <textarea name="research_areas" rows="3" class="mt-1.5 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-200"><?php echo e(old('research_areas')); ?></textarea>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-slate-700">Booking URL</label>
                                <input type="url" name="booking_url" value="<?php echo e(old('booking_url')); ?>" class="mt-1.5 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-200">
                            </div>
                        <?php endif; ?>

                        <?php if($user->role === 'student'): ?>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Last name</label>
                                <input type="text" name="lastname" value="<?php echo e(old('lastname')); ?>" required class="mt-1.5 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-200">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Matric number</label>
                                <input type="text" name="matric_number" value="<?php echo e(old('matric_number')); ?>" required class="mt-1.5 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-200">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Degree level</label>
                                <select name="degree_level" required class="mt-1.5 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-200">
                                    <?php $__currentLoopData = ['BSc', 'MSc', 'PhD']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $degree): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($degree); ?>" <?php echo e(old('degree_level') === $degree ? 'selected' : ''); ?>><?php echo e($degree); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-slate-700">Research topic</label>
                                <input type="text" name="research_topic" value="<?php echo e(old('research_topic')); ?>" class="mt-1.5 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-200">
                            </div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-academic-700 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-cyan-900/10 transition hover:bg-academic-900">
                        Activate account
                    </button>
                </form>
            </section>
        </div>
    </div>
</body>
</html><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/auth/complete-invitation.blade.php ENDPATH**/ ?>