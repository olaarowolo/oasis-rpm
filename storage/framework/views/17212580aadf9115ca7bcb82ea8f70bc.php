<?php $__env->startSection('title', 'My Proposals'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800">My Proposals</h1>
        <a href="<?php echo e(route('student.dashboard')); ?>" class="text-sm text-blue-600 hover:text-blue-900">&larr; Back to Dashboard</a>
    </div>

    <div class="mb-6">
        <button onclick="window.location.href='<?php echo e(route('student.dashboard')); ?>'" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
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
                <?php $__empty_1 = true; $__currentLoopData = $proposals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proposal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo e($proposal->title); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e(optional($proposal->date_submitted)->format('Y-m-d')); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($proposal->location); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            <?php echo e($proposal->status === 'approved' ? 'bg-green-100 text-green-800' :
                               ($proposal->status === 'revision_required' ? 'bg-yellow-100 text-yellow-800' :
                               ($proposal->status === 'pending' ? 'bg-blue-100 text-blue-800' :
                               'bg-gray-100 text-gray-800'))); ?>">
                            <?php echo e(ucfirst(str_replace('_', ' ', $proposal->status))); ?>

                        </span>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-400">No proposals yet.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/student/proposals.blade.php ENDPATH**/ ?>