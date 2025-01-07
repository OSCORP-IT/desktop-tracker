<x-app-layout>
    <x-slot name="page_title">{{ $page_title ?? 'Task Create |' }}</x-slot>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('admin-panel/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('admin-panel/tasks') }}"> Tasks </a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>

                    <h4 class="page-title">Task Create</h4>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input. <br><br>

                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <form action="{{ url('admin-panel/tasks') }}" method="POST" enctype="multipart/form-data">
                                @csrf
        
                                @include('admin_panel.tasks.form')
                                
                                <div class="float-end">
                                    <a href="{{ url('admin-panel/tasks') }}" class="btn btn-primary button-last"> Go Back </a>
                                    <button type="submit" class="btn btn-success button-last"> Save </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="script">
        <script src="https://cdn.ckeditor.com/4.17.1/standard/ckeditor.js"></script>

        <script type="text/javascript">
            CKEDITOR.replace('description');

            $(document).ready(function () {
                $('#project_id').on('change', function () {
                    var project_id = this.value;
                    
                    $("#assigned_to").html('');

                    $.ajax({
                        url: "{{ url('api/fetch-project-team-members-by-project-id') }}",
                        type: "GET",
                        data: {
                            project_id: project_id,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function (result) {
                            $('#assigned_to').html('<option value="" selected disabled> Choose Team Member </option>');
                            $.each(result.project_team_members, function (key, value) {
                                $("#assigned_to").append('<option value="' + value.id + '"> ' + value.name + ' - ' + value.email + '</option>');
                            });
                        }
                    });
                });
            });
        </script>
    </x-slot>
</x-app-layout>
