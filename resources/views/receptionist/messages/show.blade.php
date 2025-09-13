@extends('layouts.app')

@section('title','Message')

@section('content')
<div class="wrap narrow" style="padding:32px 32px 60px">
	<div class="auth">
		<header class="auth__head"><h2 class="title">{{ $message->subject }}</h2><p class="muted">From: {{ optional($message->patient)->name }} • {{ ucfirst($message->category) }} • {{ $message->created_at->toDayDateTimeString() }}</p></header>
		<div class="card" style="padding:16px;border:1px solid var(--line);border-radius:14px;background:#fff;margin-bottom:12px">{{ $message->body }}</div>
		@if($message->reply_body)
			<div class="card" style="padding:16px;border:1px solid var(--line);border-radius:14px;background:#fff;margin-bottom:12px"><span class="muted">Reply:</span> {{ $message->reply_body }}</div>
		@endif
		<form method="POST" action="{{ route('staffmod.receptionist.message.reply',$message->id) }}" class="form">
			@csrf
			<label class="field" style="display:grid;gap:8px"><span>Reply</span><textarea name="reply_body" rows="5" required style="border:1px solid var(--line);border-radius:12px;padding:12px"></textarea></label>
			<div class="form__row" style="justify-content:flex-end"><button class="btn btn--gold" type="submit">Send Reply</button></div>
		</form>
	</div>
</div>
@endsection


