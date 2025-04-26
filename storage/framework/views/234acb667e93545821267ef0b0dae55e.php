

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4><?php echo e(__('Product Details')); ?></h4>
                        <a href="<?php echo e(route('products_index')); ?>" class="btn btn-secondary">Back</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <?php if($product->photo): ?>
                                <img src="<?php echo e(asset('images/' . $product->photo)); ?>" alt="<?php echo e($product->name); ?>" class="img-fluid">
                            <?php else: ?>
                                <div class="bg-light p-5 text-center">No Image</div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <h3><?php echo e($product->name); ?></h3>
                            <p><strong>Code:</strong> <?php echo e($product->code); ?></p>
                            <p><strong>Model:</strong> <?php echo e($product->model); ?></p>
                            <p><strong>Price:</strong> $<?php echo e(number_format($product->price, 2)); ?></p>
                            <p><strong>Stock:</strong> <?php echo e($product->stock); ?> units</p>
                            <p><strong>Description:</strong> <?php echo e($product->description); ?></p>
                            
                            <?php if(auth()->user()->isCustomer()): ?>
                                <?php if($product->stock > 0): ?>
                                    <form action="<?php echo e(route('products_purchase', $product->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <div class="form-group mb-3">
                                            <label for="quantity">Quantity:</label>
                                            <input type="number" name="quantity" id="quantity" class="form-control" value="1" min="1" max="<?php echo e($product->stock); ?>">
                                        </div>
                                        <button type="submit" class="btn btn-primary">Purchase</button>
                                    </form>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        This product is currently out of stock.
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php if(auth()->user()->isEmployee()): ?>
                                <div class="mt-3">
                                    <a href="<?php echo e(route('products_edit', $product->id)); ?>" class="btn btn-primary">Edit</a>
                                    <form action="<?php echo e(route('products_destroy', $product->id)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                                    </form>
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


<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\WebsecTest\Websec\resources\views/products/show.blade.php ENDPATH**/ ?>