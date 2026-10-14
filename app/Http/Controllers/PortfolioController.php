<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function index(Request $request): View
    {
        $portfolios = $request->user()
            ? $request->user()->portfolios()->latest()->get()
            : collect();

        return view('home', compact('portfolios'));
    }

    public function create(): View
    {
        return view('form', ['portfolio' => new Portfolio]);
    }

    public function edit(Portfolio $portfolio, Request $request): View
    {
        $this->authorizePortfolio($portfolio, $request);

        return view('form', compact('portfolio'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['user_id'] = $request->user()->id;
        $portfolio = Portfolio::create($data);

        return redirect()->route('templates', $portfolio)->with('success', 'Your information is saved. Choose a design to continue.');
    }

    public function update(Request $request, Portfolio $portfolio): RedirectResponse
    {
        $this->authorizePortfolio($portfolio, $request);
        $portfolio->update($this->validated($request));

        return redirect()->route('templates', $portfolio)->with('success', 'Your changes are saved.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'max:180'],
            'phone' => ['nullable', 'string', 'max:40'], 'address' => ['nullable', 'string', 'max:180'],
            'about' => ['nullable', 'string', 'max:3000'], 'profile_picture' => ['nullable', 'url', 'max:500'],
            'education_text' => ['nullable', 'string', 'max:5000'], 'skills_text' => ['nullable', 'string', 'max:2000'],
            'projects_text' => ['nullable', 'string', 'max:6000'], 'experience_text' => ['nullable', 'string', 'max:6000'],
            'links_text' => ['nullable', 'string', 'max:3000'], 'is_complete' => ['nullable', 'boolean'],
        ]);
        foreach (['education', 'skills', 'projects', 'experience', 'links'] as $key) {
            $source = $key.'_text';
            $data[$key] = collect(preg_split('/\r\n|\r|\n/', $data[$source] ?? ''))
                ->map(fn ($line) => trim($line))->filter()->values()->all();
            unset($data[$source]);
        }
        $data['is_complete'] = $request->boolean('is_complete');

        return $data;
    }

    public function templates(Portfolio $portfolio, Request $request): View
    {
        $this->authorizePortfolio($portfolio, $request);

        return view('templates', compact('portfolio'));
    }

    public function chooseTemplate(Request $request, Portfolio $portfolio): RedirectResponse
    {
        $this->authorizePortfolio($portfolio, $request);
        $data = $request->validate(['template' => ['required', 'in:simple,modern,creative']]);
        $portfolio->update($data);

        return redirect()->route('preview', $portfolio);
    }

    public function preview(Portfolio $portfolio, Request $request): View
    {
        $this->authorizePortfolio($portfolio, $request);

        return view('portfolio', compact('portfolio'));
    }

    public function show(Portfolio $portfolio): View
    {
        return view('portfolio', compact('portfolio'));
    }

    public function destroy(Request $request, Portfolio $portfolio): RedirectResponse
    {
        $this->authorizePortfolio($portfolio, $request);
        $portfolio->delete();

        return redirect()->route('home')->with('success', 'Portfolio deleted.');
    }

    private function authorizePortfolio(Portfolio $portfolio, Request $request): void
    {
        abort_unless($request->user() && $portfolio->user_id === $request->user()->id, 403);
    }
}
