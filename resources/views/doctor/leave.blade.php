@extends('layouts.app')

@section('title','Leave Requests')

@section('content')
<div class="wrap" style="max-width:900px;margin:0 auto;padding:32px 32px 60px">
	<header style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px">
		<h2 style="margin:0">Apply Leave</h2>
	</header>
	@if(session('success'))<div class="alert alert--success">{{ session('success') }}</div>@endif
	<form method="POST" action="{{ route('staffmod.doctor.storeSchedule') }}" class="form">
		@csrf
		<div class="grid2">
			<label class="field"><span>Start Date</span><input type="date" name="start_date" required></label>
			<label class="field"><span>End Date</span><input type="date" name="end_date" required></label>
		</div>
		<label class="field" style="display:grid;gap:8px"><span>Reason</span><input name="reason" placeholder="Optional"></label>
		<div class="form__row" style="justify-content:flex-end"><button class="btn btn--gold" type="submit">Submit</button></div>
	</form>

	<div class="card" style="padding:16px;border:1px solid var(--line);border-radius:14px;background:#fff;margin-top:16px">
		<h3 style="margin:0 0 10px">My Leave Requests</h3>
		<ul style="margin:0;padding-left:18px">
			@forelse($leaves as $l)
				<li>{{ $l->start_date->format('Y-m-d') }} → {{ $l->end_date->format('Y-m-d') }} — <span class="muted">{{ $l->status }}</span> @if($l->reason) ({{ $l->reason }}) @endif</li>
			@empty
				<li>No leave requests.</li>
			@endforelse
		</ul>
	</div>
</div>
@endsection


