@extends('layout')
@section('title','My portfolios — Portalink')
@section('content')
<main class="dashboard-shell">
 <section class="dashboard">
  <div class="section-heading"><div><div class="eyebrow">YOUR WORKSPACE</div><h1>My portfolios <span class="count">{{ $portfolios->count() }}</span></h1><p class="dashboard-intro">Create, edit, and share your work from here.</p></div><a class="button button-dark" href="{{ route('portfolios.create') }}"><span aria-hidden="true">＋</span> Create portfolio <span aria-hidden="true">↗</span></a></div>
  @if($portfolios->isEmpty())
   <div class="empty-state"><div class="empty-icon" aria-hidden="true">＋</div><h2>Your first portfolio starts here.</h2><p>Add your story, choose a style, and share it with the world.</p><a class="button button-dark" href="{{ route('portfolios.create') }}">Create my first portfolio <span aria-hidden="true">↗</span></a></div>
  @else
   <div class="portfolio-grid">@foreach($portfolios as $portfolio)<article class="dashboard-card"><a class="mini-preview mini-{{ $portfolio->template }}" href="{{ route('preview',$portfolio) }}" aria-label="Preview {{ $portfolio->full_name }}'s portfolio"><span>{{ strtoupper($portfolio->template) }} TEMPLATE</span><b>{{ $portfolio->full_name }}</b><i>{{ $portfolio->about ?: 'A little bit about me…' }}</i><small>Open preview <span aria-hidden="true">↗</span></small></a><div class="dash-card-info"><div><h2>{{ $portfolio->full_name }}</h2><p>{{ $portfolio->email }}</p></div><span class="status-dot" aria-label="{{ $portfolio->is_complete ? 'Complete' : 'Draft' }}">{{ $portfolio->is_complete ? 'Complete' : 'Draft' }}</span></div><div class="dash-actions"><a href="{{ route('preview',$portfolio) }}"><span aria-hidden="true">◉</span> Preview</a><a href="{{ route('portfolios.edit',$portfolio) }}"><span aria-hidden="true">✎</span> Edit</a><form method="POST" action="{{ route('portfolios.destroy',$portfolio) }}" onsubmit="return confirm('Delete this portfolio permanently?')">@csrf @method('DELETE')<button><span aria-hidden="true">×</span> Delete</button></form></div></article>@endforeach</div>
  @endif
 </section>
</main>
@endsection
