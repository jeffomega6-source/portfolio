@extends('layout')
@section('title','Sign in — Folio Studio')
@section('content')
<main class="auth-shell"><div class="auth-card"><div class="auth-mark">f.</div><div class="eyebrow">WELCOME BACK</div><h1>Good to see you.</h1><p>Sign in to pick up where your story left off.</p>
<form method="POST" action="{{ route('login.store') }}">@csrf
<label class="field">Email address<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>@error('email')<small class="field-error">{{ $message }}</small>@enderror</label>
<label class="field">Password<input type="password" name="password" autocomplete="current-password" required>@error('password')<small class="field-error">{{ $message }}</small>@enderror</label>
<label class="remember"><input type="checkbox" name="remember" value="1"> Keep me signed in</label>
<button class="button button-dark auth-submit" type="submit">Sign in <span>↗</span></button></form>
<div class="auth-switch">New to Folio? <a href="{{ route('register') }}">Create an account</a></div></div></main>
@endsection
