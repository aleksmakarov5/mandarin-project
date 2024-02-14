<?php $__env->startSection('title'); ?>
    Заявкa 
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main'); ?>   
<a href="/applications/<?php echo e($application->id); ?>/edit" class="nav-link">Изменить</a>
<table class="table table-striped-columns table-hover table-group-divider table-responsive">
  <tr>
    <th>№ заявки</th>
    <th>
      <?php echo e($application->id); ?>

    </th>
  </tr>
  <tr>
    <th>Дата заявки</th>
    <th>
      <?php echo e($application->created_at); ?>

    </th>
  </tr>
  <tr>
    <th>Дата последнего измененения</th>
    <th>
      <?php echo e($application->updated_at); ?>

    </th>
  </tr>
  <tr>
    <th>Страховая компания</th>
    <th>
      <?php echo e($application->name); ?>

    </th>
  </tr>
  <tr>
    <th>№ заявки страховой компании</th>
    <th>
      <?php echo e($application-> numberInsuranceCompanyApplication); ?>

    </th>
  </tr>
  <tr>
    <th>Марка машины</th>
    <th>
      <?php echo e($application-> CarModel); ?>

    </th>
  </tr>
  <tr>
    <th>ГНЗ</th>
    <th>
      <?php echo e($application-> gnz); ?>

    </th>
  </tr>
  <tr>
    <th>ВИН</th>
    <th>
      <?php echo e($application-> vin); ?>

    </th>
  </tr>
  <tr>
    <th>ФИО клиента</th>
    <th>
      <?php echo e($application-> ClientName); ?>

    </th>
  </tr>
  <tr>
    <th>Номер телефона клиента</th>
    <th>
      <?php echo e($application->  ClientPhone); ?>

    </th>
  </tr>
 
 
  

 </tbody>
</table>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('header_menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/aleksejmakarov/Yandex.Disk-aleksmakarov5.localized/www/mandarin/mandarin/resources/views/application/index.blade.php ENDPATH**/ ?>