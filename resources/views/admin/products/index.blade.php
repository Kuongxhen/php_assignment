@extends('layouts.app')

@section('title', 'Manage Products - Staff Admin')

@section('content')
<div class="wrap" style="max-width:1400px;margin:0 auto;padding:32px 32px 60px">
	<header style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px">
		<h2 style="margin:0">Manage Products</h2>
		<nav style="display:flex;gap:12px">
			<a class="link" href="{{ route('staffmod.admin.dashboard') }}">Dashboard</a>
			<a class="link" href="{{ route('staffmod.admin.createStaff') }}">Create Staff</a>
			<a class="link" href="{{ route('staffmod.admin.staffList') }}">Manage Staff</a>
			<a class="link" href="{{ route('staffmod.admin.stock.alerts') }}">Stock Alerts</a>
		</nav>
	</header>

	<!-- Action Buttons -->
	<section style="margin-bottom:24px">
		<div style="display:flex;gap:12px">
			<a class="btn btn--gold" href="{{ route('staffmod.admin.products.create') }}">Add New Product</a>
			<a class="btn btn--line" href="{{ route('staffmod.admin.stock.alerts') }}">View Stock Alerts</a>
		</div>
	</section>

	@if(session('success'))
		<div style="background:#10b981;color:white;padding:12px 16px;border-radius:8px;margin-bottom:16px">
			{{ session('success') }}
		</div>
	@endif

	@if(session('error'))
		<div style="background:#ef4444;color:white;padding:12px 16px;border-radius:8px;margin-bottom:16px">
			{{ session('error') }}
		</div>
	@endif

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
					@forelse($products as $product)
						<tr style="border-bottom:1px solid #f1f1f1">
							<td style="padding:12px">
								<div style="font-weight:600">{{ $product->name }}</div>
								<div class="muted" style="font-size:14px">{{ Str::limit($product->description ?? '', 50) }}</div>
							</td>
							<td style="padding:12px">{{ $product->sku }}</td>
							<td style="padding:12px">
								<span style="background:var(--gold);color:white;padding:4px 8px;border-radius:4px;font-size:12px">
									{{ $product->category }}
								</span>
							</td>
							<td style="padding:12px">${{ number_format($product->price, 2) }}</td>
							<td style="padding:12px">
								<span style="color:{{ $product->quantity <= $product->reorder_level ? '#ef4444' : '#10b981' }}">
									{{ $product->quantity }} {{ $product->unit }}
								</span>
								@if($product->quantity <= $product->reorder_level)
									<div style="font-size:12px;color:#ef4444">Low Stock!</div>
								@endif
							</td>
							<td style="padding:12px">
								@if($product->is_active)
									<span style="background:#10b981;color:white;padding:4px 8px;border-radius:4px;font-size:12px">Active</span>
								@else
									<span style="background:#6b7280;color:white;padding:4px 8px;border-radius:4px;font-size:12px">Inactive</span>
								@endif
							</td>
							<td style="padding:12px">
								<div style="display:flex;gap:8px">
									<a href="{{ route('staffmod.admin.products.edit', $product->product_id) }}" 
									   class="btn btn--line" style="padding:6px 12px;font-size:12px">Edit</a>
									<form method="POST" action="{{ route('staffmod.admin.products.delete', $product->product_id) }}" 
										  style="display:inline" onsubmit="return confirm('Are you sure you want to delete this product?')">
										@csrf
										@method('DELETE')
										<button type="submit" class="btn btn--line" 
												style="padding:6px 12px;font-size:12px;background:#ef4444;color:white">Delete</button>
									</form>
								</div>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="7" style="padding:32px;text-align:center;color:var(--muted)">
								No products found. <a href="{{ route('staffmod.admin.products.create') }}" style="color:var(--gold)">Add your first product</a>.
							</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
		
		@if(method_exists($products, 'hasPages') && $products->hasPages())
			<div style="padding:16px;border-top:1px solid var(--line)">
				{{ $products->links() }}
			</div>
		@endif
	</section>
</div>
@endsection


