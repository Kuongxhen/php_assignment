@extends('layouts.app')

@section('title','Reception Inbox')

@section('content')
<div class="wrap" style="max-width:1000px;margin:0 auto;padding:32px 32px 60px">
	<header style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px">
		<h2 style="margin:0">Reception Inbox</h2>
	</header>
	<div class="card" style="padding:16px;border:1px solid var(--line);border-radius:14px;background:#fff">
		<table style="width:100%;border-collapse:collapse">
			<thead><tr style="text-align:left;border-bottom:1px solid var(--line)"><th style="padding:10px">When</th><th style="padding:10px">Patient</th><th style="padding:10px">Category</th><th style="padding:10px">Subject</th><th style="padding:10px">Status</th></tr></thead>
			<tbody>
				@forelse($messages as $m)
					<tr style="border-bottom:1px solid var(--line)">
						<td style="padding:10px">{{ $m->created_at->diffForHumans() }}</td>
						<td style="padding:10px">{{ optional($m->patient)->name }}</td>
						<td style="padding:10px">{{ ucfirst($m->category) }}</td>
						<td style="padding:10px"><a class="link" href="{{ route('staffmod.receptionist.message.show',$m->id) }}">{{ $m->subject }}</a></td>
						<td style="padding:10px">{{ $m->status }}</td>
					</tr>
				@empty
					<tr><td style="padding:10px" colspan="5">No messages.</td></tr>
				@endforelse
			</tbody>
		</table>
	</div>
</div>
@endsection


