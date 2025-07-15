<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Project;
use App\Models\ProjectTeamMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $projects = Project::query()
            ->latest()
            ->get();

        return view('admin_panel.projects.index', compact('projects'));
    }

    private function data(Project $project) {
        $users = User::query()
            ->orderBy('employee_id', 'asc')
            ->get();
            
        $status = Project::$status;

        $project_team_member_roles = ProjectTeamMember::$roles;

        return [
            'project' => $project,
            'users' => $users,
            'status' => $status,
            'project_team_member_roles' => $project_team_member_roles
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('admin_panel.projects.create', $this->data(new Project()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validated_data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'manager_id' => ['required', 'numeric', 'exists:users,id'],
            'overview' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'budget' => ['nullable', 'numeric'],
            'thumbnail_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'input_status' => ['required', 'in:' . implode(',', Project::$status)],
            'project_team_members' => ['nullable', 'array'],
            'project_team_members.*.id' => ['required', 'numeric', 'exists:users,id'],
            'project_team_members.*.role' => ['required', 'in:' . implode(',', ProjectTeamMember::$roles)],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['required', 'file', 'mimes:jpeg,png,jpg,gif,pdf,doc,docx,zip', 'max:5120'],
        ]);

        DB::beginTransaction();

        try {
            $thumbnail_image_name = null;
            if ($request->hasFile('thumbnail_image') && $request->file('thumbnail_image')->isValid()) {
                $thumbnail_image = $request->file('thumbnail_image');
                $destination_path = 'images/projects/';
                $thumbnail_image_name = date('YmdHis') . '.' . $thumbnail_image->getClientOriginalExtension();
                $thumbnail_image->move(public_path($destination_path), $thumbnail_image_name);
                $thumbnail_image_name = $destination_path . $thumbnail_image_name; // Store full path
            }

            // Create the project
            $project = Project::create([
                'name' => $validated_data['name'],
                'manager_id' => $validated_data['manager_id'],
                'overview' => $validated_data['overview'] ?? null,
                'start_date' => $validated_data['start_date'],
                'end_date' => $validated_data['end_date'],
                'budget' => $validated_data['budget'] ?? null,
                'thumbnail_image' => $thumbnail_image_name,
                'status' => $validated_data['input_status'],
                'created_by_id' => auth()->id(),
            ]);

            // Add project team members
            if (!empty($validated_data['project_team_members'])) {
                foreach ($validated_data['project_team_members'] as $member) {
                    ProjectTeamMember::create([
                        'project_id' => $project->id,
                        'assigned_to_id' => $member['id'],
                        'role' => $member['role'],
                    ]);
                }
            }

            // Handle attachments
            if (!empty($validated_data['attachments'])) {
                foreach ($request->file('attachments') as $attachment) {
                    if ($attachment->isValid()) {
                        $destination_path = 'attachments/projects/';
                        $file_name = date('YmdHis') . '_' . uniqid() . '.' . $attachment->getClientOriginalExtension();
                        $attachment->move(public_path($destination_path), $file_name);
                        $file_path = $destination_path . $file_name;

                        Attachment::create([
                            'attachmentable_id' => $project->id,
                            'attachmentable_type' => "App\Models\Project",
                            'file_path' => $file_path,
                            'original_name' => $attachment->getClientOriginalName(),
                            'mime_type' => $attachment->getClientMimeType(),
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json(['success' => 'Created successfully.']);
        }
        catch (\Exception $e) {
            DB::rollback();

            return response()->json(['error' => 'An error occurred while createing the data. Please try again. ' . $e->getMessage()], 422);
        }
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
        return view('admin_panel.projects.edit', $this->data($project));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project) {
        $validated_data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'manager_id' => ['required', 'numeric', 'exists:users,id'],
            'overview' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'budget' => ['nullable', 'numeric'],
            'thumbnail_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'input_status' => ['required', 'in:' . implode(',', Project::$status)],
            'project_team_members' => ['nullable', 'array'],
            'project_team_members.*.id' => ['required', 'numeric', 'exists:users,id'],
            'project_team_members.*.role' => ['required', 'in:' . implode(',', ProjectTeamMember::$roles)],
            'existing_attachments' => ['nullable', 'array'],
            'existing_attachments.*.id' => ['numeric', 'exists:attachments,id'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,pdf,doc,docx,zip', 'max:5120'],
        ]);

        DB::beginTransaction();

        try {
            // Handle thumbnail image
            $thumbnail_image_name = $project->thumbnail_image;
            if ($request->hasFile('thumbnail_image') && $request->file('thumbnail_image')->isValid()) {
                // Delete old thumbnail if exists
                if ($thumbnail_image_name && file_exists(public_path($thumbnail_image_name))) {
                    unlink(public_path($thumbnail_image_name));
                }
                $thumbnail_image = $request->file('thumbnail_image');
                $destination_path = 'images/projects/';
                $thumbnail_image_name = date('YmdHis') . '.' . $thumbnail_image->getClientOriginalExtension();
                $thumbnail_image->move(public_path($destination_path), $thumbnail_image_name);
                $thumbnail_image_name = $destination_path . $thumbnail_image_name;
            }

            // Update project details
            $project->update([
                'name' => $validated_data['name'],
                'manager_id' => $validated_data['manager_id'],
                'overview' => $validated_data['overview'] ?? null,
                'start_date' => $validated_data['start_date'],
                'end_date' => $validated_data['end_date'],
                'budget' => $validated_data['budget'] ?? null,
                'thumbnail_image' => $thumbnail_image_name,
                'status' => $validated_data['input_status'],
            ]);

            // Handle team members
            $existing_member_ids = $project->project_team_members->pluck('id')->toArray();
            $new_member_ids = array_column($validated_data['project_team_members'] ?? [], 'id');

            // Remove team members not in the new list
            ProjectTeamMember::where('project_id', $project->id)
                ->whereNotIn('assigned_to_id', $new_member_ids)
                ->delete();

            // Add or update team members
            if (!empty($validated_data['project_team_members'])) {
                foreach ($validated_data['project_team_members'] as $member) {
                    ProjectTeamMember::updateOrCreate(
                        [
                            'project_id' => $project->id,
                            'assigned_to_id' => $member['id'],
                        ],
                        [
                            'role' => $member['role'],
                        ]
                    );
                }
            }

            // Handle attachments
            $existing_attachment_ids = $project->attachments->pluck('id')->toArray();
            $submitted_attachment_ids = array_column($validated_data['existing_attachments'] ?? [], 'id');

            // Delete attachments not in the submitted list
            $attachments_to_delete = array_diff($existing_attachment_ids, $submitted_attachment_ids);
            foreach ($attachments_to_delete as $attachment_id) {
                $attachment = Attachment::find($attachment_id);
                if ($attachment && file_exists(public_path($attachment->file_path))) {
                    unlink(public_path($attachment->file_path));
                }
                $attachment?->delete();
            }

            // Add new attachments
            if (!empty($validated_data['attachments'])) {
                foreach ($request->file('attachments') as $attachment) {
                    if ($attachment && $attachment->isValid()) {
                        $destination_path = 'attachments/projects/';
                        $file_name = date('YmdHis') . '_' . uniqid() . '.' . $attachment->getClientOriginalExtension();
                        $attachment->move(public_path($destination_path), $file_name);
                        $file_path = $destination_path . $file_name;

                        Attachment::create([
                            'attachmentable_id' => $project->id,
                            'attachmentable_type' => "App\Models\Project",
                            'file_path' => $file_path,
                            'original_name' => $attachment->getClientOriginalName(),
                            'mime_type' => $attachment->getClientMimeType(),
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json(['success' => 'Updated successfully.']);
        }
        catch (\Exception $e) {
            DB::rollback();

            return response()->json(['error' => 'An error occurred while updating the data. Please try again. ' . $e->getMessage()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project) {
        DB::beginTransaction();

        try {
            // Delete thumbnail image if exists
            if ($project->thumbnail_image && file_exists(public_path($project->thumbnail_image))) {
                unlink(public_path($project->thumbnail_image));
            }

            // Delete attachments and their files
            foreach ($project->attachments as $attachment) {
                if (file_exists(public_path($attachment->file_path))) {
                    unlink(public_path($attachment->file_path));
                }
                $attachment->delete();
            }

            // Delete related records
            $project->project_team_members()->delete();
            $project->tasks()->delete();
            $project->comments()->delete();

            // Finally delete the project
            $project->delete();

            DB::commit();

            return redirect()->to('admin-panel/projects')
                ->with('success', 'Deleted Successfully.');
        } 
        catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'An error occurred while deleting the record. ' . $e->getMessage());
        }
    }
}
