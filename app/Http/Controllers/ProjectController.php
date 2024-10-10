<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::all();
        return Inertia::render('Dashboard', [
            'projects' => $projects
        ]);
    }

    public function create()
    {
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

        return response()->json($project);
    }

    public function edit(Project $project)
    {
        if (!auth()->user()->can('view', $project)) {
            abort(403, 'This is above your pay grade... work harder ;)');
        }

        $members = $project->assignees;
        $employees = User::query()
            ->where('is_manager', 0)
            ->get()
            ->filter(fn($emp) => !$emp->projects->contains($project->id));

        return Inertia::render('ProjectEditor', [
            'selectedProject' => $project,
            'projectMembers' => $members,
            'allEmployees' => $employees
        ]);
    }

    public function update(Request $request, Project $project)
    {
        if (!auth()->user()->can('update', $project)) {
            abort(403, 'This is above your pay grade... work harder ;)');
        }

        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string'
        ]);
        $project->update($validated);

        return redirect(route('dashboard'));
    }

    public function destroy(Project $project)
    {
        if (!auth()->user()->can('delete', $project)) {
            abort(403, 'This is above your pay grade... work harder ;)');
        }

        $project->delete();

        // Todo
        //How to refresh??
//        $projects = Project::all();
//        return Inertia::render('dashboard', [
//            'projects' => $projects
//        ]);
        //return Inertia::location(route('dashboard'));
    }

    public function show(Project $project)
    {
        //
    }
}
