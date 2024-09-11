<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
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

    public function edit(Project $project)
    {
        return Inertia::render('ProjectEditor', [
            'selectedProject' => $project
        ]);
    }


    public function create()
    {
        return Inertia::render('ProjectMaker');
    }


    public function store(StoreProjectRequest $request): RedirectResponse
    {
        Project::query()->create($request->validated());
        return redirect(route('dashboard'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        //
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string'
        ]);

        $project->update($validated);

        return redirect(route('dashboard'));
    }

    /**
     * Remove the specified resource from storage.
     */
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
