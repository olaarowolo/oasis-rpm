<?php $__env->startSection('title', 'Defense Readiness'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Defense Readiness Checklist</h1>

    <div class="mb-6">
        <div class="bg-purple-50 rounded-lg p-6 inline-block">
            <div class="text-2xl font-bold text-purple-600"><?php echo e($defenseScore); ?>%</div>
            <div class="text-gray-600">Defense Readiness Score</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requirement</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Topic Approval</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if($hasApprovedTopic): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                        <?php else: ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Pending</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php if($hasApprovedTopic): ?> Research topic has been approved by supervisor <?php else: ?> No approved topic yet <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Progress Completion</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if($progressPercentage >= 80): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Ready</span>
                        <?php elseif($progressPercentage >= 50): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">In Progress</span>
                        <?php else: ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Not Ready</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($progressPercentage); ?>% complete</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Final Document</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if($hasFinalDocument): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Uploaded</span>
                        <?php else: ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Pending</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php if($hasFinalDocument): ?> Final manuscript uploaded <?php else: ?> Upload your final document <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Meeting Logs</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if($completedMeetings >= $requiredMeetings): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                        <?php else: ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($completedMeetings); ?> / <?php echo e($requiredMeetings); ?> required</td>
                </tr>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Resource Completion</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if($completedResources >= $totalResources * 0.8): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                        <?php else: ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">In Progress</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($completedResources); ?> / <?php echo e($totalResources); ?> resources</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mt-6 flex items-center gap-4">
        <button onclick="window.location.href='<?php echo e(route('student.dashboard')); ?>'"
            class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
            <?php if($defenseScore < 70): ?> disabled <?php endif; ?>>
            Submit for Defense
        </button>
        <a href="<?php echo e(route('student.dashboard')); ?>" class="text-sm text-blue-600 hover:text-blue-900">&larr; Back to Dashboard</a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/student/defense-readiness.blade.php ENDPATH**/ ?>