

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Purchase Details</h4>
                    <div>
                        <a href="<?php echo e(route('purchases_index')); ?>" class="btn btn-secondary">Back to Purchases</a>
                        <form action="<?php echo e(route('purchases_destroy', $purchase->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this item from your cart? Your credit will be refunded.');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-danger">Remove from Cart</button>
                        </form>
                    </div>
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
                    
                    <?php if(session('info')): ?>
                        <div class="alert alert-info">
                            <?php echo e(session('info')); ?>

                        </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Purchase Information</h5>
                            <table class="table">
                                <tr>
                                    <th>Purchase ID:</th>
                                    <td><?php echo e($purchase->id); ?></td>
                                </tr>
                                <tr>
                                    <th>Date:</th>
                                    <td><?php echo e($purchase->created_at->format('M d, Y H:i')); ?></td>
                                </tr>
                                <tr>
                                    <th>Quantity:</th>
                                    <td><?php echo e($purchase->quantity); ?></td>
                                </tr>
                                <tr>
                                    <th>Price per Unit:</th>
                                    <td>$<?php echo e(number_format($purchase->price, 2)); ?></td>
                                </tr>
                                <tr>
                                    <th>Total:</th>
                                    <td>$<?php echo e(number_format($purchase->total, 2)); ?></td>
                                </tr>
                            </table>

                            <!-- Simplified Quantity Update Form -->
                            <div class="card mt-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Update Quantity</h5>
                                </div>
                                <div class="card-body">
                                    <form action="<?php echo e(route('purchases_update_quantity', $purchase->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <div class="mb-3">
                                            <label for="new_quantity" class="form-label">Quantity:</label>
                                            <input type="number" class="form-control" id="new_quantity" name="new_quantity" 
                                                min="1" 
                                                value="<?php echo e($purchase->quantity); ?>" 
                                                max="<?php echo e($purchase->quantity + ($purchase->product ? $purchase->product->stock : 0)); ?>"
                                                required>
                                            
                                            <div class="form-text">
                                                Use the arrows to increase or decrease the quantity.
                                                <?php if($purchase->product): ?>
                                                    <br>Maximum available: <?php echo e($purchase->quantity + $purchase->product->stock); ?> (current + stock)
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Price per Unit:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="text" class="form-control" value="<?php echo e(number_format($purchase->price, 2)); ?>" readonly>
                                            </div>
                                        </div>
                                        
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary">Update Cart</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Product Information</h5>
                            <?php if($purchase->product): ?>
                                <div class="card">
                                    <?php if($purchase->product->photo): ?>
                                        <img src="<?php echo e(asset('images/' . $purchase->product->photo)); ?>" class="card-img-top" alt="<?php echo e($purchase->product->name); ?>" style="height: 200px; object-fit: contain;">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo e($purchase->product->name); ?></h5>
                                        <p class="card-text"><?php echo e($purchase->product->description); ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-primary">$<?php echo e(number_format($purchase->product->price, 2)); ?></span>
                                            <?php if($purchase->product->stock > 0): ?>
                                                <span class="badge bg-success">In Stock (<?php echo e($purchase->product->stock); ?>)</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Out of Stock</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="mt-3">
                                            <a href="<?php echo e(route('products_show', $purchase->product->id)); ?>" class="btn btn-success">View Product</a>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    Product is no longer available.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\WebsecTest\Websec\resources\views/purchases/show.blade.php ENDPATH**/ ?>