@extends('layouts.app')

@section('title', 'Patient Dashboard')

@section('content')
<div class="wrap" style="max-width:1100px;margin:0 auto;padding:32px 32px 80px">
	<header class="dash__head">
		<h1 class="display">Welcome{{ isset($patient) && $patient ? ", " . $patient->name : '' }}</h1>
		<p class="muted">Manage your details and keep track of appointments.</p>
	</header>

	@if(session('success'))
		<div class="alert alert--success">{{ session('success') }}</div>
	@endif
	@if(session('error'))
		<div class="alert alert--error">{{ session('error') }}</div>
	@endif
	@if($errors->any())
		<div class="alert alert--error">
			<strong>We found some issues:</strong>
			<ul style="margin:8px 0 0;padding-left:16px">
				@foreach($errors->all() as $e)
					<li>{{ $e }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	<section class="grid">
		<article class="card">
			<h3 class="card__title">Profile</h3>
			@if(isset($patient) && $patient)
				<div class="rows">
					<div class="row"><span class="label">IC</span><span>{{ $patient->ic_number ?? '—' }}</span></div>
					<div class="row"><span class="label">Phone</span><span>{{ $patient->phone_number ?? '—' }}</span></div>
					<div class="row"><span class="label">Email</span><span>{{ $patient->email ?? '—' }}</span></div>
					<div class="row"><span class="label">Address</span><span>{{ $patient->address ?? '—' }}</span></div>
				</div>
				<div class="actions">
					<a class="btn btn--line" href="{{ route('patient.profile.show') }}">View Profile</a>
				</div>
			@else
				<p class="muted" style="margin:0 0 12px">You have not created your patient profile yet.</p>
				<div class="actions">
					<a class="btn btn--gold" href="{{ route('patient.profile.create') }}">Create Patient Profile</a>
				</div>
			@endif
		</article>

		<article class="card">
			<h3 class="card__title">Appointments</h3>
			@if(isset($patient) && $patient)
				@php
					$upcoming = \App\Models\Appointment::where('patient_id',$patient->id)->whereDate('scheduled_at','>=', now()->startOfDay())->count();
					$past = \App\Models\Appointment::where('patient_id',$patient->id)->whereDate('scheduled_at','<', now()->startOfDay())->count();
				@endphp
				<div class="stats">
					<div class="stat"><span class="kpi">{{ $upcoming }}</span><span class="k">Upcoming</span></div>
					<div class="stat"><span class="kpi">{{ $past }}</span><span class="k">History</span></div>
				</div>
				<div class="actions">
					<a class="btn btn--gold" href="{{ route('patient.appointments.index') }}">View Appointments</a>
				</div>
			@else
				<p class="muted">Create your profile to request appointments.</p>
				<div class="actions"><a class="btn btn--line" href="{{ route('patient.profile.create') }}">Create Profile</a></div>
			@endif
		</article>

		<article class="card">
			<h3 class="card__title">Quick Update</h3>
			@if(isset($patient) && $patient)
				<form method="POST" action="{{ route('patient.profile.update') }}" class="form">
					@csrf
					<div class="grid2">
						<label class="field">
							<span>Name</span>
							<input name="name" value="{{ old('name',$patient->name) }}" required />
						</label>
						<label class="field">
							<span>Phone</span>
							<input name="phone_number" value="{{ old('phone_number',$patient->phone_number) }}" required />
						</label>
						<label class="field">
							<span>Email</span>
							<input name="email" type="email" value="{{ old('email',$patient->email) }}" />
						</label>
						<label class="field">
							<span>Address</span>
							<input name="address" value="{{ old('address',$patient->address) }}" />
						</label>
					</div>
					<div style="display:flex;gap:10px;justify-content:flex-end">
						<button type="submit" class="btn btn--line">Save</button>
					</div>
				</form>
			@else
				<p class="muted">Quick updates will be available after you create your profile.</p>
			@endif
		</article>
	</section>
</div>

<style>
	:root{--paper:#fbfaf7;--ink:#1c1c1c;--muted:#6b6b6b;--line:rgba(0,0,0,.08);--gold:#c7a76b;--gold-2:#a68957}
	html,body{background:var(--paper);color:var(--ink);font-family:"Courier New", Courier, monospace}
	.display{margin:0 0 4px;font-size:28px}
	.muted{color:var(--muted);margin:0 0 18px}
	.alert{border:1px solid var(--line);border-left-width:4px;border-radius:12px;padding:12px 14px;margin:12px 0;background:#fff}
	.alert--success{border-left-color:#16a34a}
	.alert--error{border-left-color:#ef4444}
	.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}
	.card{padding:18px;border:1px solid var(--line);border-radius:16px;background:#fff;box-shadow:0 8px 24px rgba(0,0,0,.04);display:flex;flex-direction:column;gap:12px}
	.card__title{margin:0 0 4px;font-size:16px}
	.rows{display:grid;gap:8px}
	.row{display:flex;justify-content:space-between;gap:12px}
	.label{color:var(--muted)}
	.actions{margin-top:auto}
	.stats{display:flex;gap:18px}
	.stat{display:flex;flex-direction:column;gap:2px}
	.kpi{font-weight:800}
	.k{color:var(--muted);font-size:12px}
	.form{display:grid;gap:12px}
	.grid2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
	.field{display:grid;gap:6px}
	.field span{font-size:12px;color:#6a6a6a;letter-spacing:.06em;text-transform:uppercase}
	.field input{height:44px;border-radius:12px;border:1px solid var(--line);background:#fff;color:#111;padding:0 12px}
	.field input:focus{outline:none;border-color:rgba(198,167,107,.5)}
	.btn{appearance:none;border:none;cursor:pointer;font-weight:800;border-radius:999px;padding:10px 18px}
	.btn--line{background:transparent;color:var(--ink);border:1px solid var(--line)}
	.btn--gold{background:linear-gradient(180deg, var(--gold), var(--gold-2));color:#141414;box-shadow:0 16px 42px rgba(198,167,107,.18)}
	.btn--gold:hover{transform:translateY(-1px);box-shadow:0 22px 56px rgba(198,167,107,.26)}
	@media(max-width: 980px){.grid{grid-template-columns:1fr}.grid2{grid-template-columns:1fr}}
</style>
@endsection