<?php $__env->startSection('main_content'); ?>
   <a href="/Work/<?php echo e($id); ?>" class="nav-link">Отмена</a>
<form action="/Works/store" method="POST">
     <?php echo csrf_field(); ?>
    
<input name = "id_application" type="hidden" value="<?php echo e($id); ?>">

<div class="form-floating">

 <input type="text" name="name" id="name"  class="form-control"  >
      <label for="name">Наименование</label>
</div>
  
<div class="form-floating">
 <input type="text" name="price" id="price"  class="form-control"  >
      <label for="price">Цена</label>
</div>
<div class="form-floating">
 <input type="text" name="count_hours" id="count_hours"  class="form-control"  >
      <label for="count_hours">Количество нормочасов</label>
</div>
<div class="form-floating">
 <input type="text" name="summ" id="summ"  class="form-control"  >
      <label for="summ">Сумма</label>
</div>

   <input type="submit" value="Создать"    class="nav-link">

</form>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/aleksejmakarov/Yandex.Disk-aleksmakarov5.localized/www/mandarin/mandarin/resources/views/Work/create.blade.php ENDPATH**/ ?>