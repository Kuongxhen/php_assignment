@extends('layouts.app')

@section('title', 'Staff Directory')

@section('content')
<div class="wrap" style="max-width:1100px;margin:0 auto;padding:32px 32px 60px">
	<header style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px">
		<h2 style="margin:0">Staff Directory</h2>
		<a class="btn btn--gold" href="{{ route('staffmod.admin.createStaff') }}">Create Staff</a>
	</header>

	<div class="card" style="padding:16px;border:1px solid var(--line);border-radius:14px;background:#fff">
		<table style="width:100%;border-collapse:collapse">
			<thead>
				<tr style="text-align:left;border-bottom:1px solid var(--line)">
					<th style="padding:10px">ID</th>
					<th style="padding:10px">Name</th>
					<th style="padding:10px">Email</th>
					<th style="padding:10px">Phone</th>
					<th style="padding:10px">Role</th>
					<th style="padding:10px">Hired</th>
					<th style="padding:10px">Status/Actions</th>
				</tr>
			</thead>
			<tbody>
				@forelse(($staff ?? []) as $s)
					<tr style="border-bottom:1px solid var(--line)">
						<td style="padding:10px">{{ $s->staffId }}</td>
						<td style="padding:10px">{{ $s->staffName }}</td>
						<td style="padding:10px">{{ $s->staffEmail }}</td>
						<td style="padding:10px">{{ $s->staffPhoneNumber }}</td>
						<td style="padding:10px;text-transform:capitalize">{{ $s->role }}</td>
						<td style="padding:10px">{{ \Illuminate\Support\Carbon::parse($s->dateHired)->format('Y-m-d') }}</td>
						<td style="padding:10px">
							@if($s->role === 'receptionist')
								<span class="muted">{{ $s->status ?? '—' }}</span>
								@if(($s->status ?? 'inactive') !== 'active')
									<form method="POST" action="{{ route('staffmod.admin.receptionist.activate', $s->staffId) }}" style="display:inline">@csrf<button class="btn btn--line" type="submit">Activate</button></form>
								@else
									<form method="POST" action="{{ route('staffmod.admin.receptionist.deactivate', $s->staffId) }}" style="display:inline">@csrf<button class="btn btn--line" type="submit">Deactivate</button></form>
								@endif
							@else
								—
							@endif
						</td>
					</tr>
				@empty
					<tr><td style="padding:10px" colspan="7">No staff found.</td></tr>
				@endforelse
			</tbody>
		</table>
	</div>
</div>
@endsection


