<x-app-layout>
    <x-slot name="page_title">{{ $page_title ?? 'Project Show |' }}</x-slot>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('admin-panel/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('admin-panel/projects') }}"> Projects </a></li>
                            <li class="breadcrumb-item active">Show</li>
                        </ol>
                    </div>

                    <h4 class="page-title">Project Show</h4>
                </div>
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success" id="notification_alert">
                <p>{{ $message }}</p>
            </div>
        @endif

        <div class="row">
            <div class="col-xxl-8 col-lg-6">
                <div class="card d-block">
                    <div class="card-body">
                        <div class="dropdown float-end">
                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="dripicons-dots-3"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ url('admin-panel/projects/'. $project->id . '/edit') }}" class="dropdown-item"><i class="mdi mdi-pencil me-1"></i>Edit</a>
                                <a href="#" class="dropdown-item"><i class="mdi mdi-delete me-1"></i>Delete</a>
                            </div>
                        </div>

                        <h3 class="mt-0">
                            {{ $project->name ?? "" }}
                        </h3>

                        @php
                            $now = now();
                            $badge_class = '';
                            $badge_text = '';
        
                            if ($now->lt($project->start_date)) {
                                $badge_class = 'bg-warning text-dark';
                                $badge_text = 'Upcoming';
                            } 
                            elseif ($now->between($project->start_date, $project->end_date)) {
                                $badge_class = 'bg-secondary text-light';
                                $badge_text = 'Ongoing';
                            } 
                            else {
                                $badge_class = 'bg-success text-light';
                                $badge_text = 'Finished';
                            }
                        @endphp
                        
                        <div class="badge {{ $badge_class }} mb-3">{{ $badge_text }}</div>

                        <h5>Project Overview:</h5>

                        <p class="text-muted mb-2">
                            {{ $project->overview ?? "" }}
                        </p>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <h5>Start Date</h5>
                                    <p>{{ Carbon\Carbon::parse($project->start_date)->format('d M Y') }}</p>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-4">
                                    <h5>End Date</h5>
                                    <p>{{ Carbon\Carbon::parse($project->end_date)->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <div id="tooltip-container">
                            <h5>Team Members:</h5>

                            @foreach($project->project_team_members as $member)
                                <a href="{{ url('admin-panel/users/'. $member->id . '') }}" data-bs-container="#tooltip-container" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $member->name }}" class="d-inline-block">
                                    <img src="{{ $member->profile_image ? url('images/users', $member->profile_image) : asset('assets/images/avator.png') }}" class="rounded-circle img-thumbnail avatar-sm" alt="{{ $member->name }}">
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-xxl-4">
                {{-- <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Progress</h5>

                        <div dir="ltr">
                            <div class="mt-3 chartjs-chart" style="height: 320px;"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                                <canvas id="line-chart-example" width="468" style="display: block; width: 468px; height: 320px;" class="chartjs-render-monitor" height="320"></canvas>
                            </div>
                        </div>
                    </div>
                </div> --}}

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Files</h5>

                        @php
                            $files = ($project->files) ? json_decode($project->files, true) : [];
                        @endphp

                        @foreach($files as $file)
                            <div class="card mb-1 shadow-none border">
                                <div class="p-2">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <div class="avatar-sm">
                                                <span class="avatar-title rounded">
                                                    {{ strtoupper(pathinfo($file, PATHINFO_EXTENSION)) }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="col ps-0">
                                            <a href="{{ url('files/projects/' . $file) }}" class="text-muted fw-bold">{{ $file }}</a>
                                            @php
                                                $filePath = public_path('files/projects/' . $file);
                                                $fileSize = file_exists($filePath) ? filesize($filePath) : 0;
                                            @endphp
                                            <p class="mb-0">{{ number_format($fileSize / 1024, 2) }} KB</p>
                                        </div>

                                        <div class="col-auto">
                                            <a href="{{ url('files/projects/' . $file) }}" class="btn btn-link btn-lg text-muted" download>
                                                <i class="dripicons-download"></i>
                                            </a>
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
</x-app-layout>
