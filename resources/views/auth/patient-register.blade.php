@extends('layouts.app')

@section('title', 'Create Account')

@section('content')
<div class="wrap narrow" style="min-height:calc(100vh - 60px);display:flex;align-items:center;justify-content:center">
	<div class="auth">
		<header class="auth__head">
			<h3 class="title">Create your account</h3>
			<p class="muted">Join our clinic portal to manage appointments and records.</p>
		</header>
		@if($errors->any())
			<div class="alert alert--error">
				<strong>We found some issues:</strong>
				<ul style="margin:8px 0 0;padding-left:16px">
					@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
				</ul>
			</div>
		@endif
		<form method="POST" action="{{ route('patient.register') }}" class="form" id="register-form" novalidate>
			@csrf
			<div class="grid2">
				<label class="field">
					<span>First Name</span>
					<input type="text" id="first_name" value="" required aria-describedby="hint-fn"><small class="hint" id="hint-fn">Given name.</small>
				</label>
				<label class="field">
					<span>Last Name</span>
					<input type="text" id="last_name" value="" required aria-describedby="hint-ln"><small class="hint" id="hint-ln">Family name.</small>
				</label>
			</div>
			<input type="hidden" name="name" id="full_name" value="{{ old('name') }}" />
			<label class="field">
				<span>Email</span>
				<input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" aria-describedby="hint-email">
				<small class="hint" id="hint-email">Format: name@x.com</small>
			</label>
			<label class="field">
				<span>Password</span>
				<input type="password" name="password" id="password" required autocomplete="new-password" minlength="8" aria-describedby="hint-pass">
				<small class="hint" id="hint-pass">At least 8 characters.</small>
			</label>
			<label class="field">
				<span>Confirm Password</span>
				<input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password" aria-describedby="hint-pass2">
				<small class="hint" id="hint-pass2">Must match the password.</small>
			</label>
			<div class="form__row">
				<button type="submit" class="btn btn--gold">Create Account</button>
				<a href="{{ route('home') }}" class="link">Back to Home</a>
			</div>
		</form>
	</div>
</div>

<style>
	:root{--paper:#fbfaf7;--ink:#1c1c1c;--muted:#6b6b6b;--line:rgba(0,0,0,.08);--gold:#c7a76b;--gold-2:#a68957}
	html,body{background:var(--paper);color:var(--ink);font-family:"Courier New", Courier, monospace}
	.wrap{max-width:760px;margin:0 auto;padding:0 32px}
	.auth{padding:28px;border-radius:20px;border:1px solid var(--line);background:#fff;box-shadow:0 12px 34px rgba(0,0,0,.06);width:100%}
	.auth__head{margin-bottom:12px}
	.title{margin:0 0 6px;font-size:22px}
	.muted{margin:0;color:var(--muted)}
	.form{display:grid;gap:14px;margin-top:12px}
	.grid2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
	.field{display:grid;gap:8px}
	.field span{font-size:12px;color:#6a6a6a;letter-spacing:.06em;text-transform:uppercase}
	.field input{height:48px;border-radius:14px;border:1px solid var(--line);background:#fff;color:#111;padding:0 14px}
	.field input:focus{outline:none;border-color:rgba(198,167,107,.5)}
	.field--error input{border-color:#ef4444}
	.error{color:#ef4444;font-size:12px}
	.hint{font-size:12px;color:#8a8a8a}
	.form__row{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:4px}
	.btn{appearance:none;border:none;cursor:pointer;font-weight:800;border-radius:999px;padding:12px 20px}
	.btn--gold{background:linear-gradient(180deg,var(--gold),var(--gold-2));color:#141414;box-shadow:0 16px 42px rgba(198,167,107,.18)}
	.btn--gold:hover{transform:translateY(-1px);box-shadow:0 22px 56px rgba(198,167,107,.26)}
	.link{text-decoration:none;color:#444;border-bottom:1px solid transparent}
	.link:hover{border-color:var(--line)}
</style>
<script>
document.addEventListener('DOMContentLoaded', function(){
	const form = document.getElementById('register-form');
	if(!form) return;
	const first = document.getElementById('first_name');
	const last = document.getElementById('last_name');
	const full = document.getElementById('full_name');
	const email = document.getElementById('email');
	const pass = document.getElementById('password');
	const pass2 = document.getElementById('password_confirmation');

	function setError(el, msg){
		const field = el.closest('.field');
		field.classList.add('field--error');
		el.setAttribute('aria-invalid','true');
		let err = field.querySelector('.error');
		if(!err){ err = document.createElement('small'); err.className = 'error'; field.appendChild(err); }
		err.textContent = msg;
	}
	function clearError(el){
		const field = el.closest('.field');
		field.classList.remove('field--error');
		el.removeAttribute('aria-invalid');
		const err = field.querySelector('.error');
		if(err){ err.remove(); }
	}

	function validate(){
		let ok = true;
		// names
		[first,last].forEach(el=>clearError(el));
		const nameRe = /^[A-Za-z][A-Za-z\-\s']{1,49}$/;
		if(!nameRe.test(first.value.trim())){ setError(first, 'Enter a valid first name.'); ok = false; }
		if(!nameRe.test(last.value.trim())){ setError(last, 'Enter a valid last name.'); ok = false; }
		// email
		clearError(email);
		const emailRe = /^[^\s@]+@x\.com$/i;
		if(!emailRe.test(email.value.trim())){ setError(email, 'Enter a valid email.'); ok = false; }
		// passwords
		[pass,pass2].forEach(el=>clearError(el));
		if((pass.value||'').length < 8){ setError(pass, 'Password must be at least 8 characters.'); ok = false; }
		if(pass.value !== pass2.value){ setError(pass2, 'Passwords do not match.'); ok = false; }
		return ok;
	}

	[first,last,email,pass,pass2].forEach(el=>el.addEventListener('input', ()=>{ clearError(el); }));

	form.addEventListener('submit', function(e){
		full.value = [first.value.trim(), last.value.trim()].filter(Boolean).join(' ');
		if(!validate()){
			e.preventDefault();
		}
	});
});
</script>
@endsection