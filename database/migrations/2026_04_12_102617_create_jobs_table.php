<?php

declare(strict_types=1);

use App\Enums\JobStatus;
use App\Enums\JobWorkType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Creates the business jobs table.
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('employer_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->text('responsibilities')->nullable();
            $table->text('requirements')->nullable();
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->string('location');
            $table->enum('work_type', JobWorkType::values());
            $table->date('deadline');
            $table->enum('status', JobStatus::values())->default(JobStatus::Pending->value);
            $table->timestamps();

            $table->index('location');
            $table->index('work_type');
            $table->index('deadline');
            $table->index('status');
        });
    }

    // Drops the business jobs table.
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
