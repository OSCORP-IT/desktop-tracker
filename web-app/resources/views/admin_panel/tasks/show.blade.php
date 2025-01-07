<x-app-layout>
    <x-slot name="page_title">{{ $page_title ?? 'Task Show |' }}</x-slot>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('admin-panel/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('admin-panel/tasks') }}"> Tasks </a></li>
                            <li class="breadcrumb-item active">Show</li>
                        </ol>
                    </div>

                    <h4 class="page-title">Task Show</h4>
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
                        <div class="dropdown card-widgets">
                            <a href="#" class="dropdown-toggle arrow-none" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="uil uil-ellipsis-h"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" style="">
                                <a href="{{ url('admin-panel/tasks/'. $task->id . '/edit') }}" class="dropdown-item">
                                    <i class="uil uil-edit me-1"></i>Edit
                                </a>
                                <a href="#" class="dropdown-item text-danger">
                                    <i class="uil uil-trash-alt me-1"></i>Delete
                                </a>
                            </div>
                        </div>
                        
                        <div class="clearfix"></div>

                        <h3 class="mt-3">{{ $task->title ?? "" }}</h3>

                        <div class="row">
                            <div class="col-6">
                                <p class="mt-2 mb-1 text-muted fw-bold font-12 text-uppercase">Assigned To</p>

                                <div class="d-flex">
                                    <img src="{{ $task->assigned_user->profile_image ? url('images/users', $task->assigned_user->profile_image) : asset('assets/images/avator.png') }}" alt="image" class="rounded-circle me-2" height="24">

                                    <div>
                                        <h5 class="mt-1 font-14">
                                            {{ $task->assigned_user->name ?? "" }}
                                        </h5>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <p class="mt-2 mb-1 text-muted fw-bold font-12 text-uppercase">Date</p>
                                <div class="d-flex">
                                    <i class="uil uil-schedule font-18 text-success me-1"></i>
                                    <div>
                                        <h5 class="mt-1 font-14">
                                            {{ $task->start_time ? \Carbon\Carbon::parse($task->start_time)->format('Y-m-d, h:i A') : '' }} - 
                                            {{ $task->end_time ? \Carbon\Carbon::parse($task->end_time)->format('Y-m-d, h:i A') : '' }}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-3">Description:</h5>

                        <div>
                            {!! $task->description ?? "" !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
