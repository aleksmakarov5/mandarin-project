<?php $__env->startSection('main_content'); ?>
   <a href="/SparePart/<?php echo e($sparepart->id_application); ?>" class="nav-link">Отмена</a>
<form action="/SparePart/<?php echo e($sparepart->id); ?>/update" method="POST">
     <?php echo csrf_field(); ?>
    
<input name = "id_application" type="hidden" value="<?php echo e($sparepart->id_application); ?>">

<div class="form-floating">

 <input type="text" name="name" id="name"  class="form-control" value="<?php echo e($sparepart->name); ?>" >
      <label for="name">Наименование</label>
</div>
  
<div class="form-floating">
 <input type="text" name="part_number" id="part_number"  class="form-control"   value="<?php echo e($sparepart->part_number); ?>">
      <label for="part_number">Кат. номер</label>
</div>
<div class="form-floating">
 <input type="text" name="order_number" id="order_number"  class="form-control"   value="<?php echo e($sparepart->part_number); ?>">
      <label for="order_number">Заказанный номер</label>
</div>
<div class="form-floating">
 <input type="text" name="price_original" id="price_original"  class="form-control"   value="<?php echo e($sparepart->price_original); ?>">
      <label for="price_original">Цена оригинал</label>
       <input type="checkbox" name="price_original_selected" id="price_original_selected"   value="<?php echo e($sparepart->price_original_selected); ?>">
</div>
<div class="form-floating">
 <input type="text" name="price_analogue" id="price_analogue"  class="form-control"   value="<?php echo e($sparepart->price_analogue); ?>">
      <label for="price_analogue">Цена аналог</label>
       <input type="checkbox" name="price_analogue_selected" id="price_analogue_selected"   value="<?php echo e($sparepart->price_analogue_selected); ?>">
</div>
<div class="form-floating">
 <input type="text" name="price_used" id="price_used"  class="form-control"   value="<?php echo e($sparepart->price_used); ?>">
      <label for="price_used">Цена б/у</label>
       <input type="checkbox" name="price_used_selected" id="price_used_selected"   value="<?php echo e($sparepart->price_used_selected); ?>">
</div>
<div class="form-floating">
<input type="text" name="status" id="status"  class="form-control"   value="<?php echo e($sparepart->status); ?>">
      <label for="status"> Статус</label>
</div>
<div class="form-floating">
<input type="date" name="date_price" id="date_price"  class="form-control"  value="<?php echo e($sparepart->date_price); ?>" >
      <label for="date_price"> Дата заказа</label>
</div>
   <input type="submit" value="Обновить"    class="nav-link">

</form>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/aleksejmakarov/Yandex.Disk-aleksmakarov5.localized/www/mandarin/mandarin/resources/views/SparePart/edit.blade.php ENDPATH**/ ?>