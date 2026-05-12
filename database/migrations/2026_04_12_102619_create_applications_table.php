<?php

declare(strict_types=1);

use App\Enums\ApplicationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Creates the applications table.
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('job_id')->constrained('jobs')->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained('users')->cascadeOnDelete();
            $table->string('resume');
            $table->text('cover_letter')->nullable();
            $table->enum('status', ApplicationStatus::values())->default(ApplicationStatus::Pending->value);
            $table->timestamps();
            $table->unique(['job_id', 'candidate_id']);
            $table->index('status');
        });
    }

    // Drops the applications table.
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
