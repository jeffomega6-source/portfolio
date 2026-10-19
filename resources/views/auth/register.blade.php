@extends('layout')
@section('title','Create an account — Portalink')
@section('content')
<main class="auth-shell"><div class="auth-card"><img class="auth-brand-logo" src="{{ asset('images/portalink-logo.png') }}" alt="Portalink"><div class="eyebrow">YOUR NEXT CHAPTER STARTS HERE</div><h1>Make room for you.</h1><p>Create an account to save and manage your portfolios.</p>
<form method="POST" action="{{ route('register.store') }}">@csrf
<label class="field">Your name<input name="name" value="{{ old('name') }}" autocomplete="name" maxlength="120" required autofocus>@error('name')<small class="field-error">{{ $message }}</small>@enderror</label>
<label class="field">Email address<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>@error('email')<small class="field-error">{{ $message }}</small>@enderror</label>
<label class="field">Password<input type="password" name="password" autocomplete="new-password" minlength="8" required>@error('password')<small class="field-error">{{ $message }}</small>@enderror</label>
<label class="field">Confirm password<input type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required></label>
<button class="button button-dark auth-submit" type="submit">Create my account <span>↗</span></button></form>
<div class="auth-switch">Already have an account? <a href="{{ route('login') }}">Sign in</a></div></div></main>
@endsection
