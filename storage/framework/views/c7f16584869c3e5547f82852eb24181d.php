<?php $__env->startSection('main_content'); ?>
   <a href="/SparePart/<?php echo e($id); ?>" class="nav-link">Отмена</a>
<form action="/SpareParts/store" method="POST">
     <?php echo csrf_field(); ?>
    
<input name = "id_application" type="hidden" value="<?php echo e($id); ?>">

<div class="form-floating">

 <input type="text" name="name" id="name"  class="form-control"  >
      <label for="name">Наименование</label>
</div>
  
<div class="form-floating">
 <input type="text" name="part_number" id="part_number"  class="form-control"  >
      <label for="part_number">Кат. номер</label>
</div>
<div class="form-floating">
 <input type="text" name="order_number" id="order_number"  class="form-control"  >
      <label for="order_number">Заказанный номер</label>
</div>
<div class="form-floating">
 <input type="text" name="price_original" id="price_original"  class="form-control"  >
      <label for="price_original">Цена оригинал</label>
       <input type="checkbox" name="price_original_selected" id="price_original_selected"  >
</div>
<div class="form-floating">
 <input type="text" name="price_analogue" id="price_analogue"  class="form-control"  >
      <label for="price_analogue">Цена аналог</label>
       <input type="checkbox" name="price_analogue_selected" id="price_analogue_selected"  >
</div>
<div class="form-floating">
 <input type="text" name="price_used" id="price_used"  class="form-control"  >
      <label for="price_used">Цена б/у</label>
       <input type="checkbox" name="price_used_selected" id="price_used_selected"  >
</div>
<div class="form-floating">
<input type="text" name="status" id="status"  class="form-control"  >
      <label for="status"> Статус</label>
</div>
<div class="form-floating">
<input type="date" name="date_price" id="date_price"  class="form-control"  >
      <label for="date_price"> Дата заказа</label>
</div>
   <input type="submit" value="Создать"    class="nav-link">

</form>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/aleksejmakarov/Yandex.Disk-aleksmakarov5.localized/www/mandarin/mandarin/resources/views/SparePart/create.blade.php ENDPATH**/ ?>