<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Creates the job to category pivot table.
    public function up(): void
    {
        Schema::create('job_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('jobs')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');

            $table->unique(['job_id', 'category_id']);
        });
    }

    // Drops the job to category pivot table.
    public function down(): void
    {
        Schema::dropIfExists('job_category');
    }
};
