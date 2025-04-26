

<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">WebSecTest Store</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('products_index')); ?>">Products</a>
                </li>
                <?php if(auth()->guard()->check()): ?>
                    <?php if(auth()->user()->isCustomer()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('purchases_index')); ?>">My Purchases</a>
                        </li>
                    <?php endif; ?>
                    <?php if(auth()->user()->isEmployee() || auth()->user()->isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('users_index')); ?>">Customers</a>
                        </li>
                    <?php endif; ?>
                    <?php if(auth()->user()->isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('users_create_employee')); ?>">Create Employee</a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if(auth()->guard()->guest()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('login')); ?>">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('register')); ?>">Register</a>
                </li>
                <?php else: ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo e(Auth::user()->name); ?>

                        <?php if(auth()->user()->isCustomer()): ?>
                            <span class="badge bg-success">$<?php echo e(number_format(auth()->user()->credit, 2)); ?></span>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="<?php echo e(route('profile')); ?>">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="<?php echo e(route('logout')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<?php /**PATH C:\xampp\htdocs\WebsecTest\Websec\resources\views/layouts/menu.blade.php ENDPATH**/ ?>