<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Mail\TaskAssigned;
use Illuminate\Support\Facades\Mail;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $tasks = Task::get();
        $today = Carbon::today();
        
        $today_tasks = $tasks->filter(function($task) use ($today) {
            return Carbon::parse($task->start_time)->isSameDay($today);
        });

        $upcoming_tasks = $tasks->filter(function($task) use ($today) {
            return Carbon::parse($task->start_time)->isAfter($today);
        });

        $others_tasks = $tasks->filter(function($task) use ($today) {
            return Carbon::parse($task->start_time)->isBefore($today) && !Carbon::parse($task->start_time)->isSameDay($today);
        });
    
        return view('admin_panel.tasks.index', compact('today_tasks', 'upcoming_tasks', 'others_tasks'));
    }

    private function data(Task $task) {
        $priorities = Task::$priorities;

        $projects = Project::query()
            ->orderBy('name', "asc")
            ->get();

        $project_team_members = User::query()
            ->orderBy('name', "asc")
            ->get();

        return [
            'task' => $task,
            'priorities' => $priorities,
            'projects' => $projects,
            'project_team_members' => $project_team_members
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('admin_panel.tasks.create', $this->data(new Task()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'priority' => ["required", "in:" . implode(',', Task::$priorities)],
            'description' => ['nullable', 'string'],
            'assigned_to' => ['required', 'exists:users,id'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after_or_equal:start_time'],
        ]);

        $orderNumber = Task::max('order_number') + 1;
    
        $task = Task::create([
            'project_id' => $validated['project_id'],
            'title' => $validated['title'],
            'priority' => $validated['priority'],
            'order_number' => $orderNumber,
            'description' => $validated['description'] ?? null,
            'assigned_to' => $validated['assigned_to'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'status' => 'Pending',
        ]);

        $user = User::find($validated['assigned_to']);

        Mail::to($user->email)->send(new TaskAssigned($task));
    
        return redirect()->to('admin-panel/tasks')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task) {
        return view('admin_panel.tasks.show', $this->data($task));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task) {
        return view('admin_panel.tasks.edit', $this->data($task));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task) {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'priority' => ["required", "in:" . implode(',', Task::$priorities)],
            'description' => ['nullable', 'string'],
            'assigned_to' => ['required', 'exists:users,id'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after_or_equal:start_time'],
        ]);
    
        $task->update([
            'project_id' => $validated['project_id'],
            'title' => $validated['title'],
            'priority' => $validated['priority'],
            'description' => $validated['description'] ?? null,
            'assigned_to' => $validated['assigned_to'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);
    
        return redirect()->to('admin-panel/tasks')
            ->with('success', 'Task updated successfully.');
    }    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task) {
        $task->delete();

        return redirect()->to('admin-panel/tasks')
            ->with('success', 'Task deleted successfully.');
    }
}
