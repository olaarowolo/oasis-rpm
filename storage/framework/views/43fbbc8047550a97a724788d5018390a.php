<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Ready</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen bg-slate-950 font-sans text-white">
    <div class="mx-auto flex min-h-screen max-w-3xl items-center justify-center px-6 py-12">
        <div class="w-full rounded-[2rem] border border-white/10 bg-white/10 p-10 text-center shadow-2xl backdrop-blur">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-300">
                <span class="text-3xl">✓</span>
            </div>
            <h1 class="mt-6 text-3xl font-black tracking-tight">Account setup complete</h1>
            <p class="mt-4 text-base leading-7 text-slate-300">
                <?php echo e($user->name); ?>, your <?php echo e($roleLabel); ?> account is now active. Return to the login page and sign in with the credentials for your role.
            </p>
            <a href="<?php echo e(route('login')); ?>" class="mt-8 inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-200">
                Go to login
            </a>
        </div>
    </div>
</body>
</html><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/auth/complete-invitation-success.blade.php ENDPATH**/ ?>