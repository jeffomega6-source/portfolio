<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"><title>@yield('title', 'Portalink — Portfolio studio')</title>
    <link rel="stylesheet" href="{{ asset('folio.css') }}">
    <link rel="stylesheet" href="{{ asset('auth.css') }}">
    <link rel="stylesheet" href="{{ asset('portalink.css') }}">
    @if (file_exists(public_path('build/manifest.json'))) @vite(['resources/css/app.css','resources/js/app.js']) @endif
    <link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
<header class="topbar"><a class="brand brand-logo-window" href="{{ route('home') }}" aria-label="Portalink home"><img src="{{ asset('images/portalink-logo.png') }}" alt="Portalink"></a><nav aria-label="Main navigation">
@auth
<a class="nav-action" href="{{ route('home') }}"><span aria-hidden="true">⌂</span> Home</a><a class="nav-action" href="{{ route('portfolios.index') }}"><span aria-hidden="true">▤</span> My portfolios</a><a class="nav-action nav-create" href="{{ route('portfolios.create') }}"><span aria-hidden="true">＋</span> Create portfolio</a><form method="POST" action="{{ route('logout') }}" class="nav-logout">@csrf<button type="submit"><span aria-hidden="true">↪</span> Sign out</button></form>
@else
<a class="nav-action nav-signin" href="{{ route('login') }}"><span aria-hidden="true">→</span> Sign in</a><a class="nav-action nav-signup" href="{{ route('register') }}"><span aria-hidden="true">＋</span> Create account</a>
@endauth
</nav></header>
@unless(request()->routeIs('home', 'preview') || (request()->routeIs('portfolio.show') && auth()->check() && isset($portfolio) && auth()->id() === $portfolio->user_id))
@php
    $backUrl = match (true) {
        request()->routeIs('templates') => route('portfolios.edit', $portfolio),
        request()->routeIs('portfolios.create', 'portfolios.edit', 'preview') => route('portfolios.index'),
        default => route('home'),
    };
    $backLabel = match (true) {
        request()->routeIs('templates') => 'Back to your details',
        request()->routeIs('portfolios.create', 'portfolios.edit', 'preview') => 'Back to my portfolios',
        default => 'Back to Portalink',
    };
@endphp
<div class="page-backbar"><a class="back-link" href="{{ $backUrl }}"><span aria-hidden="true">←</span> {{ $backLabel }}</a></div>
@endunless
@if(session('success'))<div class="toast">✓ {{ session('success') }}</div>@endif
@yield('content')
<footer class="footer">Made for your next chapter <span>✳</span></footer>
</body></html>
