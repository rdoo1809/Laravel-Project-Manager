<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        return Inertia::render('ProjectMaker');
    }

    public function store(StoreProjectRequest $request)
    {
        $user = $request->user();

        $project = Project::query()->create($request->validated());
        $project->assignees()->attach($user);

        return redirect(route('dashboard'));
    }

    public function edit(Project $project)
    {
        return Inertia::render('ProjectEditor', [
            'selectedProject' => $project
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string'
        ]);

        $project->update($validated);

        return redirect(route('dashboard'));
    }

    public function show(Project $project)
    {
        //
    }

    public function destroy(Project $project)
    {
        //
        $project->delete();

        //How to refresh??
//        $projects = Project::all();
//        return Inertia::render('dashboard', [
//            'projects' => $projects
//        ]);
        //return Inertia::location(route('dashboard'));
    }
}
