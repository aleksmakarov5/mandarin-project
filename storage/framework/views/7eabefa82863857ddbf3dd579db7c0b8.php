<?php $__env->startSection('title'); ?>
    Регистрация
<?php $__env->stopSection(); ?>
<?php $__env->startSection('main_content'); ?>  
<main class="form-signin w-100 m-auto">
  <form method="POST" action="\register_process">

    <h1 class="h3 mb-3 fw-normal">Регистрация</h1>
     <?php echo csrf_field(); ?>
      <div class="form-floating">
      <input type="text" name='name' id="name" class="form-control" id="floatingInput" placeholder="ФИО">
      <label for="floatingInput">Ваше имя</label>
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
      <input type="email" name='email' id="email" class="form-control" id="floatingInput" placeholder="name@example.com">
      <label for="floatingInput">Email address</label>
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
      <input type="password" name="password" id="password"  class="form-control"  placeholder="Password">
      <label for="floatingPassword">Пароль</label>
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
       <div class="form-floating">
      <input type="password" name="password_confirmation" id="password_confirmation"  class="form-control"  placeholder="Password">
      <label for="floatingPassword">Подтверждение пароля</label>
       
    </div>
    <button class="tn-atom btn btn-primary w-100 py-2" type="submit">Регистрация</button>
  </form>
</main>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/aleksejmakarov/Yandex.Disk-aleksmakarov5.localized/www/mandarin/mandarin/resources/views/auth/register.blade.php ENDPATH**/ ?>