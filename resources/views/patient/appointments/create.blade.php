@extends('layouts.app')

@section('title', 'Request Appointment')

@section('content')
<div class="wrap narrow" style="padding:32px 32px 60px">
	<header class="auth__head"><h2 class="title" style="margin:0">Request Appointment</h2></header>
	@if($errors->any())
		<div class="alert alert--error">
			<strong>We found some issues:</strong>
			<ul style="margin:8px 0 0;padding-left:16px">
				@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
			</ul>
		</div>
	@endif
	<form method="POST" action="{{ route('patient.appointments.store') }}" class="form">
		@csrf
		<div class="grid2">
			<label class="field"><span>Date & Time</span><input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" required></label>
			<label class="field"><span>Reason</span><input name="reason" value="{{ old('reason') }}"></label>
			<label class="field" style="grid-column:1 / -1"><span>Symptoms</span><input name="symptoms" value="{{ old('symptoms') }}" placeholder="e.g. fever, cough"></label>
			<label class="field"><span>Urgency</span>
				<select name="urgency">
					<option value="normal" @selected(old('urgency')==='normal')>Normal</option>
					<option value="urgent" @selected(old('urgency')==='urgent')>Urgent</option>
					<option value="follow-up" @selected(old('urgency')==='follow-up')>Follow-up</option>
				</select>
			</label>
			<label class="field"><span>Preferred Time Window</span><input name="preferred_window" value="{{ old('preferred_window') }}" placeholder="e.g. Morning"></label>
			<label class="field" style="grid-column:1 / -1"><span>Notes</span><input name="notes" value="{{ old('notes') }}"></label>
		</div>
		<div class="form__row" style="justify-content:flex-end;gap:10px">
			<a class="btn btn--line" href="{{ route('patient.appointments.index') }}">Cancel</a>
			<button class="btn btn--gold" type="submit">Submit Request</button>
		</div>
	</form>
</div>

<style>
	:root{--paper:#fbfaf7;--ink:#1c1c1c;--muted:#6b6b6b;--line:rgba(0,0,0,.08);--gold:#c7a76b;--gold-2:#a68957}
	html,body{background:var(--paper);color:var(--ink);font-family:"Courier New", Courier, monospace}
	.title{font-size:28px}
	.alert{border:1px solid var(--line);border-left-width:4px;border-radius:12px;padding:12px 14px;margin:12px 0;background:#fff}
	.alert--error{border-left-color:#ef4444}
	.form{display:grid;gap:16px}
	.grid2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
	.field{display:grid;gap:8px}
	.field span{font-size:13px;color:#6a6a6a;letter-spacing:.06em;text-transform:uppercase}
	.field input, .field select{height:48px;border-radius:12px;border:1px solid var(--line);background:#fff;color:#111;padding:0 12px}
	.field input:focus, .field select:focus{outline:none;border-color:rgba(198,167,107,.5)}
	.btn{appearance:none;border:none;cursor:pointer;font-weight:800;border-radius:999px;padding:12px 20px}
	.btn--line{background:transparent;color:var(--ink);border:1px solid var(--line)}
	.btn--gold{background:linear-gradient(180deg,var(--gold),var(--gold-2));color:#141414}
	.form__row{display:flex;align-items:center}
	@media(max-width:980px){.grid2{grid-template-columns:1fr}}
</style>
@endsection
