@extends('layouts.app')

@section('title', 'My Appointments')

@section('content')
<div class="wrap" style="max-width:1000px;margin:0 auto;padding:32px 32px 60px">
	<header style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px">
		<h2 style="margin:0">Appointments</h2>
		<a class="btn btn--gold" href="{{ route('patient.appointments.create') }}">Request Appointment</a>
	</header>

	@if(session('success'))
		<div class="alert alert--success">{{ session('success') }}</div>
	@endif

	<section class="grid">
		<article class="card">
			<h3 class="card__title">Upcoming</h3>
			@if($upcoming->isEmpty())
				<p class="muted">No upcoming appointments.</p>
			@else
				<ul class="list">
					@foreach($upcoming as $a)
					<li class="item">
						<div>
							<strong>{{ $a->scheduled_at->format('D, M j, Y g:i A') }}</strong>
							<div class="muted">{{ $a->doctor_name ?? 'Any doctor' }} @ {{ $a->location ?? 'Clinic' }}</div>
						</div>
						<span class="badge">{{ ucfirst($a->status) }}</span>
					</li>
					@endforeach
				</ul>
			@endif
		</article>

		<article class="card">
			<h3 class="card__title">History</h3>
			@if($past->isEmpty())
				<p class="muted">No past appointments.</p>
			@else
				<ul class="list">
					@foreach($past as $a)
					<li class="item">
						<div>
							<strong>{{ $a->scheduled_at->format('D, M j, Y g:i A') }}</strong>
							<div class="muted">{{ $a->doctor_name ?? 'Any doctor' }} @ {{ $a->location ?? 'Clinic' }}</div>
						</div>
						<span class="badge">{{ ucfirst($a->status) }}</span>
					</li>
					@endforeach
				</ul>
			@endif
		</article>
	</section>
</div>

<style>
	:root{--paper:#fbfaf7;--ink:#1c1c1c;--muted:#6b6b6b;--line:rgba(0,0,0,.08);--gold:#c7a76b;--gold-2:#a68957}
	html,body{background:var(--paper);color:var(--ink);font-family:"Courier New", Courier, monospace}
	.grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
	.card{padding:18px;border:1px solid var(--line);border-radius:16px;background:#fff;box-shadow:0 8px 24px rgba(0,0,0,.04)}
	.card__title{margin:0 0 8px}
	.list{list-style:none;margin:0;padding:0;display:grid;gap:10px}
	.item{display:flex;align-items:center;justify-content:space-between;border:1px solid var(--line);border-radius:12px;padding:10px 12px}
	.badge{border-radius:999px;padding:4px 10px;background:#eee;font-size:12px}
	.muted{color:var(--muted)}
	.btn{appearance:none;border:none;cursor:pointer;font-weight:800;border-radius:999px;padding:10px 18px}
	.btn--gold{background:linear-gradient(180deg, var(--gold), var(--gold-2));color:#141414}
	.alert{border:1px solid var(--line);border-left-width:4px;border-radius:12px;padding:12px 14px;margin:12px 0;background:#fff}
	.alert--success{border-left-color:#16a34a}
	@media(max-width:980px){.grid{grid-template-columns:1fr}}
</style>
@endsection
