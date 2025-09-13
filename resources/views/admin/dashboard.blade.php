@extends('layouts.app')

@section('title', 'Staff Admin')

@section('content')
<div class="wrap" style="max-width:1100px;margin:0 auto;padding:32px 32px 60px">
	<header style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px">
		<h2 style="margin:0">Staff Admin</h2>
		<nav style="display:flex;gap:12px">
			<a class="link" href="{{ route('staffmod.admin.createStaff') }}">Create Staff</a>
			<a class="link" href="{{ route('staffmod.admin.staffList') }}">Manage Staff</a>
			<a class="link" href="{{ route('staffmod.admin.products') }}">Manage Products</a>
			<a class="link" href="{{ route('staffmod.admin.stock.alerts') }}">Stock Alerts</a>
		</nav>
	</header>

	<section style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:16px">
		<div class="card" style="padding:16px;border:1px solid var(--line);border-radius:14px;background:#fff"><div class="muted">Total Staff</div><div style="font-size:28px;font-weight:800">{{ $totalStaff ?? 0 }}</div></div>
		<div class="card" style="padding:16px;border:1px solid var(--line);border-radius:14px;background:#fff"><div class="muted">Doctors</div><div style="font-size:28px;font-weight:800">{{ $totalDoctors ?? 0 }}</div></div>
		<div class="card" style="padding:16px;border:1px solid var(--line);border-radius:14px;background:#fff"><div class="muted">Receptionists</div><div style="font-size:28px;font-weight:800">{{ $totalReceptionists ?? 0 }}</div></div>
		<div class="card" style="padding:16px;border:1px solid var(--line);border-radius:14px;background:#fff"><div class="muted">Admins</div><div style="font-size:28px;font-weight:800">{{ $totalAdmins ?? 0 }}</div></div>
	</section>

	<section class="rows" style="display:grid;gap:12px">
		<div class="row" style="display:flex;justify-content:space-between;gap:12px">
			<a class="btn btn--gold" href="{{ route('staffmod.admin.createStaff') }}">Create Staff</a>
			<a class="btn btn--line" href="{{ route('staffmod.admin.staffList') }}">View All Staff</a>
		</div>
		<div class="row" style="display:flex;justify-content:space-between;gap:12px">
			<a class="btn btn--gold" href="{{ route('staffmod.admin.products') }}">Manage Products</a>
			<a class="btn btn--line" href="{{ route('staffmod.admin.stock.alerts') }}">Stock Alerts</a>
		</div>
	</section>
</div>
@endsection


