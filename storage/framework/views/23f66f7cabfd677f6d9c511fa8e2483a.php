<?php
    $pageTitle = trim($__env->yieldContent('title')) ?: 'Super Admin Portal';
    $breadcrumbs = $__env->yieldContent('breadcrumbs') ?: [];
?>

<?php if (isset($component)) { $__componentOriginalcacc8df4b3d1c36ecacad2523621359b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcacc8df4b3d1c36ecacad2523621359b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.super-admin','data' => ['title' => $pageTitle,'breadcrumbs' => $breadcrumbs]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('layouts.super-admin'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pageTitle),'breadcrumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($breadcrumbs)]); ?>
    <?php echo $__env->yieldContent('content'); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcacc8df4b3d1c36ecacad2523621359b)): ?>
<?php $attributes = $__attributesOriginalcacc8df4b3d1c36ecacad2523621359b; ?>
<?php unset($__attributesOriginalcacc8df4b3d1c36ecacad2523621359b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcacc8df4b3d1c36ecacad2523621359b)): ?>
<?php $component = $__componentOriginalcacc8df4b3d1c36ecacad2523621359b; ?>
<?php unset($__componentOriginalcacc8df4b3d1c36ecacad2523621359b); ?>
<?php endif; ?><?php /**PATH /Users/olasunkanmiarowolo/Documents/OAsis-RS/Archive/oasis-rpm/resources/views/layouts/super-admin.blade.php ENDPATH**/ ?>