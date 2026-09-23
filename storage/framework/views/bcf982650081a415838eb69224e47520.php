<?php $__env->startSection('title', 'Manage Resources'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Manage Resources</h1>
        <button onclick="window.location.href='<?php echo e(route('admin.resources.create', ['university_id' => $selectedUniversityId ?? null])); ?>'" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            Add Resource
        </button>
    </div>

    <?php if(session('status')): ?>
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            <?php echo e(session('status')); ?>

        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stage</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Points</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mandatory</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $resources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $resource): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo e($resource->title); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            <?php echo e($resource->type === 'video' ? 'bg-red-100 text-red-800' :
                               ($resource->type === 'document' ? 'bg-gray-100 text-gray-800' :
                               ($resource->type === 'link' ? 'bg-blue-100 text-blue-800' :
                               'bg-purple-100 text-purple-800'))); ?>">
                            <?php echo e(ucfirst($resource->type)); ?>

                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Stage <?php echo e($resource->stage); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($resource->points); ?> pts</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?php if($resource->is_mandatory): ?>
                            <span class="text-green-600 font-semibold">Yes</span>
                        <?php else: ?>
                            <span class="text-gray-400">No</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="<?php echo e(route('admin.resources.edit', $resource->id)); ?>" class="text-blue-600 hover:text-blue-900">Edit</a>
                        <form class="inline-block" action="<?php echo e(route('admin.resources.destroy', $resource->id)); ?>" method="POST" onsubmit="return confirm('Delete this resource? This cannot be undone.');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="text-red-600 hover:text-red-900 ml-2">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-400">No resources yet. Click "Add Resource" to create one.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/admin/resources.blade.php ENDPATH**/ ?>