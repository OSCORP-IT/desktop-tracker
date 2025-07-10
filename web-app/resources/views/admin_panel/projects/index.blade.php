<x-app-layout>
    <x-slot name="page_title">{{ $page_title ?? 'Projects |' }}</x-slot>

    <x-slot name="style">
        <link href="{{ asset('hyper/css/vendor/dataTables.bootstrap5.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('hyper/css/vendor/responsive.bootstrap5.css') }}" rel="stylesheet" type="text/css" />
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
                                <a href="{{ url('admin-panel/projects/create') }}" class="btn btn-danger rounded-pill mb-2"><i class="mdi mdi-plus-circle me-2"></i> Add Project</a>
                            </div>
                        </div>

                        <div class="row">
                            @forelse ($projects as $project)
                                <div class="col-lg-6 col-xxl-3">
                                    <div class="card d-block">
                                        <div class="card-body">
                                            <div class="dropdown card-widgets">
                                                <a href="#" class="dropdown-toggle arrow-none" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="dripicons-dots-3"></i>
                                                </a>

                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="{{ url('admin-panel/projects/'. $project->id . '') }}" class="dropdown-item"><i class="mdi mdi-email-outline me-1"></i>Details</a>
                                                    <a href="{{ url('admin-panel/projects/'. $project->id . '/edit') }}" class="dropdown-item"><i class="mdi mdi-pencil me-1"></i>Edit</a>

                                                    <form action="{{ url('admin-panel/projects', $project->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')

                                                        <input name="_method" type="hidden" value="DELETE">
                                                        <button type="submit" class="dropdown-item"><i class="mdi mdi-delete me-1"></i>Delete</button>
                                                    </form>
                                                </div>
                                            </div>

                                            <h4 class="mt-0">
                                                <a href="{{ url('admin-panel/projects/'. $project->id . '') }}" class="text-title">{{ $project->name ?? "" }}</a>
                                            </h4>

                                            <div class="badge bg-{{ $project->status == 'Finished' ? 'success' : ($project->status == 'Ongoing' ? 'primary' : ($project->status == 'Pending' ? 'warning' : 'secondary')) }} mb-3">
                                                {{ $project->status }}
                                            </div>

                                            @if ($project->overview)
                                                <p class="text-muted font-13 mb-3">
                                                    {{ Str::limit($project->overview ?? '', 70) }}
                                                    <a href="{{ url('admin-panel/projects/'. $project->id . '') }}" class="fw-bold text-muted">view more</a>
                                                </p>
                                            @else
                                                <p class="text-muted font-13 mb-3">No overview provided.</p>
                                            @endif

                                            <p class="mb-1">
                                                <span class="pe-2 text-nowrap mb-2 d-inline-block">
                                                    <i class="mdi mdi-format-list-bulleted-type text-muted"></i>
                                                    <b>{{ $project->tasks->count() ?? 0 }}</b> Tasks
                                                </span>
                                            </p>

                                            <div id="tooltip-container-{{ $project->id }}">
                                                @foreach ($project->project_team_members->take(3) as $member)
                                                    <a href="javascript:void(0);" data-bs-container="#tooltip-container-{{ $project->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $member->member->name }}" class="d-inline-block">
                                                        <img src="{{ $member->member->profile_image ? url('images/users', $member->member->profile_image) : asset('hyper/images/avator.png') }}" class="rounded-circle avatar-xs" alt="{{ $member->member->name }}">
                                                    </a>
                                                @endforeach

                                                @if ($project->project_team_members->count() > 3)
                                                    <a href="javascript:void(0);" class="d-inline-block text-muted fw-bold ms-2">
                                                        +{{ $project->project_team_members->count() - 3 }} more
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-muted">No projects found.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="script">
        <script src="{{ asset('hyper/js/vendor/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('hyper/js/vendor/dataTables.bootstrap5.js') }}"></script>
        <script src="{{ asset('hyper/js/vendor/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('hyper/js/vendor/responsive.bootstrap5.min.js') }}"></script>

        <script src="{{ asset('hyper/js/pages/demo.datatable-init.js') }}"></script>
        <script src="{{ asset('hyper/js/sweetalert2@11') }}"></script>

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
