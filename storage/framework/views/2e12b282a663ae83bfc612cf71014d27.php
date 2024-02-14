<?php $__env->startSection('title'); ?>
    Запчасти 
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main'); ?> 
<a href="/SparePart/<?php echo e($id); ?>/create" class="nav-link">Добавить</a>
<table class="table table-striped-columns table-hover table-group-divider table-responsive">
  <thead>
  
    <tr>
      <th scope="col">#</th>
      <th scope="col">Наименование</th>
      <th scope="col">Кат. номер</th>
      <th scope="col">Заказанный номер</th>
      <th scope="col">Цена оригинал</th>
      <th scope="col">Цена аналог</th>
      <th scope="col">Цена б/у</th>
      <th scope="col">Дата заказа</th>
      <th scope="col">Статус</th>
    
    
      <th scope="col"></th>

    </tr>
  </thead>
  <tbody>

   
 
<?php $__currentLoopData = $spareparts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sparepart): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<tr >
    
      <th scope="col" >
        
        <?php echo e($sparepart->id); ?>

    
      </th> 
      <th scope="col" onclick="window.location.href='/SparePart/<?php echo e($sparepart->id); ?>/edit'; return false"><?php echo e($sparepart->name); ?></th>
      <th scope="col" onclick="window.location.href='/SparePart/<?php echo e($sparepart->id); ?>/edit'; return false"><?php echo e($sparepart->part_number); ?> </th>
      <th scope="col" onclick="window.location.href='/SparePart/<?php echo e($sparepart->id); ?>/edit'; return false"><?php echo e($sparepart->order_number); ?> </th>
      <th scope="col" onclick="window.location.href='/SparePart/<?php echo e($sparepart->id); ?>/edit'; return false"><?php echo e($sparepart->price_original); ?></th>
      <th scope="col" onclick="window.location.href='/SparePart/<?php echo e($sparepart->id); ?>/edit'; return false"><?php echo e($sparepart->price_analogue); ?></th>
      <th scope="col" onclick="window.location.href='/SparePart/<?php echo e($sparepart->id); ?>/edit'; return false"><?php echo e($sparepart->price_used); ?></th>
      <th scope="col" onclick="window.location.href='/SparePart/<?php echo e($sparepart->id); ?>/edit'; return false"><?php echo e($sparepart->status); ?></th>
      <th scope="col" onclick="window.location.href='/SparePart/<?php echo e($sparepart->id); ?>/edit'; return false"><?php echo e($sparepart->date_price); ?></th>
         <th scope="col"> <a href="<?php echo e(route('SparePart.destroy', $sparepart->id)); ?>" class="nav-link">Удалить</a>
        </th>
    </tr>
      
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 </tbody>
</table>

 <?php $__env->stopSection(); ?> 
<?php echo $__env->make('header_menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/aleksejmakarov/Yandex.Disk-aleksmakarov5.localized/www/mandarin/mandarin/resources/views/SparePart/index.blade.php ENDPATH**/ ?>