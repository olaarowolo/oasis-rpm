<?php $__env->startSection('title', 'Meeting Logs'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Meeting Logs</h1>

    <?php if(session('status')): ?>
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            <?php echo e(session('status')); ?>

        </div>
    <?php endif; ?>

    <div class="mb-6">
        <button onclick="window.location.href='<?php echo e(route('supervisor.meetings.create')); ?>'" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            Log New Meeting
        </button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meeting #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $meetings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $meeting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e(optional($meeting->meeting_date)->format('Y-m-d')); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($meeting->student->full_name ?? 'Unknown'); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#<?php echo e($meeting->meeting_number); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e(ucfirst(str_replace('_', ' ', $meeting->meeting_mode))); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            <?php echo e($meeting->status === 'approved' ? 'bg-green-100 text-green-800' :
                               ($meeting->status === 'draft' ? 'bg-yellow-100 text-yellow-800' :
                               'bg-gray-100 text-gray-800')); ?>">
                            <?php echo e(ucfirst(str_replace('_', ' ', $meeting->status))); ?>

                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="<?php echo e(route('supervisor.meetings.view', $meeting->id)); ?>" class="text-blue-600 hover:text-blue-900">View</a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-400">No meeting logs yet. Click "Log New Meeting" to add one.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/supervisor/meetings.blade.php ENDPATH**/ ?>