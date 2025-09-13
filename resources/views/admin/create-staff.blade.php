@extends('layouts.app')

@section('title', 'Create Staff')

@section('content')
<div class="wrap narrow" style="padding:32px 32px 60px">
	<div class="auth">
		<header class="auth__head"><h2 class="title">Create Staff Member</h2><p class="muted">Add doctors, receptionists, or admins.</p></header>
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
		@if(session('success'))<div class="alert alert--success">{{ session('success') }}</div>@endif
		@if(session('error'))<div class="alert alert--error">{{ session('error') }}</div>@endif
		<div class="meter" aria-hidden="false" aria-label="Completeness">
			<div class="meter__bar" id="complete-bar" style="width:0%"></div>
			<div class="meter__label"><span id="complete-text">0%</span> complete</div>
		</div>
		<form method="POST" action="{{ route('staffmod.admin.createStaff.do') }}" class="form" id="staff-create-form" novalidate>
			@csrf
			<div class="grid2">
				<label class="field"><span>Staff ID <b class="req">*</b></span><input id="staffId" name="staffId" value="{{ old('staffId') }}" required aria-describedby="hint-staffId"><small class="hint" id="hint-staffId">Unique ID.</small></label>
				<label class="field"><span>Full Name <b class="req">*</b></span><input id="staffName" name="staffName" value="{{ old('staffName') }}" required aria-describedby="hint-name"><small class="hint" id="hint-name">Person’s full name.</small></label>
				<label class="field"><span>Email <b class="req">*</b></span><input id="staffEmail" type="email" name="staffEmail" value="{{ old('staffEmail') }}" required aria-describedby="hint-email"><small class="hint" id="hint-email">Format: name@x.com</small></label>
				<label class="field"><span>Phone <b class="req">*</b></span><input id="staffPhoneNumber" name="staffPhoneNumber" value="{{ old('staffPhoneNumber') }}" required aria-describedby="hint-phone"><small class="hint" id="hint-phone">Digits only.</small></label>
				<label class="field"><span>Date Hired <b class="req">*</b></span><input id="dateHired" type="date" name="dateHired" value="{{ old('dateHired', now()->format('Y-m-d')) }}" required></label>
				<label class="field"><span>Password <b class="req">*</b></span><input id="password" type="password" name="password" minlength="6" required aria-describedby="hint-pass"><small class="hint" id="hint-pass">At least 6 characters.</small></label>
				<label class="field"><span>Role <b class="req">*</b></span>
					<select id="role" name="role" required>
						<option value="doctor" @selected(old('role')==='doctor')>Doctor</option>
						<option value="receptionist" @selected(old('role')==='receptionist')>Receptionist</option>
						<option value="admin" @selected(old('role','admin')==='admin')>Admin</option>
					</select>
				</label>
				<label class="field" id="specialization-field" style="grid-column:1 / -1"><span>Specialization (Doctor)</span><input id="specialization" name="specialization" value="{{ old('specialization') }}" placeholder="e.g. Cardiology"></label>
				<label class="field"><span>Admin Level</span><input id="adminLevel" name="adminLevel" type="number" min="1" max="5" value="{{ old('adminLevel', 1) }}"></label>
			</div>
			<div class="form__row" style="justify-content:space-between"><div class="muted" style="font-size:14px">Fields marked <b class="req">*</b> are required</div><button class="btn btn--gold" type="submit">Create</button></div>
		</form>
	</div>
</div>
@endsection

<style>
/* match patient form spacing */
.form{display:grid;gap:22px;margin-top:16px}
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:24px}
.field{display:grid;gap:8px}
.field span{font-size:13px;color:#6a6a6a;letter-spacing:.06em;text-transform:uppercase}
.field input, .field select{height:54px;border-radius:12px;border:1px solid var(--line);background:#fff;color:#111;padding:0 14px;transition:border-color .15s, box-shadow .15s}
.field input:focus, .field select:focus{outline:none;border-color:rgba(198,167,107,.7);box-shadow:0 0 0 3px rgba(198,167,107,.15)}
.form__row{display:flex;align-items:center;gap:12px}
.meter{position:relative;border:1px solid var(--line);border-radius:12px;height:18px;background:#fff;margin:10px 0}
.meter__bar{height:100%;background:linear-gradient(90deg,var(--gold),var(--gold-2));border-radius:12px 0 0 12px;width:0%;transition:width .2s}
.meter__label{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:12px;color:#333;pointer-events:none}
.hint{font-size:12px;color:#8a8a8a}
.req{color:#c2410c;font-weight:900;margin-left:4px}
@media(max-width:980px){.grid2{grid-template-columns:1fr}}
</style>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('staff-create-form');
    if(!form) return;
    const err = document.getElementById('error-summary');
    if (err){
        err.addEventListener('click', function(e){
            const link = e.target.closest('a[data-jump]');
            if (!link) return; e.preventDefault();
            const el = form.querySelector('[name="'+link.getAttribute('data-jump')+'"]');
            if (el){ el.scrollIntoView({behavior:'smooth', block:'center'}); el.focus({preventScroll:true}); }
        });
    }
    function updateMeter(){
        const required = form.querySelectorAll('[required]');
        let filled = 0; required.forEach(el=>{ if((el.value||'').trim().length>0) filled++; });
        const pct = required.length ? Math.round((filled/required.length)*100) : 100;
        document.getElementById('complete-bar').style.width = pct+'%';
        document.getElementById('complete-text').textContent = pct+'%';
    }
    form.querySelectorAll('input,select').forEach(el=>el.addEventListener('input', updateMeter));
    updateMeter();

    // Toggle specialization when role = doctor
    const role = document.getElementById('role');
    const specField = document.getElementById('specialization-field');
    function toggleSpec(){ specField.style.display = (role.value === 'doctor') ? 'block' : 'none'; }
    role.addEventListener('change', toggleSpec); toggleSpec();
});
</script>


