<?php $__env->startSection('title'); ?>
    Аутентификация
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main_content'); ?>  

<main class="form-signin w-100 m-auto">
  <form method="POST" action="\login_process">

    <h1 class="h3 mb-3 fw-normal">Пожалуйста авторизуйтесь</h1>
     <?php echo csrf_field(); ?>
    <div class="form-floating">
      <input type="email" name='email' id="email" class="form-control" placeholder="name@example.com">
      <label for="email">Введите логин (ваш email)</label>
      <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
          <p class="text-red-500"><?php echo e($message); ?></p>
      <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="form-floating">
      <input type="password" name="password" id="password"  class="form-control" placeholder="Password">
      <label for="password">Введите пароль</label>
       <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
          <p class="text-red-500"><?php echo e($message); ?></p>
      <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <button class="nav-link btn btn-primary w-100 py-2" type="submit">Войти</button>
  </form>
</main>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/aleksejmakarov/Yandex.Disk-aleksmakarov5.localized/www/mandarin/mandarin/resources/views/auth/login.blade.php ENDPATH**/ ?>