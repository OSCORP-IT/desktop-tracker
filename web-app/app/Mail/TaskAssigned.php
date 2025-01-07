<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public $task;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Task $task) {
        $this->task = $task;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->view('emails.task_assigned')
            ->subject('New Task Assigned')
            ->with([
                'taskTitle' => $this->task->title,
                'startTime' => $this->task->start_time,
                'endTime' => $this->task->end_time,
                'description' => $this->task->description,
            ]);
    }
}
