<x-app-layout>
    <x-slot name="page_title">{{ $page_title ?? 'My Tasks |' }}</x-slot>

    <x-slot name="style">
        <link href="{{ asset('assets/css/vendor/dataTables.bootstrap5.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/vendor/responsive.bootstrap5.css') }}" rel="stylesheet" type="text/css" />
    </x-slot>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('admin-panel/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">My Tasks</li>
                        </ol>
                    </div>
                    
                    <h4 class="page-title">My Task List</h4>
                </div>
            </div>
        </div>
        
        @if ($message = Session::get('success'))
            <div class="alert alert-success" id="notification_alert">
                <p>{{ $message }}</p>
            </div>
        @endif

        <div class="row">
            <div class="row">
                <div class="col-12">
                    <div class="board">
                        <div class="tasks" data-plugin="dragula" data-containers='["pending-task-list", "in-progress-task-list", "review-task-list", "completed-task-list"]'>
                            <h5 class="mt-0 task-header">Pending ({{ $pending_my_tasks->count() }})</h5>
                            
                            <div id="pending-task-list" class="task-list-items">
                                @foreach ($pending_my_tasks as $pending_my_task)
                                    <div class="card mb-0">
                                        <div class="card-body p-3">
                                            <small class="float-end text-muted">Due: {{ $pending_my_task->end_time ? \Carbon\Carbon::parse($pending_my_task->end_time)->format('Y-m-d, h:i A') : '' }}</small>
                                            
                                            @php
                                                if ($pending_my_task->priority == "High") {
                                                    $badge_class = 'badge-danger-lighten';
                                                    $badge_text = 'High';
                                                } 
                                                elseif ($pending_my_task->priority == "Medium") {
                                                    $badge_class = 'badge-info-lighten';
                                                    $badge_text = 'Medium';
                                                }
                                                elseif ($pending_my_task->priority == "Low") {
                                                    $badge_class = 'badge-success-lighten';
                                                    $badge_text = 'Low';
                                                }
                                            @endphp

                                            <span class="badge {{ $badge_class }}">{{ $badge_text }}</span>

                                            <h5 class="mt-2 mb-2">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#task-details-modal" data-task-id="{{ $pending_my_task->id }}" class="text-body open-task-modal">{{ $pending_my_task->title ?? "" }}</a>
                                            </h5>

                                            <p class="mb-0">
                                                <span class="pe-2 text-nowrap mb-2 d-inline-block">
                                                    <i class="mdi mdi-briefcase-outline text-muted"></i>
                                                    {{ $pending_my_task->project->name ?? "" }}
                                                </span>

                                                <span class="text-nowrap mb-2 d-inline-block">
                                                    <i class="mdi mdi-comment-multiple-outline text-muted"></i>
                                                    <b>{{ $pending_my_task->task_comments->count() }}</b> Comments
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="tasks">
                            <h5 class="mt-0 task-header">In Progress ({{ $in_progress_my_tasks->count() }})</h5>
                            
                            <div id="in-progress-task-list" class="task-list-items">
                                @foreach ($in_progress_my_tasks as $in_progress_my_task)
                                    <div class="card mb-0">
                                        <div class="card-body p-3">
                                            <small class="float-end text-muted">Due: {{ $in_progress_my_task->end_time ? \Carbon\Carbon::parse($in_progress_my_task->end_time)->format('Y-m-d, h:i A') : '' }}</small>
                                            
                                            @php
                                                if ($in_progress_my_task->priority == "High") {
                                                    $badge_class = 'badge-danger-lighten';
                                                    $badge_text = 'High';
                                                } 
                                                elseif ($in_progress_my_task->priority == "Medium") {
                                                    $badge_class = 'badge-info-lighten';
                                                    $badge_text = 'Medium';
                                                }
                                                elseif ($in_progress_my_task->priority == "Low") {
                                                    $badge_class = 'badge-success-lighten';
                                                    $badge_text = 'Low';
                                                }
                                            @endphp

                                            <span class="badge {{ $badge_class }}">{{ $badge_text }}</span>

                                            <h5 class="mt-2 mb-2">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#task-details-modal" data-task-id="{{ $in_progress_my_task->id }}" class="text-body open-task-modal">{{ $in_progress_my_task->title ?? "" }}</a>
                                            </h5>

                                            <p class="mb-0">
                                                <span class="pe-2 text-nowrap mb-2 d-inline-block">
                                                    <i class="mdi mdi-briefcase-outline text-muted"></i>
                                                    {{ $in_progress_my_task->project->name ?? "" }}
                                                </span>

                                                <span class="text-nowrap mb-2 d-inline-block">
                                                    <i class="mdi mdi-comment-multiple-outline text-muted"></i>
                                                    <b>{{ $in_progress_my_task->task_comments->count() }}</b> Comments
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="tasks">
                            <h5 class="mt-0 task-header">Review ({{ $review_my_tasks->count() }})</h5>
                            
                            <div id="review-task-list" class="task-list-items">
                                @foreach ($review_my_tasks as $review_my_task)
                                    <div class="card mb-0">
                                        <div class="card-body p-3">
                                            <small class="float-end text-muted">Due: {{ $review_my_task->end_time ? \Carbon\Carbon::parse($review_my_task->end_time)->format('Y-m-d, h:i A') : '' }}</small>
                                            
                                            @php
                                                if ($review_my_task->priority == "High") {
                                                    $badge_class = 'badge-danger-lighten';
                                                    $badge_text = 'High';
                                                } 
                                                elseif ($review_my_task->priority == "Medium") {
                                                    $badge_class = 'badge-info-lighten';
                                                    $badge_text = 'Medium';
                                                }
                                                elseif ($review_my_task->priority == "Low") {
                                                    $badge_class = 'badge-success-lighten';
                                                    $badge_text = 'Low';
                                                }
                                            @endphp

                                            <span class="badge {{ $badge_class }}">{{ $badge_text }}</span>

                                            <h5 class="mt-2 mb-2">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#task-details-modal" data-task-id="{{ $review_my_task->id }}" class="text-body open-task-modal">{{ $review_my_task->title ?? "" }}</a>
                                            </h5>

                                            <p class="mb-0">
                                                <span class="pe-2 text-nowrap mb-2 d-inline-block">
                                                    <i class="mdi mdi-briefcase-outline text-muted"></i>
                                                    {{ $review_my_task->project->name ?? "" }}
                                                </span>

                                                <span class="text-nowrap mb-2 d-inline-block">
                                                    <i class="mdi mdi-comment-multiple-outline text-muted"></i>
                                                    <b>{{ $review_my_task->task_comments->count() }}</b> Comments
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="tasks">
                            <h5 class="mt-0 task-header">Completed ({{ $completed_my_tasks->count() }})</h5>
                            
                            <div id="completed-task-list" class="task-list-items">
                                @foreach ($completed_my_tasks as $completed_my_task)
                                    <div class="card mb-0">
                                        <div class="card-body p-3">
                                            <small class="float-end text-muted">Due: {{ $completed_my_task->end_time ? \Carbon\Carbon::parse($completed_my_task->end_time)->format('Y-m-d, h:i A') : '' }}</small>
                                            
                                            @php
                                                if ($completed_my_task->priority == "High") {
                                                    $badge_class = 'badge-danger-lighten';
                                                    $badge_text = 'High';
                                                } 
                                                elseif ($completed_my_task->priority == "Medium") {
                                                    $badge_class = 'badge-info-lighten';
                                                    $badge_text = 'Medium';
                                                }
                                                elseif ($completed_my_task->priority == "Low") {
                                                    $badge_class = 'badge-success-lighten';
                                                    $badge_text = 'Low';
                                                }
                                            @endphp

                                            <span class="badge {{ $badge_class }}">{{ $badge_text }}</span>

                                            <h5 class="mt-2 mb-2">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#task-details-modal" data-task-id="{{ $completed_my_task->id }}" class="text-body open-task-modal">{{ $completed_my_task->title ?? "" }}</a>
                                            </h5>

                                            <p class="mb-0">
                                                <span class="pe-2 text-nowrap mb-2 d-inline-block">
                                                    <i class="mdi mdi-briefcase-outline text-muted"></i>
                                                    {{ $completed_my_task->project->name ?? "" }}
                                                </span>

                                                <span class="text-nowrap mb-2 d-inline-block">
                                                    <i class="mdi mdi-comment-multiple-outline text-muted"></i>
                                                    <b>{{ $completed_my_task->task_comments->count() }}</b> Comments
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade task-modal-content" id="task-details-modal" tabindex="-1" role="dialog" aria-labelledby="TaskDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="TaskDetailModalLabel"></h4> <span class="badge bg-info ms-2" id="priority">High</span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="p-2">
                        <h5 class="mt-0">Description:</h5>

                        <p class="text-muted mb-4" id="description"></p>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <h5>Start Date</h5>
                                    <p id="start_time"></p>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-4">
                                    <h5>End Date</h5>
                                    <p id="end_time"></p>
                                </div>
                            </div>
                        </div>

                        <ul class="nav nav-tabs nav-bordered mb-3">
                            <li class="nav-item">
                                <a href="#home-b1" data-bs-toggle="tab" aria-expanded="false" class="nav-link active">
                                    Comments
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane show active" id="home-b1">
                                <form id="submit_comment" action="" method="post">
                                    @csrf
                                    
                                    <textarea class="form-control form-control-light mb-2" placeholder="Write message" id="example-textarea" name="text" rows="3" required></textarea>
                                    
                                    <div class="text-end">
                                        <div class="btn-group mb-2 ms-2 d-none d-sm-inline-block">
                                            <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                                        </div>
                                    </div>
                                </form>

                                <div id="task_comments"> </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="script">
        <script src="{{ asset('assets/js/vendor/dragula.min.js') }}"></script>
        <script src="{{ asset('assets/js/ui/component.dragula.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>

        <script type="text/javascript">
            $(document).on('click', '.open-task-modal', function (e) {
                e.preventDefault();
                var task_id = $(this).data('task-id');

                $.ajax({
                    url: '/admin-panel/my-tasks/' + task_id + '/show',
                    method: 'GET',
                    success: function (response) {
                        if (response.success) {
                            $('#TaskDetailModalLabel').text(response.task.title);
                            $('#priority').text(response.task.priority);
                            $('#description').html(response.task.description);

                            var startTime = new Date(response.task.start_time);
                            var endTime = new Date(response.task.end_time);

                            var options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true };
                            
                            $('#start_time').text(startTime.toLocaleString('en-US', options));
                            $('#end_time').text(endTime.toLocaleString('en-US', options));

                            $('#task_comments').html('');

                            response.task_comments.forEach(function (comment) {
                                $('#task_comments').append(`
                                    <div class="d-flex mb-3">
                                        <img class="me-3 avatar-sm rounded-circle" src="${comment.user_profile_image}" alt="Generic placeholder image">
                                        <div class="w-100">
                                            <h5 class="m-0 p-0">${comment.user_name}</h5>
                                            <p class="m-0 p-0">${comment.text}</p>
                                            <small class="text-muted">${comment.created_at}</small>
                                        </div>
                                    </div>
                                `);
                            });

                            var actionUrl = `{{ url('admin-panel/my-tasks/${response.task.id}/submit-comment') }}`;
                            $('#submit_comment').attr('action', actionUrl);
                        }
                        else {
                            alert(response.message);
                        }
                    },
                    error: function () {
                        alert('Failed to load task details.');
                    }
                });
            });

            document.addEventListener('DOMContentLoaded', function () {
                var drake = dragula([
                    document.getElementById('pending-task-list'),
                    document.getElementById('in-progress-task-list'),
                    document.getElementById('review-task-list'),
                    document.getElementById('completed-task-list')
                ]);

                
            });
        </script>
    </x-slot>
</x-app-layout>
