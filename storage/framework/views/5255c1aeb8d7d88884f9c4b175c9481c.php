<?php $__env->startSection('main_content'); ?>
   <a href="/application/<?php echo e($application->id); ?>" class="nav-link">Отмена</a>
<form action="/applications/<?php echo e($application->id); ?>/update" method="POST">
     <?php echo csrf_field(); ?>

<?php echo $__env->make('application.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
   <input type="submit" value="Обновить"    class="nav-link">

</form>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/aleksejmakarov/Yandex.Disk-aleksmakarov5.localized/www/mandarin/mandarin/resources/views/application/edit.blade.php ENDPATH**/ ?>