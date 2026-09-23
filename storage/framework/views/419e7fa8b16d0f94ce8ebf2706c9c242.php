<?php $__env->startSection('title', $mode === 'edit' ? 'Edit Resource' : 'Add Resource'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <?php echo e($mode === 'edit' ? 'Edit Resource' : 'Add Resource'); ?>

        </h1>
        <a href="<?php echo e(route('admin.resources', ['university_id' => $selectedUniversityId])); ?>" class="text-sm text-blue-600 hover:text-blue-900">&larr; Back to Resources</a>
    </div>

    <?php if($errors->any()): ?>
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form
        action="<?php echo e($mode === 'edit' ? route('admin.resources.update', $resource->id) : route('admin.resources.store')); ?>"
        method="POST"
        class="bg-white rounded-lg shadow p-6 space-y-5">
        <?php echo csrf_field(); ?>
        <?php if($mode === 'edit'): ?>
            <?php echo method_field('PUT'); ?>
        <?php endif; ?>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">University</label>
            <select name="university_id" required
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Select university</option>
                <?php $__currentLoopData = $universities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $university): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($university->id); ?>"
                        <?php echo e((string) old('university_id', $resource->university_id ?? $selectedUniversityId) === (string) $university->id ? 'selected' : ''); ?>>
                        <?php echo e($university->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input type="text" name="title" value="<?php echo e(old('title', $resource->title)); ?>" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                <input type="text" name="section" value="<?php echo e(old('section', $resource->section)); ?>" required
                    placeholder="e.g. Research Methods"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">URL</label>
            <input type="url" name="url" value="<?php echo e(old('url', $resource->url)); ?>" required
                placeholder="https://..."
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="4" required
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo e(old('description', $resource->description)); ?></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <?php $__currentLoopData = ['video', 'document', 'link', 'quiz', 'assignment']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($type); ?>" <?php echo e(old('type', $resource->type) === $type ? 'selected' : ''); ?>>
                            <?php echo e(ucfirst($type)); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stage</label>
                <input type="number" name="stage" min="1" max="12" value="<?php echo e(old('stage', $resource->stage ?? 1)); ?>" required
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Points</label>
                <input type="number" name="points" min="0" value="<?php echo e(old('points', $resource->points ?? 0)); ?>"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sort order</label>
                <input type="number" name="sort_order" min="0" value="<?php echo e(old('sort_order', $resource->sort_order ?? 0)); ?>"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_mandatory" value="1" id="is_mandatory"
                <?php echo e(old('is_mandatory', $resource->is_mandatory) ? 'checked' : ''); ?>

                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <label for="is_mandatory" class="text-sm font-medium text-gray-700">Mandatory resource</label>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded font-semibold">
                <?php echo e($mode === 'edit' ? 'Save changes' : 'Create resource'); ?>

            </button>
            <a href="<?php echo e(route('admin.resources', ['university_id' => $selectedUniversityId])); ?>"
                class="px-5 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold">Cancel</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/admin/resource-form.blade.php ENDPATH**/ ?>