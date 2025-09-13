<?php $__env->startSection('content'); ?>
<div class="container">
    <h1>Edit Product</h1>
    <form method="POST" action="<?php echo e(route('staffmod.admin.products.update', $product->product_id)); ?>" class="mt-3" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">SKU</label>
                <input name="sku" value="<?php echo e(old('sku', $product->sku)); ?>" class="form-control" required>
                <?php $__errorArgs = ['sku'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-8">
                <label class="form-label">Name</label>
                <input name="name" value="<?php echo e(old('name', $product->name)); ?>" class="form-control" required>
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Category</label>
                <select name="category" class="form-select" required>
                    <option value="Medication" <?php echo e(old('category', $product->category)=='Medication'?'selected':''); ?>>Medication</option>
                    <option value="Supplement" <?php echo e(old('category', $product->category)=='Supplement'?'selected':''); ?>>Supplement</option>
                    <option value="Equipment" <?php echo e(old('category', $product->category)=='Equipment'?'selected':''); ?>>Equipment</option>
                </select>
                <?php $__errorArgs = ['category'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control"><?php echo e(old('description', $product->description)); ?></textarea>
            </div>
            <div class="col-md-3">
                <label class="form-label">Price</label>
                <input type="number" step="0.01" min="0" name="price" value="<?php echo e(old('price', $product->price)); ?>" class="form-control" required>
                <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-3">
                <label class="form-label">Cost</label>
                <input type="number" step="0.01" min="0" name="cost" value="<?php echo e(old('cost', $product->cost)); ?>" class="form-control" required>
                <?php $__errorArgs = ['cost'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-3">
                <label class="form-label">Quantity</label>
                <input type="number" min="0" name="quantity" value="<?php echo e(old('quantity', $product->quantity)); ?>" class="form-control" required>
                <?php $__errorArgs = ['quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-3">
                <label class="form-label">Expiration Date</label>
                <input type="date" name="expiration_date" value="<?php echo e(old('expiration_date', optional($product->expiration_date)->format('Y-m-d'))); ?>" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Manufacturer</label>
                <input name="manufacturer" value="<?php echo e(old('manufacturer', $product->manufacturer)); ?>" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Reorder Level</label>
                <input type="number" min="0" name="reorder_level" value="<?php echo e(old('reorder_level', $product->reorder_level)); ?>" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Unit</label>
                <input name="unit" value="<?php echo e(old('unit', $product->unit)); ?>" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Product Image</label>
                <input type="file" name="product_image" class="form-control" accept="image/*">
                <div class="form-text">Upload new product image (JPG, PNG, GIF). Max size: 2MB. Leave empty to keep current image.</div>
                <?php if($product->image_path): ?>
                    <div class="mt-2">
                        <small class="text-muted">Current image:</small><br>
                        <img src="<?php echo e(asset($product->image_path)); ?>" alt="<?php echo e($product->name); ?>" style="max-width: 100px; max-height: 100px; object-fit: cover;" class="border rounded">
                    </div>
                <?php endif; ?>
                <?php $__errorArgs = ['product_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-3">
                <label class="form-label">Active</label>
                <select name="is_active" class="form-select">
                    <option value="1" <?php echo e(old('is_active', $product->is_active) == 1 ? 'selected' : ''); ?>>Yes</option>
                    <option value="0" <?php echo e(old('is_active', $product->is_active) == 0 ? 'selected' : ''); ?>>No</option>
                </select>
            </div>
        </div>
        <div class="mt-3">
            <button class="btn btn-primary">Update</button>
            <a href="<?php echo e(route('staffmod.admin.products')); ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/myproject/resources/views/admin/products/edit.blade.php ENDPATH**/ ?>