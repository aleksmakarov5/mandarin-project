<?php $__env->startSection('title'); ?>
    Заявкa 
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main'); ?>   
<a href="/application/<?php echo e($application->id); ?>/edit" class="nav-link">Изменить</a>
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

    </tr>
  </thead>
  <tbody>

   
 


   
<tr >
    
      <th scope="col" >
        
       -
    
      </th> 
      <th scope="col"><?php echo e(date("d-m-Y ",strtotime($application->created_at))); ?></th>
      <th scope="col"><?php echo e($application->name); ?> </th>
      <th scope="col"><?php echo e($application->numberInsuranceCompanyApplication); ?> </th>
      <th scope="col"><?php echo e($application->CarModel); ?> </th>
      <th scope="col"><?php echo e($application->gnz); ?></th>
      <th scope="col"><?php echo e($application->vin); ?></th>
      <th scope="col"><?php echo e($application->ClientName); ?></th>
      <th scope="col"><?php echo e($application->ClientPhone); ?></th>
       <th scope="col"><?php echo e(48-round((strtotime(date("Y-m-d h:m:s"))-strtotime($application->created_at))/3600)); ?> </th>
        <th scope="col">-</th>
  часов 
    </tr>
      

 </tbody>
</table>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('header_menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/aleksejmakarov/Yandex.Disk-aleksmakarov5.localized/www/mandarin/mandarin/resources/views/application.blade.php ENDPATH**/ ?>