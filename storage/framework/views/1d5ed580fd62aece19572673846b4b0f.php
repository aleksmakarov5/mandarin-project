<?php $__env->startSection('title'); ?>
  Документы 
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main'); ?> 

 <?php $__currentLoopData = $document; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
 
    <a href="/showdoc/<?php echo e($doc->location); ?>" class="nav-link"><?php echo e($doc->file_name); ?></a>
 
 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 <?php $__env->stopSection(); ?>
<?php echo $__env->make('header_menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/aleksejmakarov/Yandex.Disk-aleksmakarov5.localized/www/mandarin/mandarin/resources/views/document.blade.php ENDPATH**/ ?>