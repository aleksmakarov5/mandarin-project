<?php $__env->startSection('title'); ?>
  Работы 
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main'); ?> 
<a href="/Work/<?php echo e($id); ?>/create" class="nav-link">Добавить</a>
<table class="table table-striped-columns table-hover table-group-divider table-responsive">
  <thead>
  
    <tr>
      <th scope="col">#</th>
      <th scope="col">Наименование</th>
      <th scope="col">Цена</th>
      <th scope="col">Количество нормочасов</th>
      <th scope="col">Сумма</th>
      <th scope="col"></th>

    </tr>
  </thead>
  <tbody>

   
 
<?php $__currentLoopData = $Works; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $Work): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<tr >
    
      <th scope="col" >
        
        <?php echo e($Work->id); ?>

    
      </th> 
      <th scope="col" onclick="window.location.href='/Work/<?php echo e($Work->id); ?>/edit'; return false"><?php echo e($Work->name); ?></th>
      <th scope="col" onclick="window.location.href='/Work/<?php echo e($Work->id); ?>/edit'; return false"><?php echo e($Work->price); ?> </th>
      <th scope="col" onclick="window.location.href='/Work/<?php echo e($Work->id); ?>/edit'; return false"><?php echo e($Work->count_hours); ?> </th>
      <th scope="col" onclick="window.location.href='/Work/<?php echo e($Work->id); ?>/edit'; return false"><?php echo e($Work->summ); ?></th>
         <th scope="col"> <a href="<?php echo e(route('Work.destroy', $Work->id)); ?>" class="nav-link">Удалить</a>
        </th>
    </tr>
      
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 </tbody>
</table>

 <?php $__env->stopSection(); ?> 
<?php echo $__env->make('header_menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/aleksejmakarov/Yandex.Disk-aleksmakarov5.localized/www/mandarin/mandarin/resources/views/Work/index.blade.php ENDPATH**/ ?>