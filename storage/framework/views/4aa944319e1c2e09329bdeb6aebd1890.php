<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Admin - Products</h1>
        <a href="<?php echo e(route('admin.products.create')); ?>" class="btn btn-primary">Create Product</a>
    </div>
    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>SKU</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Cost</th>
                    <th>Qty</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($product->product_id); ?></td>
                    <td>
                        <?php if($product->image_path): ?>
                            <img src="<?php echo e(asset($product->image_path)); ?>" alt="<?php echo e($product->name); ?>" style="width: 40px; height: 40px; object-fit: cover;" class="rounded">
                        <?php else: ?>
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-image text-muted"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($product->sku); ?></td>
                    <td><?php echo e($product->name); ?></td>
                    <td><?php echo e($product->category); ?></td>
                    <td><?php echo e(number_format($product->price, 2)); ?></td>
                    <td><?php echo e(number_format($product->cost, 2)); ?></td>
                    <td><?php echo e($product->quantity); ?></td>
                    <td><?php echo e($product->is_active ? 'Yes' : 'No'); ?></td>
                    <td class="d-flex gap-2">
                        <a href="<?php echo e(route('admin.products.edit', $product->product_id)); ?>" class="btn btn-sm btn-warning">Edit</a>
                        <form method="POST" action="<?php echo e(route('admin.products.destroy', $product->product_id)); ?>" onsubmit="return confirm('Delete this product?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center">No products found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/myproject/resources/views/admin/products/index.blade.php ENDPATH**/ ?>