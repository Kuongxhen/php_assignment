@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="wrap" style="max-width:900px;margin:0 auto;padding:32px 32px 60px">
	<header style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px">
		<h2 style="margin:0">My Profile</h2>
		<a href="{{ route('patient.dashboard') }}" style="text-decoration:none;color:#444">Back to dashboard</a>
	</header>

	@if(session('success'))
		<div class="alert alert--success">{{ session('success') }}</div>
	@endif
	@if(session('error'))
		<div class="alert alert--error">{{ session('error') }}</div>
	@endif

	@php $hasProfile = (bool) $patient; @endphp

	@if(!$hasProfile)
		<div class="card">
			<p>You haven't created your patient profile yet.</p>
			<div style="margin-top:12px"><a class="btn btn--gold" href="{{ route('patient.profile.create') }}">Create Profile</a></div>
		</div>
	@else
		<article class="card">
			<div class="rows">
				<div class="row"><span class="label">Name</span><span>{{ $patient->name }}</span></div>
				<div class="row"><span class="label">IC Number</span><span>{{ $patient->ic_number }}</span></div>
				<div class="row"><span class="label">Gender</span><span>{{ ucfirst($patient->gender) }}</span></div>
				<div class="row"><span class="label">Date of Birth</span><span>{{ optional($patient->date_of_birth)->format('Y-m-d') }}</span></div>
				<div class="row"><span class="label">Phone</span><span>{{ $patient->phone_number }}</span></div>
				<div class="row"><span class="label">Email</span><span>{{ $patient->email ?? '—' }}</span></div>
				<div class="row"><span class="label">Address</span><span>{{ $patient->address }}</span></div>
				<div class="row"><span class="label">Blood Type</span><span>{{ $patient->blood_type ?? '—' }}</span></div>
				<div class="row"><span class="label">Allergies</span><span>{{ $patient->allergies ?? '—' }}</span></div>
				<div class="row"><span class="label">Current Medications</span><span>{{ $patient->current_medications ?? '—' }}</span></div>
				<div class="row"><span class="label">Chronic Conditions</span><span>{{ $patient->chronic_conditions ?? '—' }}</span></div>
				<div class="row"><span class="label">Status</span><span style="text-transform:capitalize">{{ $patient->status }}</span></div>
			</div>
			@if($patient->appointments && $patient->appointments->count())
				<hr style="border:none;border-top:1px solid var(--line);margin:12px 0">
				<h3 style="margin:8px 0 4px">Upcoming Appointments</h3>
				<ul style="margin:0;padding-left:18px">
					@foreach($patient->appointments->where('status','!=','cancelled')->sortBy('scheduled_at')->take(3) as $appt)
						<li>{{ optional($appt->scheduled_at)->format('Y-m-d H:i') }} — {{ $appt->reason ?? 'Consultation' }} @if($appt->location) ({{ $appt->location }}) @endif</li>
					@endforeach
				</ul>
			@endif
			<div class="actions" style="display:flex;gap:10px;justify-content:flex-end;margin-top:16px">
				<a class="btn btn--line" href="{{ route('patient.profile.edit') }}">Edit</a>
                @if($patient->status === 'active')
                <form method="POST" action="{{ route('patient.profile.deactivate') }}" onsubmit="return confirm('Set your account status to inactive? You can contact the clinic to reactivate.');" style="margin:0">
                    @csrf
                    <button type="submit" class="btn btn--line">Deactivate</button>
                </form>
                @endif
				<form method="POST" action="{{ route('patient.profile.destroy') }}" onsubmit="return confirm('Delete your profile? This cannot be undone.');">
					@csrf
					<button type="submit" class="btn btn--danger">Delete</button>
				</form>
			</div>
		</article>
	@endif
</div>

<style>
	:root{--paper:#fbfaf7;--ink:#1c1c1c;--muted:#6b6b6b;--line:rgba(0,0,0,.08);--gold:#c7a76b;--gold-2:#a68957}
	html,body{background:var(--paper);color:var(--ink);font-family:"Courier New", Courier, monospace}
	.card{padding:18px;border:1px solid var(--line);border-radius:16px;background:#fff;box-shadow:0 8px 24px rgba(0,0,0,.04)}
	.rows{display:grid;gap:8px}
	.row{display:flex;justify-content:space-between;gap:12px}
	.label{color:var(--muted)}
	.btn{appearance:none;border:none;cursor:pointer;font-weight:800;border-radius:999px;padding:10px 18px}
	.btn--line{background:transparent;color:var(--ink);border:1px solid var(--line)}
	.btn--gold{background:linear-gradient(180deg, var(--gold), var(--gold-2));color:#141414}
	.btn--danger{background:#ef4444;color:#fff}
	.alert{border:1px solid var(--line);border-left-width:4px;border-radius:12px;padding:12px 14px;margin:12px 0;background:#fff}
	.alert--success{border-left-color:#16a34a}
	.alert--error{border-left-color:#ef4444}
</style>
@endsection
