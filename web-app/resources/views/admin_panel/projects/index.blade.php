<x-app-layout>
    <x-slot name="page_title">{{ $page_title ?? 'Projects |' }}</x-slot>

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
                            <li class="breadcrumb-item active">Projects</li>
                        </ol>
                    </div>
                    
                    <h4 class="page-title">Project List</h4>
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
                                <a href="{{ url('admin-panel/projects/create') }}" class="btn btn-danger mb-2"><i class="mdi mdi-plus-circle me-2"></i> Add Project</a>
                            </div>
                        </div>

                        <div class="row">
                            @foreach ($projects as $project)
                                <div class="col-md-6 col-xxl-3">
                                    <div class="card d-block">
                                        @if ($project->thumbnail_image)
                                            <img class="card-img-top" src="{{ url('images/projects', $project->thumbnail_image) }}" alt="">
                                        @else
                                            <img class="card-img-top" src="{{ asset('assets/images/no_image.png') }}" alt="">
                                        @endif

                                        <div class="card-img-overlay">
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
                                            
                                            <div class="badge {{ $badge_class }} p-1">{{ $badge_text }}</div>
                                        </div>

                                        <div class="card-body position-relative">
                                            <h4 class="mt-0">
                                                <a href="{{ url('admin-panel/projects/'. $project->id . '') }}" class="text-title">{{ $project->name ?? "" }}</a>
                                            </h4>

                                            <p class="mb-3">
                                                <span class="pe-2 text-nowrap">
                                                    <i class="mdi mdi-format-list-bulleted-type"></i>
                                                    <b>--</b> Tasks
                                                </span>
                                            </p>

                                            <div class="mb-3" id="tooltip-container4">
                                                @foreach ($project->project_team_members->take(3) as $user)
                                                    <a href="javascript:void(0);" data-bs-container="#tooltip-container4" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $user->name }}" class="d-inline-block">
                                                        <img src="{{ $user->profile_image ? url('images/users', $user->profile_image) : asset('assets/images/avator.png') }}" class="rounded-circle avatar-xs" alt="{{ $user->name }}">
                                                    </a>
                                                @endforeach

                                                @if ($project->project_team_members->count() > 3)
                                                    <a href="javascript:void(0);" class="d-inline-block text-muted fw-bold ms-2">
                                                        +{{ $project->project_team_members->count() - 3 }} more
                                                    </a>
                                                @endif
                                            </div>

                                            <p class="mb-2 fw-bold">Progress <span class="float-end">0%</span></p>

                                            <div class="progress progress-sm">
                                                <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                                                </div>
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
