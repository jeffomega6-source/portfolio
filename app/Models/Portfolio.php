<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Portfolio extends Model
{
    protected $fillable = [
        'slug', 'user_id', 'full_name', 'email', 'phone', 'address', 'about', 'profile_picture',
        'education', 'skills', 'projects', 'experience', 'links', 'template', 'is_complete',
    ];

    protected function casts(): array
    {
        return ['education' => 'array', 'skills' => 'array', 'projects' => 'array', 'experience' => 'array', 'links' => 'array', 'is_complete' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getProfilePictureUrlAttribute(): ?string
    {
        $picture = $this->profile_picture;

        if (! $picture) {
            return null;
        }

        if (filter_var($picture, FILTER_VALIDATE_URL)) {
            return $picture;
        }

        return Storage::disk(config('filesystems.photo_disk', 'public'))->url($picture);
    }

    protected static function booted(): void
    {
        static::creating(function (Portfolio $portfolio) {
            $portfolio->slug ??= Str::lower(Str::random(12));
        });
    }
}
