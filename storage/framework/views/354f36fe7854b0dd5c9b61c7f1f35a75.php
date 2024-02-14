<?php $__env->startSection('main_content'); ?>
   <a href="/applications" class="nav-link">Отмена</a>
<form action="/applications/store" method="POST">
     <?php echo csrf_field(); ?>
    
     <div class="form-floating">
    
    <select class="form-control" id="id_insuranceCompany" name="id_insuranceCompany">
        <?php $__currentLoopData = $InsuranceCompany; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($item->id); ?>"
<?php if($item->id=== 4): ?>
                    selected
                <?php endif; ?>
                ><?php echo e($item->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      
    </select>
    <label for="id_insuranceCompany">Страховая компания</label>
  </div>

<div class="form-floating">
 <input type="text" name="numberInsuranceCompanyApplication" id="numberInsuranceCompanyApplication"  class="form-control"  >
      <label for="numberInsuranceCompanyApplication">Номер заявки страховой компании</label>
</div>
  
<div class="form-floating">
 <input type="text" name="CarModel" id="CarModel"  class="form-control"  >
      <label for="CarModel">Марка машины</label>
</div>
<div class="form-floating">
 <input type="text" name="gnz" id="gnz"  class="form-control" >
      <label for="gnz">ГНЗ</label>
</div>
<div class="form-floating">
 <input type="text" name="vin" id="vin"  class="form-control"  >
      <label for="vin">ВИН</label>
</div>
<div class="form-floating">
 <input type="text" name="ClientName" id="ClientName"  class="form-control"  >
      <label for="ClientName">ФИО клиента</label>
</div>
<div class="form-floating">
 <input type="text" name="ClientName" id="ClientName"  class="form-control"  >
      <label for="ClientName">Телефон клиента</label>
</div>
   <input type="submit" value="Создать"    class="nav-link">

</form>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/aleksejmakarov/Yandex.Disk-aleksmakarov5.localized/www/mandarin/mandarin/resources/views/application/create.blade.php ENDPATH**/ ?>