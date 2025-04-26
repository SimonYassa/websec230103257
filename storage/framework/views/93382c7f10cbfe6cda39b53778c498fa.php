

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><?php echo e(__('Add Credit to User Account')); ?></div>

                <div class="card-body">
                    <div class="mb-4">
                        <h5>User: <?php echo e($user->name); ?></h5>
                        <p>Current Credit: $<?php echo e(number_format($user->credit, 2)); ?></p>
                    </div>

                    <form method="POST" action="<?php echo e(route('users_add_credit', $user->id)); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="form-group row mb-3">
                            <label for="amount" class="col-md-4 col-form-label text-md-right"><?php echo e(__('Amount to Add ($)')); ?></label>

                            <div class="col-md-6">
                                <input id="amount" type="number" step="0.01" min="0.01" class="form-control <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="amount" value="<?php echo e(old('amount')); ?>" required>

                                <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-feedback" role="alert">
                                        <strong><?php echo e($message); ?></strong>
                                    </span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <?php echo e(__('Add Credit')); ?>

                                </button>
                                <a href="<?php echo e(route('users_index')); ?>" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\WebsecTest\Websec\resources\views/users/add_credit.blade.php ENDPATH**/ ?>