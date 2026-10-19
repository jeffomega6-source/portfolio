<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PortfolioController extends Controller
{
    public function index(Request $request): View
    {
        $portfolios = $request->user()->portfolios()->latest()->get();

        return view('portfolios.index', compact('portfolios'));
    }

    public function home(): View
    {
        return view('home');
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
        $profilePicture = $data['profile_picture_upload'] ?? null;
        unset($data['profile_picture_upload'], $data['remove_profile_picture']);

        if ($profilePicture) {
            $data['profile_picture'] = $this->storeProfilePicture($profilePicture);
        }

        $data['user_id'] = $request->user()->id;
        $portfolio = Portfolio::create($data);

        return redirect()->route('templates', $portfolio)->with('success', 'Your information is saved. Choose a design to continue.');
    }

    public function update(Request $request, Portfolio $portfolio): RedirectResponse
    {
        $this->authorizePortfolio($portfolio, $request);
        $data = $this->validated($request);
        $oldProfilePicture = $portfolio->profile_picture;
        $profilePicture = $data['profile_picture_upload'] ?? null;
        $removeProfilePicture = (bool) ($data['remove_profile_picture'] ?? false);
        unset($data['profile_picture_upload'], $data['remove_profile_picture']);

        if ($profilePicture) {
            $data['profile_picture'] = $this->storeProfilePicture($profilePicture);
        } elseif ($removeProfilePicture) {
            $data['profile_picture'] = null;
        } elseif (blank($data['profile_picture'] ?? null)) {
            $data['profile_picture'] = $oldProfilePicture;
        }

        $portfolio->update($data);
        $this->deleteManagedProfilePicture($oldProfilePicture, $portfolio->profile_picture);

        return redirect()->route('templates', $portfolio)->with('success', 'Your changes are saved.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'max:180'],
            'phone' => ['nullable', 'string', 'max:40'], 'address' => ['nullable', 'string', 'max:180'],
            'about' => ['nullable', 'string', 'max:3000'],
            'profile_picture' => ['nullable', 'url:http,https', 'max:2048'],
            'profile_picture_upload' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
            'remove_profile_picture' => ['nullable', 'boolean'],
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

    private function storeProfilePicture(UploadedFile $picture): string
    {
        $diskName = config('filesystems.photo_disk', 'public');

        if ($diskName === 'supabase') {
            $disk = Storage::disk($diskName);
            $path = 'portfolio-photos/'.$picture->hashName();
            $filePath = $picture->getRealPath();
            $mimeType = $picture->getMimeType();

            if (! $filePath || ! $mimeType) {
                throw new RuntimeException('The profile image could not be read for upload.');
            }

            $stream = fopen($filePath, 'rb');

            if ($stream === false) {
                throw new RuntimeException('The profile image could not be opened for upload.');
            }

            try {
                // Supabase Storage does not support per-object ACL headers.
                $disk->getClient()->putObject([
                    'Bucket' => config('filesystems.disks.supabase.bucket'),
                    'Key' => $path,
                    'Body' => $stream,
                    'ContentType' => $mimeType,
                ]);
            } finally {
                fclose($stream);
            }

            return $path;
        }

        $path = $picture->store('portfolio-photos', $diskName);

        if (! is_string($path)) {
            throw new RuntimeException('The profile image could not be stored.');
        }

        return $path;
    }

    private function deleteManagedProfilePicture(?string $oldPath, ?string $newPath = null): void
    {
        if (! $oldPath || $oldPath === $newPath || ! str_starts_with($oldPath, 'portfolio-photos/')) {
            return;
        }

        if (! Storage::disk(config('filesystems.photo_disk', 'public'))->delete($oldPath)) {
            throw new RuntimeException('The previous profile image could not be deleted.');
        }
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
        $profilePicture = $portfolio->profile_picture;
        $portfolio->delete();
        $this->deleteManagedProfilePicture($profilePicture);

        return redirect()->route('portfolios.index')->with('success', 'Portfolio deleted.');
    }

    private function authorizePortfolio(Portfolio $portfolio, Request $request): void
    {
        abort_unless($request->user() && $portfolio->user_id === $request->user()->id, 403);
    }
}
