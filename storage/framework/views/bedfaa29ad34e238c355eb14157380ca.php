
    <?php if($errors->any()): ?>
    <div>
        <ul>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<div class="form-floating">
    
    <select class="form-control" id="id_insuranceCompany" name="id_insuranceCompany">
        <?php $__currentLoopData = $InsuranceCompany; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($item->id); ?>"
                <?php if($item->id=== $application->id_insuranceCompany): ?>
                    selected
                <?php endif; ?>
                ><?php echo e($item->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      
    </select>
    <label for="id_insuranceCompany">Страховая компания</label>
  </div>

<div class="form-floating">
 <input type="text" name="numberInsuranceCompanyApplication" id="numberInsuranceCompanyApplication"  class="form-control"  value="<?php echo e($application->numberInsuranceCompanyApplication); ?>">
      <label for="numberInsuranceCompanyApplication">Номер заявки страховой компании</label>
</div>
  
<div class="form-floating">
 <input type="text" name="CarModel" id="CarModel"  class="form-control"  value="<?php echo e($application->CarModel); ?>">
      <label for="CarModel">Марка машины</label>
</div>
<div class="form-floating">
 <input type="text" name="gnz" id="gnz"  class="form-control"  value="<?php echo e($application->gnz); ?>">
      <label for="gnz">ГНЗ</label>
</div>
<div class="form-floating">
 <input type="text" name="vin" id="vin"  class="form-control"  value="<?php echo e($application->vin); ?>">
      <label for="vin">ВИН</label>
</div>
<div class="form-floating">
 <input type="text" name="ClientName" id="ClientName"  class="form-control"  value="<?php echo e($application->ClientName); ?>">
      <label for="ClientName">ФИО клиента</label>
</div>
<div class="form-floating">
 <input type="text" name="ClientPhone" id="ClientPhone"  class="form-control"  value="<?php echo e($application->ClientPhone); ?>">
      <label for="ClientPhone">Телефон клиента</label>
</div><?php /**PATH /Users/aleksejmakarov/Yandex.Disk-aleksmakarov5.localized/www/mandarin/mandarin/resources/views/application/form.blade.php ENDPATH**/ ?>