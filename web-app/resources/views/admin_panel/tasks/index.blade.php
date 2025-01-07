<x-app-layout>
    <x-slot name="page_title">{{ $page_title ?? 'Tasks |' }}</x-slot>

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
                            <li class="breadcrumb-item active">Tasks</li>
                        </ol>
                    </div>
                    
                    <h4 class="page-title">Task List</h4>
                </div>
            </div>
        </div>
        
        @if ($message = Session::get('success'))
            <div class="alert alert-success" id="notification_alert">
                <p>{{ $message }}</p>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-sm-4">
                                <a href="{{ url('admin-panel/tasks/create') }}" class="btn btn-danger mb-2"><i class="mdi mdi-plus-circle me-2"></i> Add Task</a>
                            </div>
                        </div>

                        <div class="mt-2">
                            <h5 class="m-0 pb-2">
                                <a class="text-dark" data-bs-toggle="collapse" href="#todayTasks" role="button" aria-expanded="false" aria-controls="todayTasks">
                                    <i class="uil uil-angle-down font-18"></i>Today <span class="text-muted">({{ $today_tasks->count() }})</span>
                                </a>
                            </h5>

                            <div class="collapse show" id="todayTasks">
                                <div class="card mb-0">
                                    <div class="card-body">

                                        @foreach ($today_tasks as $today_task)
                                            <div class="row justify-content-sm-between">
                                                <div class="col-sm-6 mb-2 mb-sm-0">
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                            <a href="{{ url('admin-panel/tasks/'. $today_task->id . '') }}" class="text-title">{{ $today_task->title ?? "" }}</a>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <div class="d-flex justify-content-between">
                                                        <div id="tooltip-container">
                                                            <img src="{{ $today_task->assigned_user->profile_image ? url('images/users', $today_task->assigned_user->profile_image) : asset('assets/images/avator.png') }}" alt="image" class="avatar-xs rounded-circle me-1" data-bs-container="#tooltip-container" data-bs-toggle="tooltip" data-bs-placement="bottom" title="" data-bs-original-title="{{ $today_task->assigned_user->name ?? "" }}" aria-label="{{ $today_task->assigned_user->name ?? "" }}">
                                                        </div>

                                                        <div>
                                                            <ul class="list-inline font-13 text-end">
                                                                <li class="list-inline-item ms-2">
                                                                    <span class="text-success p-1">{{ $today_task->status ?? "" }}</span>
                                                                </li>

                                                                <li class="list-inline-item">
                                                                    <i class="uil uil-schedule font-16 me-1"></i>
                                                                    {{ $today_task->start_time ? \Carbon\Carbon::parse($today_task->start_time)->format('Y-m-d, h:i A') : '' }} - 
                                                                    {{ $today_task->end_time ? \Carbon\Carbon::parse($today_task->end_time)->format('Y-m-d, h:i A') : '' }}
                                                                </li>

                                                                <li class="list-inline-item ms-1">
                                                                    <i class="uil uil-comment-message font-16 me-1"></i> {{ $today_task->task_comments->count() }}
                                                                </li>

                                                                <li class="list-inline-item ms-2">
                                                                    @php
                                                                        if ($today_task->priority == "High") {
                                                                            $badge_class = 'badge-danger-lighten';
                                                                            $badge_text = 'High';
                                                                        } 
                                                                        elseif ($today_task->priority == "Medium") {
                                                                            $badge_class = 'badge-info-lighten';
                                                                            $badge_text = 'Medium';
                                                                        }
                                                                        elseif ($today_task->priority == "Low") {
                                                                            $badge_class = 'badge-success-lighten';
                                                                            $badge_text = 'Low';
                                                                        }
                                                                    @endphp

                                                                    <span class="badge {{ $badge_class }} p-1">{{ $badge_text }}</span>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-2">
                            <h5 class="m-0 pb-2">
                                <a class="text-dark" data-bs-toggle="collapse" href="#UpcomingTasks" role="button" aria-expanded="false" aria-controls="UpcomingTasks">
                                    <i class="uil uil-angle-down font-18"></i>Upcoming <span class="text-muted">({{ $today_tasks->count() }})</span>
                                </a>
                            </h5>

                            <div class="collapse show" id="UpcomingTasks">
                                <div class="card mb-0">
                                    <div class="card-body">

                                        @foreach ($upcoming_tasks as $upcoming_task)
                                            <div class="row justify-content-sm-between">
                                                <div class="col-sm-6 mb-2 mb-sm-0">
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                            <a href="{{ url('admin-panel/tasks/'. $upcoming_task->id . '') }}" class="text-title">{{ $upcoming_task->title ?? "" }}</a>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <div class="d-flex justify-content-between">
                                                        <div id="tooltip-container">
                                                            <img src="{{ $upcoming_task->assigned_user->profile_image ? url('images/users', $upcoming_task->assigned_user->profile_image) : asset('assets/images/avator.png') }}" alt="image" class="avatar-xs rounded-circle me-1" data-bs-container="#tooltip-container" data-bs-toggle="tooltip" data-bs-placement="bottom" title="" data-bs-original-title="{{ $upcoming_task->assigned_user->name ?? "" }}" aria-label="{{ $upcoming_task->assigned_user->name ?? "" }}">
                                                        </div>

                                                        <div>
                                                            <ul class="list-inline font-13 text-end">
                                                                <li class="list-inline-item ms-2">
                                                                    <span class="text-success p-1">{{ $upcoming_task->status ?? "" }}</span>
                                                                </li>

                                                                <li class="list-inline-item">
                                                                    <i class="uil uil-schedule font-16 me-1"></i>
                                                                    {{ $upcoming_task->start_time ? \Carbon\Carbon::parse($upcoming_task->start_time)->format('Y-m-d, h:i A') : '' }} - 
                                                                    {{ $upcoming_task->end_time ? \Carbon\Carbon::parse($upcoming_task->end_time)->format('Y-m-d, h:i A') : '' }}
                                                                </li>

                                                                <li class="list-inline-item ms-1">
                                                                    <i class="uil uil-comment-message font-16 me-1"></i> {{ $upcoming_task->task_comments->count() }}
                                                                </li>

                                                                <li class="list-inline-item ms-2">
                                                                    @php
                                                                        if ($upcoming_task->priority == "High") {
                                                                            $badge_class = 'badge-danger-lighten';
                                                                            $badge_text = 'High';
                                                                        } 
                                                                        elseif ($upcoming_task->priority == "Medium") {
                                                                            $badge_class = 'badge-info-lighten';
                                                                            $badge_text = 'Medium';
                                                                        }
                                                                        elseif ($upcoming_task->priority == "Low") {
                                                                            $badge_class = 'badge-success-lighten';
                                                                            $badge_text = 'Low';
                                                                        }
                                                                    @endphp

                                                                    <span class="badge {{ $badge_class }} p-1">{{ $badge_text }}</span>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-2">
                            <h5 class="m-0 pb-2">
                                <a class="text-dark" data-bs-toggle="collapse" href="#OthersTasks" role="button" aria-expanded="false" aria-controls="OthersTasks">
                                    <i class="uil uil-angle-down font-18"></i>Others <span class="text-muted">({{ $others_tasks->count() }})</span>
                                </a>
                            </h5>

                            <div class="collapse show" id="OthersTasks">
                                <div class="card mb-0">
                                    <div class="card-body">
                                        @foreach ($others_tasks as $others_task)
                                            <div class="row justify-content-sm-between">
                                                <div class="col-sm-6 mb-2 mb-sm-0">
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                            <a href="{{ url('admin-panel/tasks/'. $others_task->id . '') }}" class="text-title">{{ $others_task->title ?? "" }}</a>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">
                                                    <div class="d-flex justify-content-between">
                                                        <div id="tooltip-container">
                                                            <img src="{{ $others_task->assigned_user->profile_image ? url('images/users', $others_task->assigned_user->profile_image) : asset('assets/images/avator.png') }}" alt="image" class="avatar-xs rounded-circle me-1" data-bs-container="#tooltip-container" data-bs-toggle="tooltip" data-bs-placement="bottom" title="" data-bs-original-title="{{ $others_task->assigned_user->name ?? "" }}" aria-label="{{ $others_task->assigned_user->name ?? "" }}">
                                                        </div>

                                                        <div>
                                                            <ul class="list-inline font-13 text-end">
                                                                <li class="list-inline-item ms-2">
                                                                    <span class="text-success p-1">{{ $others_task->status ?? "" }}</span>
                                                                </li>

                                                                <li class="list-inline-item">
                                                                    <i class="uil uil-schedule font-16 me-1"></i>
                                                                    {{ $others_task->start_time ? \Carbon\Carbon::parse($others_task->start_time)->format('Y-m-d, h:i A') : '' }} - 
                                                                    {{ $others_task->end_time ? \Carbon\Carbon::parse($others_task->end_time)->format('Y-m-d, h:i A') : '' }}
                                                                </li>

                                                                <li class="list-inline-item ms-1">
                                                                    <i class="uil uil-comment-message font-16 me-1"></i> {{ $others_task->task_comments->count() }}
                                                                </li>

                                                                <li class="list-inline-item ms-2">
                                                                    @php
                                                                        if ($others_task->priority == "High") {
                                                                            $badge_class = 'badge-danger-lighten';
                                                                            $badge_text = 'High';
                                                                        } 
                                                                        elseif ($others_task->priority == "Medium") {
                                                                            $badge_class = 'badge-info-lighten';
                                                                            $badge_text = 'Medium';
                                                                        }
                                                                        elseif ($others_task->priority == "Low") {
                                                                            $badge_class = 'badge-success-lighten';
                                                                            $badge_text = 'Low';
                                                                        }
                                                                    @endphp

                                                                    <span class="badge {{ $badge_class }} p-1">{{ $badge_text }}</span>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
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
        </div>
    </div>

    <x-slot name="script">
        <script src="{{ asset('assets/js/vendor/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/dataTables.bootstrap5.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/responsive.bootstrap5.min.js') }}"></script>

        <script src="{{ asset('assets/js/pages/demo.datatable-init.js') }}"></script>
        <script src="{{ asset('assets/js/sweetalert2@11') }}"></script>

        <script type="text/javascript">
            $(document).ready(function() {
                $('#DataTable').DataTable();

                $('#notification_alert').delay(3000).fadeOut('slow');

                $('.show_confirm').click(function(event) {
                    var form =  $(this).closest("form");
                    var name = $(this).data("name");

                    event.preventDefault();

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You want to delete this item ?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'No, cancel!',
                        reverseButtons: true
                    })
                    .then((willDelete) => {
                        if (willDelete.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        </script>
    </x-slot>
</x-app-layout>
