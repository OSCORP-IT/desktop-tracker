<x-app-layout>
    <x-slot name="page_title">{{ $page_title ?? 'Project Details |' }}</x-slot>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('admin-panel/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('admin-panel/projects') }}">Projects</a></li>
                            <li class="breadcrumb-item active">Details</li>
                        </ol>
                    </div>

                    <h4 class="page-title">Project Details</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xxl-8 col-lg-6">
                <!-- project card -->
                <div class="card d-block">
                    <div class="card-body">
                        <div class="dropdown float-end">
                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="dripicons-dots-3"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a href="{{ url('admin-panel/projects/' . $project->id . '/edit') }}" class="dropdown-item"><i class="mdi mdi-pencil me-1"></i>Edit</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item" onclick="confirmDelete({{ $project->id }})"><i class="mdi mdi-delete me-1"></i>Delete</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item"><i class="mdi mdi-email-outline me-1"></i>Invite</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item"><i class="mdi mdi-exit-to-app me-1"></i>Leave</a>
                            </div>
                        </div>
                        <!-- project title-->
                        <h3 class="mt-0">{{ $project->name }}</h3>
                        <div class="badge bg-secondary text-light mb-3">{{ $project->status }}</div>

                        <h5>Project Overview:</h5>
                        <p class="text-muted mb-4">
                            {{ $project->overview ?? 'No overview provided' }}
                        </p>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <h5>Start Date</h5>
                                    <p>{{ $project->start_date->format('d F Y') }} <small class="text-muted">{{ $project->start_date->format('h:i A') }}</small></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <h5>End Date</h5>
                                    <p>{{ $project->end_date->format('d F Y') }} <small class="text-muted">{{ $project->end_date->format('h:i A') }}</small></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-4">
                                    <h5>Budget</h5>
                                    <p>{{ $project->budget ? '$' . number_format($project->budget, 2) : 'Not specified' }}</p>
                                </div>
                            </div>
                        </div>

                        <div id="tooltip-container">
                            <h5>Team Members:</h5>
                            @if ($project->project_team_members->isNotEmpty())
                                @foreach ($project->project_team_members as $member)
                                    <a href="javascript:void(0);" data-bs-container="#tooltip-container" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $member->member->name }} ({{ $member->role }})" class="d-inline-block">
                                        <img src="{{ $member->member->profile_image ? url('images/users', $member->member->profile_image) : asset('hyper/images/avator.png') }}" class="rounded-circle img-thumbnail avatar-sm" alt="{{ $member->name }}">
                                    </a>
                                @endforeach
                            @else
                                <p>No team members assigned</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-xxl-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Files</h5>
                        @if ($project->attachments->isNotEmpty())
                            @foreach ($project->attachments as $attachment)
                                <div class="card mb-1 shadow-none border">
                                    <div class="p-2">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                @if (in_array($attachment->mime_type, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif']))
                                                    <img src="{{ url($attachment->file_path) }}" class="avatar-sm rounded" alt="{{ $attachment->original_name }}">
                                                @else
                                                    <div class="avatar-sm">
                                                        <span class="avatar-title rounded">
                                                            {{ strtoupper(pathinfo($attachment->original_name, PATHINFO_EXTENSION)) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col ps-0">
                                                <a href="{{ url($attachment->file_path) }}" class="text-muted fw-bold" target="_blank">{{ $attachment->original_name }}</a>
                                                <p class="mb-0">{{ round(filesize(public_path($attachment->file_path)) / 1024 / 1024, 2) }} MB</p>
                                            </div>
                                            <div class="col-auto">
                                                <a href="{{ url($attachment->file_path) }}" class="btn btn-link btn-lg text-muted" download>
                                                    <i class="dripicons-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p>No attachments uploaded</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="script">
        <script type="text/javascript">
            function confirmDelete(projectId) {
                if (confirm('Are you sure you want to delete this project?')) {
                    $.ajax({
                        url: '{{ url("admin-panel/projects") }}/' + projectId,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                alert('Project deleted successfully.');
                                window.location.href = '{{ url("admin-panel/projects") }}';
                            }
                        },
                        error: function(xhr) {
                            alert('An error occurred while deleting the project. Please try again.');
                        }
                    });
                }
            }
        </script>
    </x-slot>
</x-app-layout>
