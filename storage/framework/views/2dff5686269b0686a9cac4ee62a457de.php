

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header"><?php echo e(__('Profile')); ?></div>

                <div class="card-body">
                    <?php if(session('status')): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo e(session('status')); ?>

                        </div>
                    <?php endif; ?>

                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Name:</div>
                        <div class="col-md-8"><?php echo e($user->name); ?></div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Email:</div>
                        <div class="col-md-8"><?php echo e($user->email); ?></div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Role:</div>
                        <div class="col-md-8"><?php echo e($user->getRoleNames()->implode(', ')); ?></div>
                    </div>

                    <?php if($user->isCustomer()): ?>
                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Account Credit:</div>
                        <div class="col-md-8">$<?php echo e(number_format($user->credit, 2)); ?></div>
                    </div>
                    <?php endif; ?>

                    <div class="row mb-3">
                        <div class="col-md-4 font-weight-bold">Member Since:</div>
                        <div class="col-md-8"><?php echo e($user->created_at->format('F j, Y')); ?></div>
                    </div>

                    <div class="mt-3">
                        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</a>
                        <a href="<?php echo e(route('edit_password')); ?>" class="btn btn-secondary">Change Password</a>
                    </div>
                </div>
            </div>

            <?php if($user->isCustomer()): ?>
            <div class="card">
                <div class="card-header"><?php echo e(__('My Recent Purchases')); ?></div>

                <div class="card-body">
                    <?php if(count($purchases) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $purchases->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($purchase->product->name); ?></td>
                                    <td><?php echo e($purchase->quantity); ?></td>
                                    <td>$<?php echo e(number_format($purchase->total, 2)); ?></td>
                                    <td><?php echo e($purchase->created_at->format('Y-m-d')); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="<?php echo e(route('purchases_index')); ?>" class="btn btn-info">View All Purchases</a>
                    <?php else: ?>
                    <p>You have not made any purchases yet.</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?php echo e(route('profile_update')); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo e($user->name); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo e($user->email); ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\WebsecTest\Websec\resources\views/users/profile.blade.php ENDPATH**/ ?>