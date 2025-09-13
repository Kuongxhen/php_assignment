@extends('layouts.app')

@section('title', 'Appointments')

@section('content')
<div class="wrap" style="max-width:1000px;margin:0 auto;padding:32px 32px 60px">
	<header style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px">
		<h2 style="margin:0">Appointments</h2>
		<a class="link" href="{{ route('staffmod.receptionist.dashboard') }}">Back to Dashboard</a>
	</header>

	<div class="card" style="padding:16px;border:1px solid var(--line);border-radius:14px;background:#fff">
		<p class="muted">This screen will list and manage appointments (module-owned).</p>
	</div>
</div>
@endsection


