@extends('layouts.app')

@section('title','New Message')

@section('content')
<div class="wrap narrow" style="padding:32px 32px 60px">
	<div class="auth">
		<header class="auth__head"><h2 class="title">Message Receptionist</h2><p class="muted">Send a secure message to the clinic reception.</p></header>
		@if($errors->any())
			<div class="alert alert--error"><strong>We found some issues:</strong><ul style="margin:8px 0 0;padding-left:16px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
		@endif
		<form method="POST" action="{{ route('patient.messages.store') }}" class="form">
			@csrf
			<div class="grid2">
				<label class="field"><span>Category</span>
					<select name="category" required>
						<option value="general">General</option>
						<option value="prescription">Prescription</option>
						<option value="billing">Billing</option>
					</select>
				</label>
				<label class="field"><span>Subject</span><input name="subject" value="{{ old('subject') }}" required></label>
			</div>
			<label class="field" style="display:grid;gap:8px"><span>Message</span><textarea name="body" rows="6" required style="border:1px solid var(--line);border-radius:12px;padding:12px"></textarea></label>
			<div class="form__row" style="justify-content:flex-end"><button class="btn btn--gold" type="submit">Send</button></div>
		</form>
	</div>
</div>
@endsection


