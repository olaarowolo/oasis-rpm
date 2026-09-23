<?php $__env->startSection('title', 'Meeting ' . $meeting->log_id); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Meeting #<?php echo e($meeting->meeting_number); ?></h1>
        <a href="<?php echo e(route('supervisor.meetings')); ?>" class="text-sm text-blue-600 hover:text-blue-900">&larr; Back to Meeting Logs</a>
    </div>

    <?php if(session('status')): ?>
        <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            <?php echo e(session('status')); ?>

        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Log ID</p>
                <p class="font-mono text-sm text-gray-800"><?php echo e($meeting->log_id); ?></p>
            </div>
            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                <?php echo e($meeting->status === 'approved' ? 'bg-green-100 text-green-800' :
                   ($meeting->status === 'draft' ? 'bg-yellow-100 text-yellow-800' :
                   ($meeting->status === 'submitted' ? 'bg-blue-100 text-blue-800' :
                   'bg-gray-100 text-gray-800'))); ?>">
                <?php echo e(ucfirst(str_replace('_', ' ', $meeting->status))); ?>

            </span>
        </div>

        <dl class="divide-y divide-gray-100">
            <?php
                $rows = [
                    'Student' => $meeting->student->full_name ?? 'Unknown',
                    'Date' => optional($meeting->meeting_date)->format('Y-m-d'),
                    'Mode' => ucfirst(str_replace('_', ' ', $meeting->meeting_mode)),
                    'Duration' => $meeting->duration . ' min',
                    'Previous actions' => $meeting->previous_actions,
                    'Progress since' => $meeting->progress_since,
                    'Discussion points' => $meeting->discussion_points,
                    'Work reviewed' => $meeting->work_reviewed,
                    'Chapter focus' => $meeting->chapter_focus,
                    'Risks' => $meeting->risks ?: '—',
                    'Support required' => $meeting->support_required ?: '—',
                    'Next meeting date' => optional($meeting->next_meeting_date)->format('Y-m-d'),
                    'Next meeting focus' => $meeting->next_meeting_focus,
                    'Feedback' => $meeting->feedback ?: '—',
                ];
            ?>
            <?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="px-6 py-3 grid grid-cols-1 md:grid-cols-3 gap-2">
                    <dt class="text-sm font-medium text-gray-500"><?php echo e($label); ?></dt>
                    <dd class="text-sm text-gray-800 md:col-span-2 whitespace-pre-line"><?php echo e($value); ?></dd>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </dl>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/supervisor/meeting-detail.blade.php ENDPATH**/ ?>