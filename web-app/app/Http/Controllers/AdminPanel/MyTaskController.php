<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskComments;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyTaskController extends Controller
{
    public function index() {
        $pending_my_tasks = Task::query()
            ->where('status', "Pending")
            ->get();
        
        $in_progress_my_tasks = Task::query()
            ->where('status', "In Progress")
            ->get();

        $review_my_tasks = Task::query()
            ->where('status', "Review")
            ->get();

        $completed_my_tasks = Task::query()
            ->whereIn('status', ["Completed", "Late Completion"])
            ->get();

        return view('admin_panel.my_tasks.index', compact('pending_my_tasks', 'in_progress_my_tasks', 'review_my_tasks', 'completed_my_tasks'));
    }

    public function show(Task $task) {
        $task_comments = TaskComments::query()
            ->where('task_id', $task->id)
            ->get()
            ->map(function($task_comment) {
                return [
                    'id' => $task_comment->id,
                    'user_name' => $task_comment->user->name,
                    'user_profile_image' => ($task_comment->user->profile_image) ? url('images/users', $task_comment->user->profile_image) : asset('assets/images/avator.png'),
                    'text' => $task_comment->text,
                    'created_at' => ($task_comment->created_at) ? Carbon::parse($task_comment->created_at)->format('Y-m-d, h:i A') : '',
                ];
            });

        return response()->json([
            'success' => true,
            'message' => "Updated successfully.",
            'task' => $task,
            'task_comments' => $task_comments
        ], 200);
    }

    public function submit_comment(Request $request, Task $task) {
        $request->validate([
            'text' => ['required', 'string']
        ]);

        TaskComments::create([
            'task_id' => $task->id,
            'task_comment_id' => null,
            'user_id' => Auth::user()->id,
            'text' => $request->text,
            'attachment' => null
        ]);

        return back()
            ->with('success', 'Comment successfully.');
    }

    public function change_status(Request $request, Task $task) {
        $request->validate([
            'status' => ["required", "in:" . implode(',', Task::$status)],
        ]);

        $task->update([
            'status' => $request->status
        ]);

        return back()
            ->with('success', 'Updated successfully.');
    }
}
