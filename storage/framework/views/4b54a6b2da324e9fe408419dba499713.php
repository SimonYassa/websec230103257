

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>My Purchases</h4>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>
                    
                    <?php if(session('error')): ?>
                        <div class="alert alert-danger">
                            <?php echo e(session('error')); ?>

                        </div>
                    <?php endif; ?>
                    
                    <?php if($purchases->isEmpty()): ?>
                        <div class="alert alert-info">
                            You have no purchases yet.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <?php if($purchase->product): ?>
                                                    <a href="<?php echo e(route('products_show', $purchase->product->id)); ?>">
                                                        <?php echo e($purchase->product->name); ?>

                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">Product no longer available</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>$<?php echo e(number_format($purchase->price, 2)); ?></td>
                                            <td><?php echo e($purchase->quantity); ?></td>
                                            <td>$<?php echo e(number_format($purchase->total, 2)); ?></td>
                                            <td><?php echo e($purchase->created_at->format('M d, Y H:i')); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?php echo e(route('purchases_show', $purchase->id)); ?>" class="btn btn-sm btn-info">
                                                        View
                                                    </a>
                                                    <form action="<?php echo e(route('purchases_destroy', $purchase->id)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to remove this item from your cart? Your credit will be refunded.');">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            <h5>Total Spent: $<?php echo e(number_format($purchases->sum('total'), 2)); ?></h5>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\WebsecTest\Websec\resources\views/purchases/index.blade.php ENDPATH**/ ?>