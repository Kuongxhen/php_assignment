<?php $__env->startSection('title', 'Products - Clinique Aurelia'); ?>

<?php $__env->startSection('content'); ?>


<?php $__env->startSection('title', 'Products - Clinique Aurelia'); ?>

<?php $__env->startSection('content'); ?>
<div style="background:var(--paper);min-height:100vh;padding:24px 0">
    <div class="wrap">
        <!-- Header Section -->
        <div style="text-align:center;margin-bottom:48px">
            <h1 style="font-size:48px;font-weight:800;color:var(--ink);margin:0 0 16px 0;font-family:'Courier New',monospace">Our Products</h1>
            <p style="font-size:20px;color:var(--muted);max-width:600px;margin:0 auto;line-height:1.6">
                Discover our comprehensive range of medical products and equipment designed to support your health and wellness journey.
            </p>
        </div>

        <!-- Navigation Back -->
        <div style="margin-bottom:32px">
            <a href="<?php echo e(route('home')); ?>" 
               style="display:inline-flex;align-items:center;gap:8px;color:var(--ink);text-decoration:none;padding:12px 20px;border:1px solid var(--line);border-radius:10px;font-family:'Courier New',monospace;font-weight:600;transition:all 0.2s"
               onmouseover="this.style.background='var(--line)'" 
               onmouseout="this.style.background='transparent'">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Home
            </a>
        </div>

        <!-- Products Grid -->
        <?php if($products->count() > 0): ?>
            <!-- Category Filter -->
            <div style="margin-bottom:32px;text-align:center">
                <div style="display:inline-flex;gap:12px;padding:8px;background:white;border:1px solid var(--line);border-radius:14px;box-shadow:0 4px 12px rgba(0,0,0,0.05)">
                    <button onclick="filterProducts('all')" 
                            class="category-btn active-category"
                            style="padding:10px 20px;border:none;background:linear-gradient(180deg, var(--gold), var(--gold-2));color:#141414;border-radius:10px;font-family:'Courier New',monospace;font-weight:600;cursor:pointer;transition:all 0.2s">
                        All Products
                    </button>
                    <button onclick="filterProducts('Medication')" 
                            class="category-btn"
                            style="padding:10px 20px;border:none;background:transparent;color:var(--ink);border-radius:10px;font-family:'Courier New',monospace;font-weight:600;cursor:pointer;transition:all 0.2s">
                        💊 Medication
                    </button>
                    <button onclick="filterProducts('Supplement')" 
                            class="category-btn"
                            style="padding:10px 20px;border:none;background:transparent;color:var(--ink);border-radius:10px;font-family:'Courier New',monospace;font-weight:600;cursor:pointer;transition:all 0.2s">
                        🧪 Supplements
                    </button>
                    <button onclick="filterProducts('Equipment')" 
                            class="category-btn"
                            style="padding:10px 20px;border:none;background:transparent;color:var(--ink);border-radius:10px;font-family:'Courier New',monospace;font-weight:600;cursor:pointer;transition:all 0.2s">
                        🏥 Equipment
                    </button>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:24px">
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="product-card" 
                         data-category="<?php echo e($product->category); ?>"
                         style="background:white;border:1px solid var(--line);border-radius:14px;overflow:hidden;transition:all 0.3s;box-shadow:0 4px 12px rgba(0,0,0,0.05)"
                         onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.1)'" 
                         onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)'">
                        
                        <!-- Product Image -->
                        <div style="height:200px;background:linear-gradient(135deg,#f8fafc,#e2e8f0);display:flex;align-items:center;justify-content:center;border-bottom:1px solid var(--line)">
                            <?php if($product->product_image && file_exists(public_path('storage/' . $product->product_image))): ?>
                                <img src="<?php echo e(asset('storage/' . $product->product_image)); ?>" 
                                     alt="<?php echo e($product->name); ?>"
                                     style="max-width:100%;max-height:100%;object-fit:contain;border-radius:8px">
                            <?php else: ?>
                                <div style="text-align:center;color:var(--muted)">
                                    <svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-bottom:8px">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    <div style="font-family:'Courier New',monospace;font-size:14px">No Image</div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Product Content -->
                        <div style="padding:20px">
                            <!-- Category Badge -->
                            <div style="margin-bottom:12px">
                                <?php if($product->category === 'Medication'): ?>
                                    <span style="background:#dbeafe;color:#1e40af;padding:4px 8px;border-radius:6px;font-size:12px;font-weight:600;font-family:'Courier New',monospace">💊 Medication</span>
                                <?php elseif($product->category === 'Supplement'): ?>
                                    <span style="background:#dcfce7;color:#166534;padding:4px 8px;border-radius:6px;font-size:12px;font-weight:600;font-family:'Courier New',monospace">🧪 Supplement</span>
                                <?php elseif($product->category === 'Equipment'): ?>
                                    <span style="background:#fef3c7;color:#92400e;padding:4px 8px;border-radius:6px;font-size:12px;font-weight:600;font-family:'Courier New',monospace">🏥 Equipment</span>
                                <?php endif; ?>
                            </div>

                            <!-- Product Name -->
                            <h3 style="font-size:20px;font-weight:700;color:var(--ink);margin:0 0 8px 0;font-family:'Courier New',monospace;line-height:1.3">
                                <?php echo e($product->name); ?>

                            </h3>

                            <!-- SKU -->
                            <p style="color:var(--muted);font-size:14px;margin:0 0 12px 0;font-family:'Courier New',monospace">
                                SKU: <?php echo e($product->sku); ?>

                            </p>

                            <!-- Description -->
                            <?php if($product->description): ?>
                                <p style="color:var(--muted);font-size:16px;line-height:1.5;margin:0 0 16px 0">
                                    <?php echo e(Str::limit($product->description, 120)); ?>

                                </p>
                            <?php endif; ?>

                            <!-- Product Details -->
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
                                <div>
                                    <div style="color:var(--muted);font-size:12px;font-family:'Courier New',monospace;font-weight:600;margin-bottom:4px">PRICE</div>
                                    <div style="color:var(--ink);font-size:18px;font-weight:700;font-family:'Courier New',monospace">
                                        $<?php echo e(number_format($product->price, 2)); ?>

                                    </div>
                                </div>
                                <div>
                                    <div style="color:var(--muted);font-size:12px;font-family:'Courier New',monospace;font-weight:600;margin-bottom:4px">UNIT</div>
                                    <div style="color:var(--ink);font-size:16px;font-weight:600;font-family:'Courier New',monospace">
                                        <?php echo e($product->unit ?? 'pcs'); ?>

                                    </div>
                                </div>
                            </div>

                            <!-- Manufacturer -->
                            <?php if($product->manufacturer): ?>
                                <div style="margin-bottom:16px">
                                    <div style="color:var(--muted);font-size:12px;font-family:'Courier New',monospace;font-weight:600;margin-bottom:4px">MANUFACTURER</div>
                                    <div style="color:var(--ink);font-size:14px;font-family:'Courier New',monospace">
                                        <?php echo e($product->manufacturer); ?>

                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Availability Status -->
                            <div style="display:flex;align-items:center;justify-content:space-between;padding-top:16px;border-top:1px solid var(--line)">
                                <div style="display:flex;align-items:center;gap:8px">
                                    <?php if($product->quantity > $product->reorder_level): ?>
                                        <div style="width:8px;height:8px;background:#10b981;border-radius:50%"></div>
                                        <span style="color:#10b981;font-size:14px;font-weight:600;font-family:'Courier New',monospace">In Stock</span>
                                    <?php elseif($product->quantity > 0): ?>
                                        <div style="width:8px;height:8px;background:#f59e0b;border-radius:50%"></div>
                                        <span style="color:#f59e0b;font-size:14px;font-weight:600;font-family:'Courier New',monospace">Low Stock</span>
                                    <?php else: ?>
                                        <div style="width:8px;height:8px;background:#ef4444;border-radius:50%"></div>
                                        <span style="color:#ef4444;font-size:14px;font-weight:600;font-family:'Courier New',monospace">Out of Stock</span>
                                    <?php endif; ?>
                                </div>
                                <div style="color:var(--muted);font-size:14px;font-family:'Courier New',monospace">
                                    Qty: <?php echo e($product->quantity); ?>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div style="text-align:center;padding:64px 24px;background:white;border:1px solid var(--line);border-radius:14px">
                <svg width="96" height="96" fill="none" stroke="var(--muted)" viewBox="0 0 24 24" style="margin:0 auto 24px">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <h3 style="font-size:24px;font-weight:700;color:var(--ink);margin:0 0 12px 0;font-family:'Courier New',monospace">No Products Available</h3>
                <p style="color:var(--muted);font-size:16px;max-width:400px;margin:0 auto">
                    We're currently updating our product catalog. Please check back soon for our latest offerings.
                </p>
            </div>
        <?php endif; ?>

        <!-- Contact Section -->
        <div style="margin-top:64px;padding:32px;background:white;border:1px solid var(--line);border-radius:14px;text-align:center">
            <h3 style="font-size:24px;font-weight:700;color:var(--ink);margin:0 0 12px 0;font-family:'Courier New',monospace">Need More Information?</h3>
            <p style="color:var(--muted);font-size:16px;margin:0 0 24px 0">
                Contact our team for detailed product information, pricing, or to place an order.
            </p>
            <a href="#contact" 
               onclick="window.location='<?php echo e(route('home')); ?>#contact'"
               style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(180deg, var(--gold), var(--gold-2));color:#141414;text-decoration:none;padding:12px 24px;border-radius:10px;font-family:'Courier New',monospace;font-weight:600;transition:all 0.2s"
               onmouseover="this.style.transform='translateY(-2px)'" 
               onmouseout="this.style.transform='translateY(0)'">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Contact Us
            </a>
        </div>
    </div>
</div>

<script>
function filterProducts(category) {
    const productCards = document.querySelectorAll('.product-card');
    const categoryBtns = document.querySelectorAll('.category-btn');
    
    // Update button states
    categoryBtns.forEach(btn => {
        btn.style.background = 'transparent';
        btn.style.color = 'var(--ink)';
        btn.classList.remove('active-category');
    });
    
    // Highlight active button
    event.target.style.background = 'linear-gradient(180deg, var(--gold), var(--gold-2))';
    event.target.style.color = '#141414';
    event.target.classList.add('active-category');
    
    // Filter products
    productCards.forEach(card => {
        if (category === 'all' || card.dataset.category === category) {
            card.style.display = 'block';
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 50);
        } else {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.display = 'none';
            }, 300);
        }
    });
}

// Initialize smooth transitions
document.addEventListener('DOMContentLoaded', function() {
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
        card.style.transition = 'all 0.3s ease';
    });
});
</script>

<style>
/* Smooth transitions for filtering */
.product-card {
    transition: opacity 0.3s ease, transform 0.3s ease !important;
}

/* Category button hover effects */
.category-btn:hover:not(.active-category) {
    background: var(--line) !important;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/myproject/resources/views/products.blade.php ENDPATH**/ ?>