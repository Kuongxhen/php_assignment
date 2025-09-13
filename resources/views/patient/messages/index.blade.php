@extends('layouts.app')

@section('title','Messages')

@section('content')
<div class="wrap" style="max-width:900px;margin:0 auto;padding:32px 32px 60px">
	<header style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px">
		<h2 style="margin:0">My Messages</h2>
		<a class="btn btn--gold" href="{{ route('patient.messages.create') }}">New Message</a>
	</header>
	<div class="card" style="padding:16px;border:1px solid var(--line);border-radius:14px;background:#fff">
		<ul style="margin:0;padding-left:18px">
		@forelse($messages as $m)
			<li><strong>[{{ ucfirst($m->category) }}]</strong> {{ $m->subject }} — <span class="muted">{{ $m->created_at->diffForHumans() }}</span>
				@if($m->reply_body)
					<div style="margin-top:6px"><span class="muted">Reply:</span> {{ $m->reply_body }}</div>
				@endif
			</li>
		@empty
			<li>No messages yet.</li>
		@endforelse
		</ul>
	</div>
</div>
@endsection


