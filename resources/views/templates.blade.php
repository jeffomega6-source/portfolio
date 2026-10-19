@extends('layout')
@section('title','Pick a look — Portalink')
@section('content')
<main class="form-shell template-shell"><div class="form-header"><div class="eyebrow">STEP 2 OF 2 · FIND YOUR LOOK</div><h1>Three ways to <em>show up.</em></h1><p>Choose the feeling that fits you. You can switch any time.</p></div><form method="POST" action="{{ route('template.choose',$portfolio) }}">@csrf @method('PUT')<div class="template-grid">
@foreach([
    ['simple', 'Simple', 'Calm and considered. A quiet editorial portfolio with graceful type and plenty of room for your work.', '✳ folio', 'DESIGNER & CREATIVE', 'Thoughtful work. Good people.', '01 — 03'],
    ['modern', 'Modern', 'A confident digital profile. Deep navy, electric blue and crisp project cards make every detail feel current.', 'ALEX MORGAN', 'PRODUCT DESIGNER · NEW YORK', 'I make digital things matter.', 'SELECTED / 03'],
    ['creative', 'Creative', 'A bold creative playground. Punchy color, expressive shapes and a little surprise at every turn.', 'AM / 26', 'INDEPENDENT CREATIVE PRACTICE', 'Making a little more magic.', '✳ ✦ ✳'],
] as [$key, $name, $description, $brand, $role, $tagline, $workLabel])
<label class="template-choice">
    <input type="radio" name="template" value="{{ $key }}" @checked($portfolio->template === $key) required>
    <div class="template-preview preview-{{ $key }}">
        <div class="pv-top"><span>{{ $brand }}</span><span aria-hidden="true">{{ $key === 'creative' ? '✦' : '↗' }}</span></div>
        <div class="pv-main"><small>{{ $role }}</small><strong>John Doe</strong><span>{{ $tagline }}</span></div>
        <div class="pv-bottom"><span>SELECTED WORK</span><span>{{ $workLabel }}</span></div>
    </div>
    <div class="choice-info"><span><b>{{ $name }}</b><small>{{ $description }}</small></span><i class="choice-radio" aria-hidden="true"></i></div>
</label>
@endforeach
</div><div class="form-actions"><a class="button button-quiet" href="{{ route('portfolios.edit',$portfolio) }}">Edit details</a><button class="button button-dark">Preview my portfolio <span>↗</span></button></div></form></main>
@endsection
