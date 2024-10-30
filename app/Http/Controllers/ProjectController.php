<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\User;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        if (auth()->user()->can('viewAny', Project::class)) {
            $projects = Project::all();
        } else {
            $projects = auth()->user()->projects;
        }

        return Inertia::render('Dashboard', [
            'projects' => $projects
        ]);
    }

    public function create()
    {
        if (!auth()->user()->can('create', Project::class)) {
            abort(403, 'This is above your pay grade... work harder ;)');
        }

        $regularEmployees = User::query()->where('is_manager', '=', '0')->get();
        return Inertia::render('ProjectMaker', [
            'employees' => $regularEmployees
        ]);
    }

    public function store(StoreProjectRequest $request)
    {
        $project = Project::query()->create($request->validated());
        $members = [$request->user()->id, ...$request->input('members', [])];
        $project->assignees()->attach($members);

        return redirect(route('dashboard'));
    }

    public function edit(Project $project)
    {
        if (!auth()->user()->can('view', $project)) {
            abort(403, 'This is above your pay grade... work harder ;)');
        }

        $nonProjectEmployees = User::query()
            ->where('is_manager', 0)
            ->get()
            ->filter(fn($emp) => !$emp->projects->contains($project->id));

//        $nonTaskMembers = $project->assignees;
//        $nonTaskMembers->filter(fn($member) => $member->tasks);

        //todo resource for Project containing all relevant info?
        $project->load(['assignees', 'tasks']);
        return Inertia::render('ProjectEditor', [
            'selectedProject' => $project,
            'nonProjectEmployees' => $nonProjectEmployees,
            'nonTaskMembers' => 'employees where there is no task_user record?'
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $members = $request->input('members', []);
        $removeMembers = $request->input('removeMembers', []);
        $project->addMembers($members);
        $project->removeMembers($removeMembers);

        $project->update($request->validated());
        return redirect(route('dashboard'));
    }

    public function destroy(Project $project)
    {
        if (!auth()->user()->can('delete', $project)) {
            abort(403, 'This is above your pay grade... work harder ;)');
        }
        $project->delete();
    }

//    public function show(Project $project)
//    {
//        //
//    }
}
