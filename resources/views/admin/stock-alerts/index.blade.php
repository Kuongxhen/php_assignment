@extends('layouts.app')

@section('title', 'Stock Alerts - Staff Admin')

@section('content')
<div class="wrap" style="max-width:1400px;margin:0 auto;padding:32px 32px 60px">
	<header style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px">
		<h2 style="margin:0">Stock Alerts</h2>
		<nav style="display:flex;gap:12px">
			<a class="link" href="{{ route('staffmod.admin.dashboard') }}">Dashboard</a>
			<a class="link" href="{{ route('staffmod.admin.createStaff') }}">Create Staff</a>
			<a class="link" href="{{ route('staffmod.admin.staffList') }}">Manage Staff</a>
			<a class="link" href="{{ route('staffmod.admin.products') }}">Manage Products</a>
		</nav>
	</header>

	<!-- Statistics Cards -->
	<section style="display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:24px">
		<div class="card" style="padding:16px;border:1px solid var(--line);border-radius:14px;background:#fff">
			<div class="muted">Total Alerts</div>
			<div style="font-size:28px;font-weight:800">{{ $stats['total_alerts'] ?? 0 }}</div>
		</div>
		<div class="card" style="padding:16px;border:1px solid var(--line);border-radius:14px;background:#fff">
			<div class="muted">Active Alerts</div>
			<div style="font-size:28px;font-weight:800;color:#f59e0b">{{ $stats['active_alerts'] ?? 0 }}</div>
		</div>
		<div class="card" style="padding:16px;border:1px solid var(--line);border-radius:14px;background:#fff">
			<div class="muted">Critical Alerts</div>
			<div style="font-size:28px;font-weight:800;color:#ef4444">{{ $stats['critical_alerts'] ?? 0 }}</div>
		</div>
		<div class="card" style="padding:16px;border:1px solid var(--line);border-radius:14px;background:#fff">
			<div class="muted">Pending Reorders</div>
			<div style="font-size:28px;font-weight:800;color:#8b5cf6">{{ $stats['pending_reorders'] ?? 0 }}</div>
		</div>
		<div class="card" style="padding:16px;border:1px solid var(--line);border-radius:14px;background:#fff">
			<div class="muted">Total Reorders</div>
			<div style="font-size:28px;font-weight:800;color:#10b981">{{ $stats['total_reorders'] ?? 0 }}</div>
		</div>
	</section>

	<!-- Action Buttons -->
	<section style="margin-bottom:24px">
		<div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
			<a class="btn btn--gold" href="{{ route('staffmod.admin.products') }}">Manage Products</a>
			<form method="POST" action="{{ route('staffmod.admin.stock.trigger-check') }}" style="display:inline">
				@csrf
				<button type="submit" class="btn" style="background:#059669;color:white;border:1px solid #059669">
					🔄 Check All Products
				</button>
			</form>
		</div>
	</section>

	<!-- Filters Section -->
	<section style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);border-radius:16px;padding:24px;margin-bottom:24px;box-shadow:0 10px 25px rgba(0,0,0,0.1)">
		<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
			<h3 style="margin:0;font-size:18px;color:white;font-weight:600;display:flex;align-items:center;gap:8px;font-family:'Courier New', Courier, monospace">
				<svg style="width:20px;height:20px;fill:white" viewBox="0 0 20 20">
					<path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/>
				</svg>
				Filter Options
			</h3>
			<div style="background:rgba(255,255,255,0.2);padding:6px 12px;border-radius:20px;color:white;font-size:14px;font-family:'Courier New', Courier, monospace">
				{{ $alerts->total() }} alerts • {{ $reorderRequests->total() }} reorders
			</div>
		</div>
		
		<form method="GET" action="{{ route('staffmod.admin.stock.alerts') }}" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:16px;align-items:end">
			<div style="display:flex;flex-direction:column;gap:6px">
				<label style="font-size:14px;font-weight:600;color:rgba(255,255,255,0.9);font-family:'Courier New', Courier, monospace">Alert Status</label>
				<div style="position:relative">
					<select name="status" style="width:100%;padding:12px 16px;border:none;border-radius:10px;background:rgba(255,255,255,0.95);color:#374151;font-weight:500;box-shadow:0 4px 6px rgba(0,0,0,0.05);appearance:none;cursor:pointer;font-family:'Courier New', Courier, monospace;font-size:16px">
						<option value="all" {{ ($status ?? 'all') === 'all' ? 'selected' : '' }}>🔄 All Statuses</option>
						<option value="active" {{ ($status ?? '') === 'active' ? 'selected' : '' }}>🟢 Active</option>
						<option value="acknowledged" {{ ($status ?? '') === 'acknowledged' ? 'selected' : '' }}>🟡 Acknowledged</option>
						<option value="resolved" {{ ($status ?? '') === 'resolved' ? 'selected' : '' }}>✅ Resolved</option>
					</select>
					<svg style="position:absolute;right:12px;top:50%;transform:translateY(-50%);width:16px;height:16px;fill:#6b7280;pointer-events:none" viewBox="0 0 20 20">
						<path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
					</svg>
				</div>
			</div>
			
			<div style="display:flex;flex-direction:column;gap:6px">
				<label style="font-size:14px;font-weight:600;color:rgba(255,255,255,0.9);font-family:'Courier New', Courier, monospace">Alert Severity</label>
				<div style="position:relative">
					<select name="severity" style="width:100%;padding:12px 16px;border:none;border-radius:10px;background:rgba(255,255,255,0.95);color:#374151;font-weight:500;box-shadow:0 4px 6px rgba(0,0,0,0.05);appearance:none;cursor:pointer;font-family:'Courier New', Courier, monospace;font-size:16px">
						<option value="all" {{ ($severity ?? 'all') === 'all' ? 'selected' : '' }}>🎯 All Severities</option>
						<option value="critical" {{ ($severity ?? '') === 'critical' ? 'selected' : '' }}>🔴 Critical</option>
						<option value="high" {{ ($severity ?? '') === 'high' ? 'selected' : '' }}>🟠 High</option>
						<option value="medium" {{ ($severity ?? '') === 'medium' ? 'selected' : '' }}>🟡 Medium</option>
						<option value="low" {{ ($severity ?? '') === 'low' ? 'selected' : '' }}>🟢 Low</option>
					</select>
					<svg style="position:absolute;right:12px;top:50%;transform:translateY(-50%);width:16px;height:16px;fill:#6b7280;pointer-events:none" viewBox="0 0 20 20">
						<path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
					</svg>
				</div>
			</div>
			
			<div style="display:flex;flex-direction:column;gap:6px">
				<label style="font-size:14px;font-weight:600;color:rgba(255,255,255,0.9);font-family:'Courier New', Courier, monospace">Reorder Status</label>
				<div style="position:relative">
					<select name="reorder_status" style="width:100%;padding:12px 16px;border:none;border-radius:10px;background:rgba(255,255,255,0.95);color:#374151;font-weight:500;box-shadow:0 4px 6px rgba(0,0,0,0.05);appearance:none;cursor:pointer;font-family:'Courier New', Courier, monospace;font-size:16px">
						<option value="all" {{ ($reorderStatus ?? 'all') === 'all' ? 'selected' : '' }}>📦 All Reorders</option>
						<option value="pending" {{ ($reorderStatus ?? '') === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
						<option value="approved" {{ ($reorderStatus ?? '') === 'approved' ? 'selected' : '' }}>✅ Approved</option>
						<option value="ordered" {{ ($reorderStatus ?? '') === 'ordered' ? 'selected' : '' }}>🚛 Ordered</option>
						<option value="received" {{ ($reorderStatus ?? '') === 'received' ? 'selected' : '' }}>📥 Received</option>
						<option value="cancelled" {{ ($reorderStatus ?? '') === 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
					</select>
					<svg style="position:absolute;right:12px;top:50%;transform:translateY(-50%);width:16px;height:16px;fill:#6b7280;pointer-events:none" viewBox="0 0 20 20">
						<path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
					</svg>
				</div>
			</div>
			
			<div style="display:flex;gap:10px">
				<button type="submit" class="btn" style="padding:12px 20px;background:rgba(255,255,255,0.95);color:#374151;border:none;border-radius:10px;font-weight:600;box-shadow:0 4px 6px rgba(0,0,0,0.05);transition:all 0.2s;cursor:pointer;display:flex;align-items:center;gap:6px;font-family:'Courier New', Courier, monospace" onmouseover="this.style.background='white';this.style.transform='translateY(-1px)'" onmouseout="this.style.background='rgba(255,255,255,0.95)';this.style.transform='translateY(0)'">
					<svg style="width:16px;height:16px;fill:currentColor" viewBox="0 0 20 20">
						<path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
					</svg>
					Apply
				</button>
				<a href="{{ route('staffmod.admin.stock.alerts') }}" class="btn" style="padding:12px 20px;background:rgba(255,255,255,0.2);color:white;border:2px solid rgba(255,255,255,0.3);border-radius:10px;font-weight:600;text-decoration:none;transition:all 0.2s;cursor:pointer;display:flex;align-items:center;gap:6px;font-family:'Courier New', Courier, monospace" onmouseover="this.style.background='rgba(255,255,255,0.3)';this.style.transform='translateY(-1px)'" onmouseout="this.style.background='rgba(255,255,255,0.2)';this.style.transform='translateY(0)'">
					<svg style="width:16px;height:16px;fill:currentColor" viewBox="0 0 20 20">
						<path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
					</svg>
					Clear
				</a>
			</div>
		</form>
		
		@if(($status ?? 'all') !== 'all' || ($severity ?? 'all') !== 'all' || ($reorderStatus ?? 'all') !== 'all')
			<div style="margin-top:16px;padding:12px 16px;background:rgba(255,255,255,0.1);border-radius:8px;color:rgba(255,255,255,0.9);font-size:14px;display:flex;align-items:center;gap:8px;font-family:'Courier New', Courier, monospace">
				<svg style="width:16px;height:16px;fill:currentColor" viewBox="0 0 20 20">
					<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
				</svg>
				<span>Filters applied - showing filtered results</span>
			</div>
		@endif
	</section>

	@if(($status ?? 'all') !== 'all' || ($severity ?? 'all') !== 'all' || ($reorderStatus ?? 'all') !== 'all')
		<!-- Active Filters Summary -->
		<section style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px;margin-bottom:16px;box-shadow:0 2px 4px rgba(0,0,0,0.02)">
			<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
				<div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
					<span style="font-weight:600;color:#374151;display:flex;align-items:center;gap:6px">
						<svg style="width:16px;height:16px;fill:#6366f1" viewBox="0 0 20 20">
							<path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/>
						</svg>
						Active Filters:
					</span>
					
					@if(($status ?? 'all') !== 'all')
						<span style="background:#eff6ff;color:#1d4ed8;padding:4px 8px;border-radius:6px;font-size:12px;font-weight:600">
							Status: {{ ucfirst($status) }}
						</span>
					@endif
					
					@if(($severity ?? 'all') !== 'all')
						<span style="background:#fef3c7;color:#d97706;padding:4px 8px;border-radius:6px;font-size:12px;font-weight:600">
							Severity: {{ ucfirst($severity) }}
						</span>
					@endif
					
					@if(($reorderStatus ?? 'all') !== 'all')
						<span style="background:#d1fae5;color:#065f46;padding:4px 8px;border-radius:6px;font-size:12px;font-weight:600">
							Reorder: {{ ucfirst($reorderStatus) }}
						</span>
					@endif
				</div>
				
				<a href="{{ route('staffmod.admin.stock.alerts') }}" style="background:#f3f4f6;color:#374151;padding:6px 12px;border-radius:6px;text-decoration:none;font-size:12px;font-weight:600;display:flex;align-items:center;gap:4px;transition:all 0.2s" onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
					<svg style="width:12px;height:12px;fill:currentColor" viewBox="0 0 20 20">
						<path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
					</svg>
					Clear All
				</a>
			</div>
		</section>
	@endif

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

	<!-- Stock Alerts Table -->
	<section style="background:#fff;border:1px solid var(--line);border-radius:14px;overflow:hidden">
		<div style="padding:16px;border-bottom:1px solid var(--line);background:#f8f9fa">
			<h3 style="margin:0">Stock Alert Monitor</h3>
		</div>
		<div style="overflow-x:auto">
			<table style="width:100%;border-collapse:collapse">
				<thead>
					<tr style="background:#f8f9fa">
						<th style="padding:12px;text-align:left;border-bottom:1px solid var(--line)">Product</th>
						<th style="padding:12px;text-align:left;border-bottom:1px solid var(--line)">Alert Type</th>
						<th style="padding:12px;text-align:left;border-bottom:1px solid var(--line)">Current Stock</th>
						<th style="padding:12px;text-align:left;border-bottom:1px solid var(--line)">Reorder Level</th>
						<th style="padding:12px;text-align:left;border-bottom:1px solid var(--line)">Date Created</th>
						<th style="padding:12px;text-align:left;border-bottom:1px solid var(--line)">Status</th>
						<th style="padding:12px;text-align:left;border-bottom:1px solid var(--line)">Actions</th>
					</tr>
				</thead>
				<tbody>
					@forelse($alerts ?? [] as $alert)
						<tr style="border-bottom:1px solid #f1f1f1">
							<td style="padding:12px">
								<div style="font-weight:600">{{ $alert->product->name ?? 'N/A' }}</div>
								<div class="muted" style="font-size:14px">SKU: {{ $alert->product->sku ?? 'N/A' }}</div>
							</td>
							<td style="padding:12px">
								@if($alert->alert_type === 'out_of_stock')
									<span style="background:#ef4444;color:white;padding:4px 8px;border-radius:4px;font-size:12px">
										Out of Stock
									</span>
								@elseif($alert->alert_type === 'low_stock')
									<span style="background:#f59e0b;color:white;padding:4px 8px;border-radius:4px;font-size:12px">
										Low Stock
									</span>
								@elseif($alert->alert_type === 'expired')
									<span style="background:#8b5cf6;color:white;padding:4px 8px;border-radius:4px;font-size:12px">
										Expired
									</span>
								@else
									<span style="background:#6b7280;color:white;padding:4px 8px;border-radius:4px;font-size:12px">
										{{ ucfirst($alert->alert_type) }}
									</span>
								@endif
							</td>
							<td style="padding:12px">
								<span style="color:#ef4444;font-weight:600">
									{{ $alert->current_quantity ?? 0 }} {{ $alert->product->unit ?? '' }}
								</span>
							</td>
							<td style="padding:12px">{{ $alert->reorder_level ?? 0 }}</td>
							<td style="padding:12px">{{ $alert->created_at ? $alert->created_at->format('M d, Y H:i') : 'N/A' }}</td>
							<td style="padding:12px">
								@if($alert->status === 'resolved')
									<span style="background:#10b981;color:white;padding:4px 8px;border-radius:4px;font-size:12px">Resolved</span>
								@elseif($alert->status === 'acknowledged')
									<span style="background:#f59e0b;color:white;padding:4px 8px;border-radius:4px;font-size:12px">Acknowledged</span>
								@else
									<span style="background:#ef4444;color:white;padding:4px 8px;border-radius:4px;font-size:12px">Active</span>
								@endif
							</td>
							<td style="padding:12px">
								<div style="display:flex;gap:8px;flex-wrap:wrap">
									@if($alert->status === 'active')
										<form method="POST" action="{{ route('staffmod.admin.stock.acknowledge', $alert->id) }}" style="display:inline">
											@csrf
											<button type="submit" class="btn btn--line" style="padding:6px 12px;font-size:12px">
												Acknowledge
											</button>
										</form>
									@endif
									@if($alert->status !== 'resolved')
										<form method="POST" action="{{ route('staffmod.admin.stock.resolve', $alert->id) }}" style="display:inline">
											@csrf
											<button type="submit" class="btn btn--gold" style="padding:6px 12px;font-size:12px">
												Resolve
											</button>
										</form>
										@if(in_array($alert->product_id, $existingReorders ?? []))
											<span class="btn" style="padding:6px 12px;font-size:12px;background:#10b981;color:white;border:1px solid #10b981;cursor:default">
												✓ Reorder Exists
											</span>
										@else
											<form method="POST" action="{{ route('staffmod.admin.stock.create-reorder', $alert->id) }}" style="display:inline">
												@csrf
												<button type="submit" class="btn" style="padding:6px 12px;font-size:12px;background:#8b5cf6;color:white;border:1px solid #8b5cf6">
													Create Reorder
												</button>
											</form>
										@endif
									@endif
								</div>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="7" style="padding:32px;text-align:center;color:var(--muted)">
								No stock alerts found. Great! Your inventory levels are healthy.
							</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
		
		@if(isset($alerts) && method_exists($alerts, 'hasPages') && $alerts->hasPages())
			<div style="padding:16px;border-top:1px solid var(--line)">
				{{ $alerts->appends(request()->query())->links('pagination::custom') }}
			</div>
		@endif
	</section>

	<!-- Reorder Requests Section -->
	<section style="background:#fff;border:1px solid var(--line);border-radius:14px;overflow:hidden;margin-top:32px">
		<div style="padding:16px;border-bottom:1px solid var(--line);background:#f8f9fa">
			<h3 style="margin:0">Recent Reorder Requests ({{ isset($reorderRequests) ? $reorderRequests->total() : 0 }} total)</h3>
		</div>
		<div style="overflow-x:auto">
			<table style="width:100%;border-collapse:collapse">
				<thead>
					<tr style="background:#f8f9fa">
						<th style="padding:12px;text-align:left;border-bottom:1px solid var(--line)">Product</th>
						<th style="padding:12px;text-align:left;border-bottom:1px solid var(--line)">Current Stock</th>
						<th style="padding:12px;text-align:left;border-bottom:1px solid var(--line)">Suggested Qty</th>
						<th style="padding:12px;text-align:left;border-bottom:1px solid var(--line)">Priority</th>
						<th style="padding:12px;text-align:left;border-bottom:1px solid var(--line)">Status</th>
						<th style="padding:12px;text-align:left;border-bottom:1px solid var(--line)">Estimated Cost</th>
						<th style="padding:12px;text-align:left;border-bottom:1px solid var(--line)">Date Requested</th>
						<th style="padding:12px;text-align:left;border-bottom:1px solid var(--line)">Notes</th>
						<th style="padding:12px;text-align:left;border-bottom:1px solid var(--line)">Actions</th>
					</tr>
				</thead>
				<tbody>
					@forelse($reorderRequests ?? [] as $request)
						<tr style="border-bottom:1px solid #f1f1f1">
							<td style="padding:12px">
								<div style="font-weight:600">{{ $request->product->name ?? 'N/A' }}</div>
								<div class="muted" style="font-size:14px">SKU: {{ $request->product->sku ?? 'N/A' }}</div>
								<div class="muted" style="font-size:12px">ID: {{ $request->id }}</div>
							</td>
							<td style="padding:12px">
								<span style="color:#ef4444;font-weight:600">
									{{ $request->current_quantity ?? 0 }} {{ $request->product->unit ?? '' }}
								</span>
							</td>
							<td style="padding:12px">
								<span style="color:#10b981;font-weight:600">
									{{ $request->suggested_quantity ?? 0 }} {{ $request->product->unit ?? '' }}
								</span>
							</td>
							<td style="padding:12px">
								@if($request->priority === 'urgent')
									<span style="background:#ef4444;color:white;padding:4px 8px;border-radius:4px;font-size:12px">
										Urgent
									</span>
								@elseif($request->priority === 'high')
									<span style="background:#f59e0b;color:white;padding:4px 8px;border-radius:4px;font-size:12px">
										High
									</span>
								@elseif($request->priority === 'medium')
									<span style="background:#3b82f6;color:white;padding:4px 8px;border-radius:4px;font-size:12px">
										Medium
									</span>
								@else
									<span style="background:#6b7280;color:white;padding:4px 8px;border-radius:4px;font-size:12px">
										Low
									</span>
								@endif
							</td>
							<td style="padding:12px">
								@if($request->status === 'pending')
									<span style="background:#f59e0b;color:white;padding:4px 8px;border-radius:4px;font-size:12px">Pending</span>
								@elseif($request->status === 'approved')
									<span style="background:#10b981;color:white;padding:4px 8px;border-radius:4px;font-size:12px">Approved</span>
								@elseif($request->status === 'ordered')
									<span style="background:#3b82f6;color:white;padding:4px 8px;border-radius:4px;font-size:12px">Ordered</span>
								@elseif($request->status === 'received')
									<span style="background:#059669;color:white;padding:4px 8px;border-radius:4px;font-size:12px">Received</span>
								@elseif($request->status === 'cancelled')
									<span style="background:#ef4444;color:white;padding:4px 8px;border-radius:4px;font-size:12px">Cancelled</span>
								@else
									<span style="background:#6b7280;color:white;padding:4px 8px;border-radius:4px;font-size:12px">{{ ucfirst($request->status) }}</span>
								@endif
							</td>
							<td style="padding:12px">
								<span style="font-weight:600">
									${{ number_format($request->estimated_cost ?? 0, 2) }}
								</span>
							</td>
							<td style="padding:12px">{{ $request->created_at ? $request->created_at->format('M d, Y') : 'N/A' }}</td>
							<td style="padding:12px">
								<div style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $request->notes }}">
									{{ Str::limit($request->notes ?? 'No notes', 30) }}
								</div>
							</td>
							<td style="padding:12px">
								<div style="display:flex;gap:8px;align-items:center">
									@if($request->status === 'pending')
										<form method="POST" action="{{ route('staffmod.admin.reorder.approve', $request->id) }}" style="display:inline">
											@csrf
											<button type="submit" 
												style="background:#10b981;color:white;border:none;padding:6px 12px;border-radius:4px;font-family:'Courier New',monospace;font-size:12px;cursor:pointer;transition:all 0.2s" 
												onclick="return confirm('Approve this reorder request? This will update the product stock.')"
												onmouseover="this.style.background='#059669'" 
												onmouseout="this.style.background='#10b981'">
												Approve
											</button>
										</form>
										<form method="POST" action="{{ route('staffmod.admin.reorder.cancel', $request->id) }}" style="display:inline">
											@csrf
											<button type="submit" 
												style="background:#ef4444;color:white;border:none;padding:6px 12px;border-radius:4px;font-family:'Courier New',monospace;font-size:12px;cursor:pointer;transition:all 0.2s" 
												onclick="return confirm('Cancel this reorder request? This action cannot be undone.')"
												onmouseover="this.style.background='#dc2626'" 
												onmouseout="this.style.background='#ef4444'">
												Cancel
											</button>
										</form>
									@elseif($request->status === 'approved')
										<span style="color:#10b981;font-family:'Courier New',monospace;font-size:12px">✓ Approved</span>
									@elseif($request->status === 'cancelled')
										<span style="color:#ef4444;font-family:'Courier New',monospace;font-size:12px">✗ Cancelled</span>
									@else
										<span style="color:#6b7280;font-family:'Courier New',monospace;font-size:12px">{{ ucfirst($request->status) }}</span>
									@endif
								</div>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="9" style="padding:32px;text-align:center;color:var(--muted)">
								No reorder requests found. Create reorder requests from stock alerts above.
							</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
		
		@if(isset($reorderRequests) && method_exists($reorderRequests, 'hasPages') && $reorderRequests->hasPages())
			<div style="padding:16px;border-top:1px solid var(--line)">
				{{ $reorderRequests->appends(request()->query())->links('pagination::custom') }}
			</div>
		@endif
	</section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when select values change
    const selects = document.querySelectorAll('select[name="status"], select[name="severity"], select[name="reorder_status"]');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            // Add loading state
            const form = this.closest('form');
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<svg style="width:16px;height:16px;fill:currentColor;animation:spin 1s linear infinite" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/></svg> Applying...';
                submitBtn.disabled = true;
            }
            
            // Submit form after short delay for better UX
            setTimeout(() => {
                form.submit();
            }, 300);
        });
    });
    
    // Add hover effects for custom selects
    const selectContainers = document.querySelectorAll('div[style*="position:relative"]');
    selectContainers.forEach(container => {
        const select = container.querySelector('select');
        if (select) {
            select.addEventListener('focus', function() {
                this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
                this.style.transform = 'translateY(-2px)';
            });
            
            select.addEventListener('blur', function() {
                this.style.boxShadow = '0 4px 6px rgba(0,0,0,0.05)';
                this.style.transform = 'translateY(0)';
            });
        }
    });
});
</script>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Custom select styling improvements */
select:focus {
    outline: none !important;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    transform: translateY(-2px) !important;
    transition: all 0.2s ease !important;
}

/* Grid responsiveness */
@media (max-width: 768px) {
    form[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
    }
}

@media (max-width: 480px) {
    section[style*="linear-gradient"] {
        padding: 16px !important;
    }
    
    div[style*="display:flex;gap:10px"] {
        flex-direction: column !important;
        gap: 8px !important;
    }
    
    button, a {
        width: 100% !important;
        justify-content: center !important;
    }
}
</style>
@endpush
