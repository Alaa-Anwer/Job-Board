<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'candidate_id',
        'application_method',
        'resume',
        'contact_email',
        'contact_phone',
        'cover_letter',
        'application_message',
        'status',
    ];

    // Defines attribute casting for application fields.
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'status' => ApplicationStatus::class,
        ];
    }

    // Returns the job associated with this application.
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    // Returns the candidate who submitted this application.
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(User::class, 'candidate_id');
    }

    // Returns chat messages linked to this application.
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
