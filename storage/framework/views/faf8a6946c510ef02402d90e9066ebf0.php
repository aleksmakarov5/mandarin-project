<?php $__env->startSection('title'); ?>
    Gbcmvf
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main_content'); ?>   
<h1>Все письма</h1> 

<?php $__currentLoopData = $messages->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="alert alert-warning">
    <h3>
        <?php echo e($message->subject); ?>

        </h3>
    <p>
        <?php
        echo htmlspecialchars_decode($message->body_html);
        ?>
    </p>
    </div>    
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/aleksejmakarov/Yandex.Disk-aleksmakarov5.localized/www/mandarin/mandarin/resources/views/message.blade.php ENDPATH**/ ?>