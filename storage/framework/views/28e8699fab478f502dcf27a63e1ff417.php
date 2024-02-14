<?php $__env->startSection('title'); ?>
    Заявки
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main_content'); ?>   
<h1>Все заявки</h1> 
<a href="/applications/create" class="nav-link">Добавить заявку</a>
<table class="table table-striped-columns table-hover table-group-divider table-responsive">
  <thead>
  
    <tr>
      <th scope="col">#</th>
      <th scope="col">Дата заявки</th>
      <th scope="col">Страховая компания</th>
      <th scope="col">Номер заявки </th>
      <th scope="col">Марка машины </th>
      <th scope="col">ГНЗ </th>
      <th scope="col">VIN</th>
      <th scope="col">ФИО клиента</th>
      <th scope="col">Телефон клиента</th>
      <th scope="col">Остаток времени, час</th>
      <th scope="col">Статус</th>
      <th scope="col"></th>

    </tr>
  </thead>
  <tbody>

   
 
<?php $__currentLoopData = $applications->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $application): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<tr >
    
      <th scope="col" >
        
        <?php echo e($application->id); ?>

    
      </th> 
      <th scope="col" onclick="window.location.href='/application/<?php echo e($application->id); ?>'; return false"><?php echo e(date("d-m-Y h:m",strtotime($application->created_at))); ?></th>
      <th scope="col" onclick="window.location.href='/application/<?php echo e($application->id); ?>'; return false"><?php echo e($application->name); ?> </th>
      <th scope="col" onclick="window.location.href='/application/<?php echo e($application->id); ?>'; return false"><?php echo e($application->numberInsuranceCompanyApplication); ?> </th>
      <th scope="col" onclick="window.location.href='/application/<?php echo e($application->id); ?>'; return false"><?php echo e($application->CarModel); ?> </th>
      <th scope="col" onclick="window.location.href='/application/<?php echo e($application->id); ?>'; return false"><?php echo e($application->gnz); ?></th>
      <th scope="col" onclick="window.location.href='/application/<?php echo e($application->id); ?>'; return false"><?php echo e($application->vin); ?></th>
      <th scope="col" onclick="window.location.href='/application/<?php echo e($application->id); ?>'; return false"><?php echo e($application->ClientName); ?></th>
      <th scope="col" onclick="window.location.href='/application/<?php echo e($application->id); ?>'; return false"><?php echo e($application->ClientPhone); ?></th>
    <?php if(((48-round((strtotime(date("Y-m-d h:m:s"))-strtotime($application->created_at))/3600)))>0): ?>
           <th scope="col"  onclick="window.location.href='/application/<?php echo e($application->id); ?>'; return false">
             <?php echo e(48-round((strtotime(date("Y-m-d h:m:s"))-strtotime($application->created_at))/3600)); ?>

             </th>
            
                 
             <?php else: ?>
                <th scope="col" class="alert alert-danger"  onclick="window.location.href='/application/<?php echo e($application->id); ?>'; return false">
                  Просрочено на 
             <?php echo e((48-round((strtotime(date("Y-m-d h:m:s"))-strtotime($application->created_at))/3600))*(-1)); ?>

             </th>
             
      <?php endif; ?>
        <th scope="col" onclick="window.location.href='/application/<?php echo e($application->id); ?>'; return false">-</th>
        <th scope="col">
          <a href="<?php echo e(route('applications.destroy', $application->id)); ?>" class="nav-link">Удалить</a>
        </th>
    </tr>
      
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 </tbody>
</table>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/aleksejmakarov/Yandex.Disk-aleksmakarov5.localized/www/mandarin/mandarin/resources/views/applications.blade.php ENDPATH**/ ?>