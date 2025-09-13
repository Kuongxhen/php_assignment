@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="wrap narrow" style="padding:32px 32px 60px">
	<header class="auth__head"><h2 class="title">Edit Profile</h2></header>
	@if(session('error'))
		<div class="alert alert--error">{{ session('error') }}</div>
	@endif
	@if($errors->any())
		<div class="alert alert--error">
			<strong>We found some issues:</strong>
			<ul style="margin:8px 0 0;padding-left:16px">
				@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
			</ul>
		</div>
	@endif
	<form method="POST" action="{{ route('patient.profile.update') }}" class="form">
		@csrf
		<div class="grid2">
			<label class="field"><span>Name</span><input name="name" value="{{ old('name',$patient->name) }}" required></label>
			<label class="field"><span>Phone</span><input name="phone_number" value="{{ old('phone_number',$patient->phone_number) }}" required></label>
			<label class="field"><span>Email</span><input type="email" name="email" value="{{ old('email',$patient->email) }}"></label>
			<label class="field" style="grid-column:1 / -1"><span>Address</span><input name="address" value="{{ old('address',$patient->address) }}"></label>
			<label class="field"><span>Blood Type</span><input name="blood_type" value="{{ old('blood_type',$patient->blood_type) }}"></label>
			<label class="field"><span>Allergies</span><input name="allergies" value="{{ old('allergies',$patient->allergies) }}"></label>
			<label class="field"><span>Current Medications</span><input name="current_medications" value="{{ old('current_medications',$patient->current_medications) }}"></label>
			<label class="field"><span>Chronic Conditions</span><input name="chronic_conditions" value="{{ old('chronic_conditions',$patient->chronic_conditions) }}"></label>
			<label class="field"><span>Emergency Contact Name</span><input name="emergency_contact_name" value="{{ old('emergency_contact_name',$patient->emergency_contact_name) }}"></label>
			<label class="field"><span>Emergency Contact Phone</span><input name="emergency_contact_phone" value="{{ old('emergency_contact_phone',$patient->emergency_contact_phone) }}"></label>
			<label class="field"><span>Relationship</span><input name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship',$patient->emergency_contact_relationship) }}"></label>
		</div>
		<div class="form__row" style="justify-content:flex-end;gap:10px">
			<a class="btn btn--line" href="{{ route('patient.profile.show') }}">Cancel</a>
			<button class="btn btn--gold" type="submit">Save Changes</button>
		</div>
	</form>
</div>

<style>
	:root{--paper:#fbfaf7;--ink:#1c1c1c;--muted:#6b6b6b;--line:rgba(0,0,0,.08);--gold:#c7a76b;--gold-2:#a68957}
	html,body{background:var(--paper);color:var(--ink);font-family:"Courier New", Courier, monospace}
	.title{font-size:28px}
	.alert{border:1px solid var(--line);border-left-width:4px;border-radius:12px;padding:12px 14px;margin:12px 0;background:#fff}
	.alert--error{border-left-color:#ef4444}
	.form{display:grid;gap:16px;margin-top:12px}
	.grid2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
	.field{display:grid;gap:8px}
	.field span{font-size:13px;color:#6a6a6a;letter-spacing:.06em;text-transform:uppercase}
	.field input{height:48px;border-radius:12px;border:1px solid var(--line);background:#fff;color:#111;padding:0 12px}
	.field input:focus{outline:none;border-color:rgba(198,167,107,.5)}
	.btn{appearance:none;border:none;cursor:pointer;font-weight:800;border-radius:999px;padding:12px 20px}
	.btn--line{background:transparent;color:var(--ink);border:1px solid var(--line)}
	.btn--gold{background:linear-gradient(180deg,var(--gold),var(--gold-2));color:#141414}
	.form__row{display:flex;align-items:center}
	@media(max-width:980px){.grid2{grid-template-columns:1fr}}
</style>
@endsection
