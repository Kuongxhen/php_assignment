@extends('layouts.app')

@section('title', 'Staff Login')

@section('content')
<div class="wrap narrow" style="padding:32px 32px 60px">
	<div class="auth">
		<header class="auth__head"><h2 class="title">Staff Login</h2><p class="muted">Sign in to access staff portal.</p></header>
		@if($errors->any())
			<div class="alert alert--error"><strong>We found some issues:</strong><ul style="margin:8px 0 0;padding-left:16px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
		@endif
		<form method="POST" action="{{ route('staffmod.login') }}" class="form">
			@csrf
			<div class="grid2">
				<label class="field"><span>Email</span><input type="email" name="email" value="{{ old('email') }}" required></label>
				<label class="field"><span>Password</span><input type="password" name="password" required></label>
				<label class="field"><span>Role</span>
					<select name="role" required>
						<option value="doctor" @selected(old('role')==='doctor')>Doctor</option>
						<option value="receptionist" @selected(old('role')==='receptionist')>Receptionist</option>
						<option value="admin" @selected(old('role','admin')==='admin')>Admin</option>
					</select>
				</label>
			</div>
			<div class="form__row" style="justify-content:flex-end"><button class="btn btn--gold" type="submit">Sign In</button></div>
		</form>
	</div>
</div>
@endsection


