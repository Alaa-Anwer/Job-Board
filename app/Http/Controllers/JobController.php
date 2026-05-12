<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\JobIndexRequest;
use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Models\Category;
use App\Models\Job;
use App\Services\JobService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class JobController extends Controller
{
    // Builds the controller with job service.
    public function __construct(private readonly JobService $jobService) {}

    // Lists approved jobs with filters and pagination.
    public function index(JobIndexRequest $request): View
    {
        $filters = $request->validated();
        $jobs = $this->jobService->paginateApproved(
            $filters,
            $request->integer('per_page', 15)
        );

        return view('jobs.index', [
            'jobs' => $jobs,
            'filters' => $filters,
        ]);
    }

    // Lists jobs posted by the authenticated employer.
    public function myJobs(): View
    {
        $this->authorize('create', Job::class);

        return view('jobs.my-jobs', [
            'jobs' => $this->jobService->paginateForEmployer(auth()->user()),
        ]);
    }

    // Shows the job creation form.
    public function create(): View
    {
        $this->authorize('create', Job::class);

        return view('jobs.create', [
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    // Creates a new job under the current employer.
    public function store(StoreJobRequest $request): RedirectResponse
    {
        $this->authorize('create', Job::class);

        $job = $this->jobService->createForEmployer($request->user(), $request->validated());

        return redirect()->route('jobs.show', $job)->with('success', 'Job created successfully.');
    }

    // Shows a single job with related data.
    public function show(Request $request, Job $job, ?string $slug = null): View|RedirectResponse
    {
        $canonicalSlug = Str::slug($job->title);

        if ($slug !== $canonicalSlug) {
            return redirect()->route('jobs.show', ['job' => $job, 'slug' => $canonicalSlug]);
        }

        $this->authorize('view', $job);

        return view('jobs.show', [
            'job' => $job->load(['employer', 'categories']),
        ]);
    }

    // Shows edit form for an employer job.
    public function edit(Job $job): View
    {
        $this->authorize('update', $job);

        return view('jobs.edit', [
            'job' => $job->load('categories'),
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    // Updates an existing employer job.
    public function update(UpdateJobRequest $request, Job $job): RedirectResponse
    {
        $this->authorize('update', $job);

        $updated = $this->jobService->update($job, $request->validated());

        return redirect()->route('jobs.show', $updated)->with('success', 'Job updated successfully.');
    }

    // Deletes an employer job.
    public function destroy(Job $job): RedirectResponse
    {
        $this->authorize('delete', $job);

        $this->jobService->delete($job);

        return redirect()->route('jobs.index')->with('success', 'Job deleted successfully.');
    }
}
