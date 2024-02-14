<?php $__env->startSection('title'); ?>
    Домашняя
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main_content'); ?>
<h1>Заявка № <?php echo e($id); ?>  </h1> 
<header class="d-flex justify-content-center py-3">
      <ul class="nav nav-pills">
        <li class="nav-item"><a href="/application/<?php echo e($id); ?>" class="nav-link <?php echo e(request()->routeIs('application') ? 'tn-active' : ''); ?>" aria-current="page">Основная информация</a></li>
         <li class="nav-item"><a href="/SparePart/<?php echo e($id); ?>" class="nav-link  <?php echo e(request()->routeIs('SparePart') ? 'tn-active' : ''); ?>" aria-current="page">Запчасти</a></li>
        <li class="nav-item"><a href="/Work/<?php echo e($id); ?>" class="nav-link  <?php echo e(request()->routeIs('Work') ? 'tn-active' : ''); ?>" aria-current="page">Работы</a></li>
        <li class="nav-item"><a href="/mail/<?php echo e($id); ?>" class="nav-link <?php echo e(request()->routeIs('mail_app') ? 'tn-active' : ''); ?>" aria-current="page">Чат</a></li>
         <li class="nav-item"><a href="/document/<?php echo e($id); ?>" class="nav-link  <?php echo e(request()->routeIs('document') ? 'tn-active' : ''); ?>" aria-current="page">Документы</a></li>
       <li class="nav-item"><a href="#" class="nav-link">История</a></li>
      </ul>
    </header>
<?php echo $__env->yieldContent('main'); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/aleksejmakarov/Yandex.Disk-aleksmakarov5.localized/www/mandarin/mandarin/resources/views/header_menu.blade.php ENDPATH**/ ?>