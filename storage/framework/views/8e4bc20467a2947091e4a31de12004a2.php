

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row my-4">
        <div class="col-12">
            <h1>All Products</h1>
        </div>
    </div>

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

    <!-- Admin/Employee Controls -->
    <?php if(Auth::user() && (Auth::user()->hasRole('Employee') || Auth::user()->hasRole('Admin'))): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Product Management</h5>
                    <a href="<?php echo e(route('products_create')); ?>" class="btn btn-success">Add New Product</a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Vertical Product List -->
    <div class="row">
        <div class="col-12">
            <?php if($products->isEmpty()): ?>
            <div class="alert alert-info">
                No products available at this time.
            </div>
            <?php else: ?>
            <div class="row">
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="row g-0">
                            <div class="col-md-3">
                                <div style="height: 200px; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                                    <?php if($product->photo): ?>
                                    <img src="<?php echo e(asset('images/' . $product->photo)); ?>" alt="<?php echo e($product->name); ?>" class="img-fluid" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                    <?php else: ?>
                                    <div class="text-center p-4">No Image</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h5 class="card-title"><?php echo e($product->name); ?></h5>
                                        <span class="badge bg-primary">$<?php echo e(number_format($product->price, 2)); ?></span>
                                    </div>
                                    <p class="card-text text-muted"><?php echo e($product->model); ?></p>
                                    <p class="card-text">
                                        <?php echo e(Str::limit($product->description, 150)); ?>

                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <?php if($product->stock > 0): ?>
                                            <span class="badge bg-success">In Stock (<?php echo e($product->stock); ?>)</span>
                                            <?php else: ?>
                                            <span class="badge bg-danger">Out of Stock</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="btn-group">
                                            <a href="<?php echo e(route('products_show', $product->id)); ?>" class="btn btn-success">View Details</a>
                                            <a href="<?php echo e(route('add-stock', $product->id)); ?>" class="btn btn-success">Add Stock</a>
                                            
                                            <?php if(Auth::user() && (Auth::user()->hasRole('Employee') || Auth::user()->hasRole('Admin'))): ?>
                                            <a href="<?php echo e(route('products_edit', $product->id)); ?>" class="btn btn-secondary">Edit</a>
                                            <form action="<?php echo e(route('products_destroy', $product->id)); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                                            </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\WebsecTest\Websec\resources\views/products/list.blade.php ENDPATH**/ ?>