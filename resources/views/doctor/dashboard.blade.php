@extends('layouts.app')

@section('title', 'Doctor Dashboard')

@section('content')
<div class="wrap" style="max-width:1100px;margin:0 auto;padding:32px 32px 60px">
	<header style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px">
		<h2 style="margin:0">Doctor Dashboard</h2>
		<nav style="display:flex;gap:12px">
			<a class="link" href="{{ route('staffmod.doctor.schedule') }}">Manage Schedule</a>
		</nav>
	</header>

	<section style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px">
		<div class="card" style="padding:16px;border:1px solid var(--line);border-radius:14px;background:#fff"><div class="muted">Today</div><div style="font-size:28px;font-weight:800">{{ $todayAppointments ?? 0 }}</div></div>
		<div class="card" style="padding:16px;border:1px solid var(--line);border-radius:14px;background:#fff"><div class="muted">Available Slots</div><div style="font-size:28px;font-weight:800">{{ $availableSlots ?? 0 }}</div></div>
		<div class="card" style="padding:16px;border:1px solid var(--line);border-radius:14px;background:#fff"><div class="muted">Patients</div><div style="font-size:28px;font-weight:800">{{ $totalPatients ?? 0 }}</div></div>
		<div class="card" style="padding:16px;border:1px solid var(--line);border-radius:14px;background:#fff"><div class="muted">Satisfaction</div><div style="font-size:28px;font-weight:800">{{ $satisfactionRate ?? 0 }}%</div></div>
	</section>
</div>
@endsection


