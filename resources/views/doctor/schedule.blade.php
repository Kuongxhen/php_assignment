@extends('layouts.app')

@section('title', 'Manage Schedule')

@section('content')
<div class="wrap" style="max-width:900px;margin:0 auto;padding:32px 32px 60px">
	<header style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px">
		<h2 style="margin:0">My Schedule</h2>
	</header>

	<div class="card" style="padding:16px;border:1px solid var(--line);border-radius:14px;background:#fff">
		<form id="add-slot" class="form" method="POST" action="{{ route('staffmod.doctor.schedule') }}" onsubmit="submitSchedule(event)">
			@csrf
			<div class="grid2">
				<label class="field"><span>Day</span>
					<select name="dayOfWeek" required>
						@foreach(['monday','tuesday','wednesday','thursday','friday','saturday','sunday'] as $d)
							<option value="{{ $d }}">{{ ucfirst($d) }}</option>
						@endforeach
					</select>
				</label>
				<label class="field"><span>Start</span><input type="time" name="startTime" required></label>
				<label class="field"><span>End</span><input type="time" name="endTime" required></label>
			</div>
			<div class="form__row" style="justify-content:flex-end"><button class="btn btn--gold" type="submit">Add Slot</button></div>
		</form>
	</div>

	<div class="card" style="padding:16px;border:1px solid var(--line);border-radius:14px;background:#fff;margin-top:16px">
		<h3 style="margin:0 0 10px">Existing Slots</h3>
		<ul style="margin:0;padding-left:18px">
			@forelse($schedules as $s)
				<li>{{ ucfirst($s->dayOfWeek) }}: {{ substr($s->startTime,0,5) }}–{{ substr($s->endTime,0,5) }}</li>
			@empty
				<li>No slots yet.</li>
			@endforelse
		</ul>
	</div>
</div>

<script>
async function submitSchedule(e){
	e.preventDefault();
	const form = e.target;
	const res = await fetch(form.action, {method:'POST', body:new FormData(form)});
	if(res.ok){ location.reload(); }
}
</script>
@endsection


