<?php $__env->startSection('title', 'Audit Logs'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Audit Logs</h1>
        <a href="<?php echo e(route('admin.audit-logs.export', request()->only(['action_type', 'start_date', 'end_date', 'university_id']))); ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded inline-block">
            Export Logs
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <form action="<?php echo e(route('admin.audit-logs.index')); ?>" method="GET" class="flex space-x-4">
                <select name="action_type" class="block w-48 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Actions</option>
                    <option value="created" <?php echo e(request('action_type') === 'created' ? 'selected' : ''); ?>>Created</option>
                    <option value="updated" <?php echo e(request('action_type') === 'updated' ? 'selected' : ''); ?>>Updated</option>
                    <option value="deleted" <?php echo e(request('action_type') === 'deleted' ? 'selected' : ''); ?>>Deleted</option>
                    <option value="viewed" <?php echo e(request('action_type') === 'viewed' ? 'selected' : ''); ?>>Viewed</option>
                    <option value="exported" <?php echo e(request('action_type') === 'exported' ? 'selected' : ''); ?>>Exported</option>
                </select>
                <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>" class="block w-48 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <input type="date" name="end_date" value="<?php echo e(request('end_date')); ?>" class="block w-48 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">Filter</button>
            </form>
        </div>

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Changes</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $auditLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e(optional($log->created_at)->format('Y-m-d H:i:s')); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo e($log->user->name ?? 'Unknown'); ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            <?php echo e($log->action === 'created' ? 'bg-green-100 text-green-800' :
                               ($log->action === 'updated' ? 'bg-blue-100 text-blue-800' :
                               ($log->action === 'deleted' ? 'bg-red-100 text-red-800' :
                               'bg-gray-100 text-gray-800'))); ?>">
                            <?php echo e(ucfirst($log->action)); ?>

                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($log->model_type); ?> #<?php echo e($log->model_id); ?></td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        <?php ($changedKeys = array_keys((array) ($log->new_values ?? $log->old_values ?? []))); ?>
                        <?php echo e(count($changedKeys) ? implode(', ', array_slice($changedKeys, 0, 5)) : '—'); ?>

                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-400">No audit log entries match the current filters.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <?php echo e($auditLogs->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/admin/audit-logs.blade.php ENDPATH**/ ?>