<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\JobStatus;
use App\Enums\JobWorkType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Job extends Model
{
    use HasFactory;

    protected $appends = ['company_logo_url'];

    protected $fillable = [
        'employer_id',
        'title',
        'description',
        'responsibilities',
        'requirements',
        'benefits',
        'salary_min',
        'salary_max',
        'location',
        'work_type',
        'experience_level',
        'company_logo',
        'deadline',
        'status',
    ];

    // Defines attribute casting for job fields.
    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
            'status' => JobStatus::class,
            'work_type' => JobWorkType::class,
        ];
    }

    // Returns the employer who owns the job.
    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    // Returns all applications submitted for the job.
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    // Returns categories attached to the job.
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'job_category');
    }

    // Filters jobs by approved status.
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', JobStatus::Approved->value);
    }

    // Filters jobs by pending status.
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', JobStatus::Pending->value);
    }

    // Filters jobs by title or description keyword.
    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->where(fn(Builder $searchQuery): Builder => $searchQuery
            ->where('title', 'like', '%' . $keyword . '%')
            ->orWhere('description', 'like', '%' . $keyword . '%'));
    }

    // Filters jobs by location.
    public function scopeLocation(Builder $query, string $location): Builder
    {
        return $query->where('location', 'like', '%' . $location . '%');
    }

    // Returns a browser-safe relative URL for the stored company logo.
    public function getCompanyLogoUrlAttribute(): ?string
    {
        if (! filled($this->company_logo)) {
            return null;
        }

        $path = $this->company_logo;

        // Prefer the file if it actually exists in the public disk.
        if (Storage::disk('public')->exists($path)) {
            return Storage::url($path);
        }

        // Try a common alternate folder-name (underscore <-> hyphen) if present.
        $alt = str_replace(['_', '-'], ['-', '_'], $path);
        if (Storage::disk('public')->exists($alt)) {
            return Storage::url($alt);
        }

        // Fallback to the relative /storage path; browser will show broken image if missing.
        return '/storage/' . $path;
    }
}
