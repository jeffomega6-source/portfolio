<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"><title>@yield('title', 'Folio — Portfolio studio')</title>
    <link rel="stylesheet" href="{{ asset('folio.css') }}">
    <link rel="stylesheet" href="{{ asset('auth.css') }}">
    @if (file_exists(public_path('build/manifest.json'))) @vite(['resources/css/app.css','resources/js/app.js']) @endif
    <link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
<header class="topbar"><a class="brand" href="{{ route('home') }}"><span class="brand-mark">f.</span> folio<span class="brand-studio">studio</span></a><nav><a href="{{ route('home') }}">My portfolios</a><a class="nav-create" href="{{ route('portfolios.create') }}">＋ New portfolio</a></nav></header>
@if(session('success'))<div class="toast">✓ {{ session('success') }}</div>@endif
@if(request()->routeIs('home', 'login', 'register'))
@auth
<div class="auth-bar"><span>Signed in as <b>{{ auth()->user()->name }}</b></span><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Sign out ↗</button></form></div>
@else
<div class="auth-bar"><span>Save your work across visits</span><div><a href="{{ route('login') }}">Sign in</a><a href="{{ route('register') }}">Create account ↗</a></div></div>
@endauth
@endif
@yield('content')
<footer class="footer">Made for your next chapter <span>✳</span></footer>
</body></html>
