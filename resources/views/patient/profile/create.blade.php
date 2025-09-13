@extends('layouts.app')

@section('title', 'Create Patient Profile')

@section('content')
<div class="wrap narrow" style="padding:32px 32px 60px">
	<div class="auth">
		<header class="auth__head"><h2 class="title">Create Patient Profile</h2><p class="muted">Fill in your basic information to get started.</p></header>
		@if($errors->any())
			<div class="alert alert--error" id="error-summary">
				<strong>We found some issues:</strong>
				<ul style="margin:8px 0 0;padding-left:16px">
					@foreach($errors->getMessages() as $field => $messages)
						@foreach($messages as $msg)
							<li><a href="#" data-jump="{{ $field }}">{{ $msg }}</a></li>
						@endforeach
					@endforeach
				</ul>
			</div>
		@endif
		<div class="meter" aria-hidden="false" aria-label="Profile completeness">
			<div class="meter__bar" id="complete-bar" style="width:0%"></div>
			<div class="meter__label"><span id="complete-text">0%</span> complete</div>
		</div>
		<form method="POST" action="{{ route('patient.profile.store') }}" class="form" id="patient-create-form" novalidate>
			@csrf
			<div class="grid2">
				<label class="field"><span>Name <b class="req" aria-hidden="true">*</b></span><input id="name" name="name" value="{{ old('name') }}" required autocomplete="name" aria-describedby="hint-name" aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"><small class="hint" id="hint-name">Your full legal name.</small></label>
				<label class="field"><span>IC Number <b class="req" aria-hidden="true">*</b></span><input id="ic_number" name="ic_number" value="{{ old('ic_number') }}" required inputmode="numeric" pattern="[A-Za-z0-9\-]{6,20}" maxlength="20" aria-describedby="hint-ic" aria-invalid="{{ $errors->has('ic_number') ? 'true' : 'false' }}"><small class="hint" id="hint-ic">National ID, 6–20 characters.</small></label>
				<label class="field"><span>Gender <b class="req" aria-hidden="true">*</b></span>
					<select id="gender" name="gender" required aria-describedby="hint-gender" aria-invalid="{{ $errors->has('gender') ? 'true' : 'false' }}">
						<option value="male" @selected(old('gender')==='male')>Male</option>
						<option value="female" @selected(old('gender')==='female')>Female</option>
						<option value="other" @selected(old('gender','other')==='other')>Other</option>
					</select>
					<small class="hint" id="hint-gender">Choose the option that best fits.</small>
				</label>
				<label class="field"><span>Date of Birth <b class="req" aria-hidden="true">*</b></span><input id="date_of_birth" type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required aria-describedby="hint-dob" aria-invalid="{{ $errors->has('date_of_birth') ? 'true' : 'false' }}"><small class="hint" id="hint-dob">Must be in the past.</small></label>
				<label class="field"><span>Phone <b class="req" aria-hidden="true">*</b></span><input id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required inputmode="tel" autocomplete="tel" pattern="[0-9+\-\s]{7,20}" maxlength="20" aria-describedby="hint-phone" aria-invalid="{{ $errors->has('phone_number') ? 'true' : 'false' }}"><small class="hint" id="hint-phone">Digits only, include country code if needed.</small></label>
				<label class="field"><span>Email</span><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" aria-describedby="hint-email"><small class="hint" id="hint-email">Optional, for notifications.</small></label>
				<label class="field" style="grid-column:1 / -1"><span>Address <b class="req" aria-hidden="true">*</b></span><input id="address" name="address" value="{{ old('address') }}" required aria-required="true" autocomplete="street-address" aria-describedby="hint-address"><small class="hint" id="hint-address">House/Street and area.</small></label>
				<label class="field"><span>Address Line 2</span><input id="address2" name="_address2" value="" autocomplete="address-line2" aria-describedby="hint-address2"><small class="hint" id="hint-address2">Apartment, suite, unit, etc. (optional)</small></label>
				<label class="field"><span>Postcode</span><input id="postcode" name="_postcode" value="" inputmode="numeric" pattern="[0-9]{4,6}" maxlength="6" aria-describedby="hint-postcode"><small class="hint" id="hint-postcode">4–6 digits (optional)</small></label>
				<label class="field"><span>Emergency Contact Name <b class="req" aria-hidden="true">*</b></span><input id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" required aria-required="true" aria-describedby="hint-emname"><small class="hint" id="hint-emname">Someone we can reach in emergencies.</small></label>
				<label class="field"><span>Emergency Contact Phone <b class="req" aria-hidden="true">*</b></span><input id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" required aria-required="true" inputmode="tel" autocomplete="tel" pattern="[0-9+\-\s]{7,20}" maxlength="20" aria-describedby="hint-emphone"><small class="hint" id="hint-emphone">Digits only.</small></label>
				<label class="field"><span>Relationship <b class="req" aria-hidden="true">*</b></span><input id="emergency_contact_relationship" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship') }}" required aria-required="true" aria-describedby="hint-emrel"><small class="hint" id="hint-emrel">e.g., Spouse, Parent, Friend.</small></label>
				<label class="field"><span>Blood Type</span>
					<select id="blood_type" name="blood_type" aria-describedby="hint-blood">
						@php $bts=['A+','A-','B+','B-','AB+','AB-','O+','O-']; @endphp
						@foreach($bts as $bt)
							<option value="{{ $bt }}" @selected(old('blood_type')===$bt)>{{ $bt }}</option>
						@endforeach
					</select>
					<small class="hint" id="hint-blood">Select your known type (optional).</small>
				</label>
				<label class="field" style="grid-column:1 / -1"><span>Allergies</span><input id="allergies" name="allergies" list="allergy-list" value="{{ old('allergies') }}" placeholder="e.g. Penicillin"><datalist id="allergy-list"><option>Penicillin</option><option>Peanuts</option><option>Latex</option><option>Seafood</option><option>NSAIDs</option></datalist></label>
				<label class="field" style="grid-column:1 / -1"><span>Medical History</span><input id="medical_history" name="medical_history" value="{{ old('medical_history') }}" placeholder="Past illnesses or surgeries"></label>
				<label class="field" style="grid-column:1 / -1"><span>Current Medications</span><input id="current_medications" name="current_medications" value="{{ old('current_medications') }}"></label>
				<label class="field" style="grid-column:1 / -1"><span>Chronic Conditions</span><input id="chronic_conditions" name="chronic_conditions" list="chronic-list" value="{{ old('chronic_conditions') }}" placeholder="e.g. Hypertension"><datalist id="chronic-list"><option>Hypertension</option><option>Diabetes</option><option>Asthma</option><option>Heart disease</option><option>Kidney disease</option></datalist></label>
			</div>
			<div class="form__row" style="justify-content:space-between"><div class="muted" style="font-size:14px">Fields marked <b class="req">*</b> are required</div><button class="btn btn--gold" type="submit">Create Profile</button></div>
		</form>
	</div>
</div>

<style>
	:root{--paper:#fbfaf7;--ink:#1c1c1c;--muted:#6b6b6b;--line:rgba(0,0,0,.08);--gold:#c7a76b;--gold-2:#a68957}
	html,body{background:var(--paper);color:var(--ink);font-family:"Courier New", Courier, monospace}
	.wrap{max-width:1020px;margin:0 auto;padding:0 36px}
	.auth{padding:36px;border-radius:20px;border:1px solid var(--line);background:#fff;box-shadow:0 16px 42px rgba(0,0,0,.06)}
	.title{font-size:28px}
	.muted{margin:0;color:var(--muted)}
	.alert{border:1px solid var(--line);border-left-width:4px;border-radius:12px;padding:12px 14px;margin:12px 0;background:#fff}
	.alert--error{border-left-color:#ef4444}
	.form{display:grid;gap:22px;margin-top:16px}
	.grid2{display:grid;grid-template-columns:1fr 1fr;gap:24px}
	.field{display:grid;gap:10px}
	.field span{font-size:13px;color:#6a6a6a;letter-spacing:.06em;text-transform:uppercase}
	.req{color:#c2410c;font-weight:900;margin-left:4px}
	.field input, .field select{height:54px;border-radius:12px;border:1px solid var(--line);background:#fff;color:#111;padding:0 14px;transition:border-color .15s, box-shadow .15s}
	.field input:focus, .field select:focus{outline:none;border-color:rgba(198,167,107,.7);box-shadow:0 0 0 3px rgba(198,167,107,.15)}
	.hint{font-size:12px;color:#8a8a8a;margin-top:-4px}
	.form__row{display:flex;align-items:center;gap:12px}
	.btn{appearance:none;border:none;cursor:pointer;font-weight:800;border-radius:999px;padding:12px 20px}
	.btn--gold{background:linear-gradient(180deg,var(--gold),var(--gold-2));color:#141414;box-shadow:0 16px 42px rgba(198,167,107,.18)}
	@media(max-width:980px){.grid2{grid-template-columns:1fr}}
</style>
<script>
document.addEventListener('DOMContentLoaded', function(){
	const form = document.getElementById('patient-create-form');
	if(!form) return;
	const draftKey = 'patient_profile_draft_v1';
	const fields = Array.from(form.querySelectorAll('input[name], select[name]'));

	// Error jump links
	const err = document.getElementById('error-summary');
	if (err){
		err.addEventListener('click', function(e){
			const link = e.target.closest('a[data-jump]');
			if (!link) return;
			e.preventDefault();
			const name = link.getAttribute('data-jump');
			const el = form.querySelector('[name="'+name+'"]');
			if (el){ el.scrollIntoView({behavior:'smooth', block:'center'}); el.focus({preventScroll:true}); }
		});
	}

	// Completeness meter
	function updateMeter(){
		const required = form.querySelectorAll('[required]');
		let filled = 0;
		required.forEach(el => { if ((el.value||'').trim().length>0) filled++; });
		const pct = required.length ? Math.round((filled/required.length)*100) : 100;
		document.getElementById('complete-bar').style.width = pct+'%';
		document.getElementById('complete-text').textContent = pct+'%';
	}
	fields.forEach(el => el.addEventListener('input', updateMeter));
	updateMeter();

	// Autosave draft
	function saveDraft(){
		const data = {};
		fields.forEach(el => data[el.name]=el.value);
		localStorage.setItem(draftKey, JSON.stringify(data));
	}
	function loadDraft(){
		try{
			const raw = localStorage.getItem(draftKey); if (!raw) return;
			const data = JSON.parse(raw)||{};
			fields.forEach(el => { if(!el.value && data[el.name]) el.value = data[el.name]; });
			updateMeter();
		}catch(_){ }
	}
	fields.forEach(el => el.addEventListener('change', saveDraft));
	loadDraft();

	// Set max date for DOB = today
	const dob = document.getElementById('date_of_birth');
	if (dob){ dob.max = new Date().toISOString().slice(0,10); }

	// Address enhancement: combine extras into address on submit
	form.addEventListener('submit', function(){
		const a = document.getElementById('address');
		const a2 = document.getElementById('address2');
		const pc = document.getElementById('postcode');
		let parts = [a.value];
		if (a2 && a2.value) parts.push(a2.value);
		if (pc && pc.value) parts.push(pc.value);
		a.value = parts.filter(Boolean).join(', ');
		localStorage.removeItem(draftKey);
	});
});
</script>
@endsection
