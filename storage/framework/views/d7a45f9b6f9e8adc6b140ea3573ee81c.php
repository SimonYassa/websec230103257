

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>
                            <?php if(Auth::user()->hasRole('Admin')): ?>
                                <?php if($activeFilter === 'all'): ?>
                                    <?php echo e(__('All Users')); ?>

                                <?php elseif($activeFilter === 'employees'): ?>
                                    <?php echo e(__('Employees')); ?>

                                <?php elseif($activeFilter === 'admins'): ?>
                                    <?php echo e(__('Administrators')); ?>

                                <?php else: ?>
                                    <?php echo e(__('Customers')); ?>

                                <?php endif; ?>
                            <?php else: ?>
                                <?php echo e(__('Customers')); ?>

                            <?php endif; ?>
                        </h4>
                        <div>
                            
                            <?php if(Auth::user()->hasRole('Admin')): ?>
                                <a href="<?php echo e(route('users_create_employee')); ?>" class="btn btn-success"><?php echo e(__('Add New Employee')); ?></a>
                            <?php endif; ?>
                        </div>
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
                    
                    <?php if(Auth::user()->hasRole('Admin')): ?>
                    <div class="mb-4">
                        <div class="btn-group" role="group" aria-label="User filters">
                            <a href="<?php echo e(route('users_index')); ?>" class="btn btn-<?php echo e($activeFilter === 'all' ? 'primary' : 'outline-primary'); ?>">All Users</a>
                            <a href="<?php echo e(route('users_index', ['filter' => 'customers'])); ?>" class="btn btn-<?php echo e($activeFilter === 'customers' ? 'primary' : 'outline-primary'); ?>">Customers</a>
                            <a href="<?php echo e(route('users_index', ['filter' => 'employees'])); ?>" class="btn btn-<?php echo e($activeFilter === 'employees' ? 'primary' : 'outline-primary'); ?>">Employees</a>
                            <a href="<?php echo e(route('users_index', ['filter' => 'admins'])); ?>" class="btn btn-<?php echo e($activeFilter === 'admins' ? 'primary' : 'outline-primary'); ?>">Admins</a>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(count($users) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <?php if(Auth::user()->hasRole('Admin')): ?>
                                    <th>Role</th>
                                    <?php endif; ?>
                                    <th>Credit</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($user->id); ?></td>
                                    <td><?php echo e($user->name); ?></td>
                                    <td><?php echo e($user->email); ?></td>
                                    <?php if(Auth::user()->hasRole('Admin')): ?>
                                    <td>
                                        <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="badge bg-<?php echo e($role->name == 'Admin' ? 'danger' : ($role->name == 'Employee' ? 'warning' : 'success')); ?>">
                                                <?php echo e($role->name); ?>

                                            </span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                    <?php endif; ?>
                                    <td>$<?php echo e(number_format($user->credit, 2)); ?></td>
                                    <td>
                                        <a class="btn btn-info btn-sm" href="<?php echo e(route('users_show', $user->id)); ?>">View</a>
                                        <a class="btn btn-primary btn-sm" href="<?php echo e(route('users_edit', $user->id)); ?>">Edit</a>
                                        <?php if(!Auth::user()->hasRole('Admin') && $user->hasRole('Customer')): ?>
                                            <a class="btn btn-success btn-sm" href="<?php echo e(route('users_show_add_credit', $user->id)); ?>">Add Credit</a>
                                        <?php endif; ?>
                                        <form action="<?php echo e(route('users_destroy', $user->id)); ?>" method="POST" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <?php if(Auth::user()->hasRole('Admin')): ?>
                            <?php if($activeFilter === 'all'): ?>
                                No users found.
                            <?php elseif($activeFilter === 'employees'): ?>
                                No employees found. <a href="<?php echo e(route('users_create_employee')); ?>">Create a new employee</a>.
                            <?php elseif($activeFilter === 'admins'): ?>
                                No administrators found.
                            <?php else: ?>
                                No customers found.
                            <?php endif; ?>
                        <?php else: ?>
                            No customers found.
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\WebsecTest\Websec\resources\views/users/list.blade.php ENDPATH**/ ?>