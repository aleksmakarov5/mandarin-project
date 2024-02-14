<?php $__env->startSection('title'); ?>
    Отзывы
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main_content'); ?>   
<h1>Форма добавления отзыва</h1> 
<?php if($errors->any()): ?>



<div class="alert alert-danger">
    <ul>
    
       <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
           <li><?php echo e($item); ?></li>
       <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
       
    </ul>
</div>
<?php endif; ?>
<form action="/review/check" method="POST">
    <?php echo csrf_field(); ?>
<input type="email" name="email" id="email" placeholder="Введите email" class="form-control"><br>
<input type="text" name="subject" id="subject" placeholder="Введите отзыв" class="form-control"><br>
<textarea name="message" id="message" cols="30" rows="10" class="form-control"></textarea><br>
<button type="submit" class="btn btn-success">Отправить</button>
</form>
<h1>Все Отзывы</h1>
<?php $__currentLoopData = $reviews->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="alert alert-warning">
    <h3>
        <?php echo e($element->subject); ?>

        </h3>
    <b><?php echo e($element->email); ?></b>
    <p>
        <?php echo e($element->message); ?>

    </p>
    </div>    
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/aleksejmakarov/Yandex.Disk-aleksmakarov5.localized/www/mandarin/mandarin/resources/views/review.blade.php ENDPATH**/ ?>