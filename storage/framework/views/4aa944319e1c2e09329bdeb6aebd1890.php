<?php $__env->startSection('title', 'Manage Products - Staff Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="wrap" style="max-width:1400px;margin:0 auto;padding:32px 32px 60px">
	<header style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px">
		<h2 style="margin:0">Manage Products</h2>
		<nav style="display:flex;gap:12px">
			<a class="link" href="<?php echo e(route('staffmod.admin.dashboard')); ?>">Dashboard</a>
			<a class="link" href="<?php echo e(route('staffmod.admin.createStaff')); ?>">Create Staff</a>
			<a class="link" href="<?php echo e(route('staffmod.admin.staffList')); ?>">Manage Staff</a>
			<a class="link" href="<?php echo e(route('staffmod.admin.stock.alerts')); ?>">Stock Alerts</a>
		</nav>
	</header>

	<!-- Action Buttons -->
	<section style="margin-bottom:24px">
		<div style="display:flex;gap:12px">
			<a class="btn btn--gold" href="<?php echo e(route('staffmod.admin.products.create')); ?>">Add New Product</a>
			<a class="btn btn--line" href="<?php echo e(route('staffmod.admin.stock.alerts')); ?>">View Stock Alerts</a>
		</div>
	</section>

	<?php if(session('success')): ?>
		<div style="background:#10b981;color:white;padding:12px 16px;border-radius:8px;margin-bottom:16px">
			<?php echo e(session('success')); ?>

		</div>
	<?php endif; ?>

	<?php if(session('error')): ?>
		<div style="background:#ef4444;color:white;padding:12px 16px;border-radius:8px;margin-bottom:16px">
			<?php echo e(session('error')); ?>

		</div>
	<?php endif; ?>

	<!-- Products Table -->
	<section style="background:#fff;border:1px solid var(--line);border-radius:14px;overflow:hidden">
		<div style="padding:16px;border-bottom:1px solid var(--line);background:#f8f9fa">
			<h3 style="margin:0">Product Inventory</h3>
		</div>
		<div style="overflow-x:auto">
			<table style="width:100%;border-collapse:collapse">
				<thead>
					<tr style="background:#f8f9fa">
						<th style="padding:12px;text-align:left;border-bottom:1px solid var(--line)">Product</th>
						<th style="padding:12px;text-align:left;border-bottom:1px solid var(--line)">SKU</th>
						<th style="padding:12px;text-align:left;border-bottom:1px solid var(--line)">Category</th>
						<th style="padding:12px;text-align:left;border-bottom:1px solid var(--line)">Price</th>
						<th style="padding:12px;text-align:left;border-bottom:1px solid var(--line)">Stock</th>
						<th style="padding:12px;text-align:left;border-bottom:1px solid var(--line)">Status</th>
						<th style="padding:12px;text-align:left;border-bottom:1px solid var(--line)">Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
						<tr style="border-bottom:1px solid #f1f1f1">
							<td style="padding:12px">
								<div style="font-weight:600"><?php echo e($product->name); ?></div>
								<div class="muted" style="font-size:14px"><?php echo e(Str::limit($product->description ?? '', 50)); ?></div>
							</td>
							<td style="padding:12px"><?php echo e($product->sku); ?></td>
							<td style="padding:12px">
								<span style="background:var(--gold);color:white;padding:4px 8px;border-radius:4px;font-size:12px">
									<?php echo e($product->category); ?>

								</span>
							</td>
							<td style="padding:12px">$<?php echo e(number_format($product->price, 2)); ?></td>
							<td style="padding:12px">
								<span style="color:<?php echo e($product->quantity <= $product->reorder_level ? '#ef4444' : '#10b981'); ?>">
									<?php echo e($product->quantity); ?> <?php echo e($product->unit); ?>

								</span>
								<?php if($product->quantity <= $product->reorder_level): ?>
									<div style="font-size:12px;color:#ef4444">Low Stock!</div>
								<?php endif; ?>
							</td>
							<td style="padding:12px">
								<?php if($product->is_active): ?>
									<span style="background:#10b981;color:white;padding:4px 8px;border-radius:4px;font-size:12px">Active</span>
								<?php else: ?>
									<span style="background:#6b7280;color:white;padding:4px 8px;border-radius:4px;font-size:12px">Inactive</span>
								<?php endif; ?>
							</td>
							<td style="padding:12px">
								<div style="display:flex;gap:8px">
									<a href="<?php echo e(route('staffmod.admin.products.edit', $product->product_id)); ?>" 
									   class="btn btn--line" style="padding:6px 12px;font-size:12px">Edit</a>
									<form method="POST" action="<?php echo e(route('staffmod.admin.products.delete', $product->product_id)); ?>" 
										  style="display:inline" onsubmit="return confirm('Are you sure you want to delete this product?')">
										<?php echo csrf_field(); ?>
										<?php echo method_field('DELETE'); ?>
										<button type="submit" class="btn btn--line" 
												style="padding:6px 12px;font-size:12px;background:#ef4444;color:white">Delete</button>
									</form>
								</div>
							</td>
						</tr>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
						<tr>
							<td colspan="7" style="padding:32px;text-align:center;color:var(--muted)">
								No products found. <a href="<?php echo e(route('staffmod.admin.products.create')); ?>" style="color:var(--gold)">Add your first product</a>.
							</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		
		<?php if(method_exists($products, 'hasPages') && $products->hasPages()): ?>
			<div style="padding:16px;border-top:1px solid var(--line)">
				<?php echo e($products->links()); ?>

			</div>
		<?php endif; ?>
	</section>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/myproject/resources/views/admin/products/index.blade.php ENDPATH**/ ?>