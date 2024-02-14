<?php $__env->startSection('title'); ?>
    Домашняя
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main_content'); ?>
<div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
  </div>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="img/carusel-1.png" class="d-block w-100" alt="...">
      <div class="carousel-caption d-none d-md-block">
         </div>
    </div>
    <div class="carousel-item">
      <img src="img/carusel-2.png" class="d-block w-100" alt="...">
      <div class="carousel-caption d-none d-md-block">
        </div>
    </div>
    <div class="carousel-item">
      <img src="img/carusel-3.png" class="d-block w-100" alt="...">
      <div class="carousel-caption d-none d-md-block">
        </div>
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Предыдущий</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Следующий</span>
  </button>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/aleksejmakarov/Yandex.Disk-aleksmakarov5.localized/www/mandarin/mandarin/resources/views/home.blade.php ENDPATH**/ ?>