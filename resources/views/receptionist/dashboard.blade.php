@extends('layouts.app')

@section('title', 'Receptionist Dashboard')

@section('content')
<div class="wrap" style="max-width:1100px;margin:0 auto;padding:32px 32px 60px">
	<header style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px">
		<h2 style="margin:0">Receptionist Dashboard</h2>
		<nav style="display:flex;gap:12px">
			<a class="link" href="{{ route('staffmod.receptionist.appointments') }}">Manage Appointments</a>
			<a class="link" href="{{ route('staffmod.receptionist.inbox') }}">Messages</a>
		</nav>
	</header>

	<section style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px">
		<div class="card" style="padding:16px;border:1px solid var(--line);border-radius:14px;background:#fff"><div class="muted">Doctors</div><div style="font-size:28px;font-weight:800">{{ $totalDoctors ?? 0 }}</div></div>
		<div class="card" style="padding:16px;border:1px solid var(--line);border-radius:14px;background:#fff"><div class="muted">Today Appointments</div><div style="font-size:28px;font-weight:800">{{ $todayAppointments ?? 0 }}</div></div>
	</section>
</div>
@endsection


