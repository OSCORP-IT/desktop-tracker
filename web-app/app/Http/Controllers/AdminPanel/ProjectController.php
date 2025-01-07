<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectTeamMember;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $projects = Project::query()
            ->with([
                'project_team_members'
            ])
            ->latest()
            ->get();

        return view('admin_panel.projects.index', compact('projects'));
    }

    private function data(Project $project) {
        $users = User::query()
            ->orderBy('name', "asc")
            ->get();

        return [
            'project' => $project,
            'project_team_members' => $users
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('admin_panel.projects.create', $this->data(new Project()) + [
            'team_member_ids' => []
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'overview' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'team_member_ids' => ['required', 'array', 'min:1'],
            'team_member_ids.*' => ['exists:users,id'],
            'thumbnail_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'files.*' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,pdf,doc,docx,zip', 'max:5120'],
        ]);

        if ($request->hasFile('files')) {
            $files_array = [];
        
            foreach ($request->file('files') as $file) {
                $destination_path = 'files/projects/';
                $original_file_name = $file->getClientOriginalName();
                $file->move($destination_path, $original_file_name);
                $files_array[] = $original_file_name;
            }
        
            $json_file = json_encode(array_filter($files_array));
        }
        else {
            $json_file = null;
        }
        

        if ($thumbnail_image = $request->file('thumbnail_image')) {
            $extension = $thumbnail_image->getClientOriginalExtension();

            if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png' || $extension == 'gif') {
                $destination_path = 'images/projects/';
                $thumbnail_image_name = date('YmdHis') . "." . $thumbnail_image->getClientOriginalExtension();
                $thumbnail_image->move($destination_path, $thumbnail_image_name);
                $thumbnail_image_name = "$thumbnail_image_name";
            }
            else {
                $thumbnail_image_name = null;
            }
        }

        $project = Project::create([
            'name' => $validated['name'],
            'overview' => $validated['overview'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'files' => $json_file,
            'thumbnail_image' => ($request->file('thumbnail_image')) ? $thumbnail_image_name : null
        ]);

        if ($request->team_member_ids) {
            foreach($request->team_member_ids as $team_member_id) {
                ProjectTeamMember::create([
                    'project_id' => $project->id,
                    'user_id' => $team_member_id,
                    'status' => "Approved"
                ]);
            }
        }

        return redirect()->to('admin-panel/projects')
            ->with('success', 'Created Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project) {
        return view('admin_panel.projects.show', $this->data($project));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project) {
        $team_member_ids = $project->project_team_members->pluck('id')->toArray();

        return view('admin_panel.projects.edit', $this->data($project) + [
            'team_member_ids' => $team_member_ids
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'overview' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'team_member_ids' => ['required', 'array', 'min:1'],
            'team_member_ids.*' => ['exists:users,id'],
            'thumbnail_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'files.*' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,pdf,doc,docx,zip', 'max:5120'],
        ]);
    
        if ($request->hasFile('files')) {
            $files_array = [];
            foreach ($request->file('files') as $file) {
                $destination_path = 'files/projects/';
                $original_file_name = $file->getClientOriginalName();
                $file->move($destination_path, $original_file_name);
                $files_array[] = $original_file_name;
            }
            $json_file = json_encode(array_filter($files_array));
        } else {
            $json_file = $project->files;
        }
    
        if ($thumbnail_image = $request->file('thumbnail_image')) {
            $destination_path = 'images/projects/';
            $thumbnail_image_name = date('YmdHis') . "." . $thumbnail_image->getClientOriginalExtension();
            $thumbnail_image->move($destination_path, $thumbnail_image_name);
            $project->thumbnail_image = $thumbnail_image_name;
        }
    
        $project->update([
            'name' => $validated['name'],
            'overview' => $validated['overview'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'files' => $json_file,
            'thumbnail_image' => $thumbnail_image ?? $project->thumbnail_image,
        ]);
    
        // Delete old team members and add new ones
        ProjectTeamMember::where('project_id', $project->id)->delete();
        foreach ($validated['team_member_ids'] as $team_member_id) {
            ProjectTeamMember::create([
                'project_id' => $project->id,
                'user_id' => $team_member_id,
                'status' => 'Approved',
            ]);
        }
    
        return redirect()->to('admin-panel/projects')
            ->with('success', 'Updated Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project) {
        ProjectTeamMember::where('project_id', $project->id)->delete();

        $project->delete();

        return redirect()->to('admin-panel/projects')
            ->with('success', 'Deleted Successfully.');
    }

    public function fetch_project_team_members_by_project_id(Request $request) {
        $request->validate([
            'project_id' => ['required', 'numeric'],
        ]);

        $project = Project::find($request->project_id);

        if ($project) {
            return response()->json([
                'success' => true,
                'message' => "No upcoming meetings found.",
                'project_team_members' => $project->project_team_members
            ], 200);
        }
        else {
            return response()->json([
                'success' => true,
                'message' => "Data not found!!!",
                'project_team_members' => []
            ], 200);
        }
    }
}
