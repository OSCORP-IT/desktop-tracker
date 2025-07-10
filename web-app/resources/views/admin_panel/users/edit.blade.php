<x-app-layout>
    <x-slot name="page_title">{{ $page_title ?? 'User Edit |' }}</x-slot>

    <x-slot name="style">
        <link href="{{ asset('hyper/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('hyper/vendor/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    </x-slot>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}"> {{ config('app.name', 'Laravel') }} </a></li>
                            <li class="breadcrumb-item"><a href="{{ url('admin-panel/dashboard') }}"> Dashboard </a></li>
                            <li class="breadcrumb-item"><a href="{{ url('admin-panel/users') }}"> Users </a></li>
                            <li class="breadcrumb-item active"> Edit </li>
                        </ol>
                    </div>

                    <h4 class="page-title"> User Edit </h4>
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

        <div class="row">
            <div class="col-12">
                <div class="card text-center">
                    <div class="card-body">
                        @if ($user->profile_image)
                            <img src="{{ url('images/users', $user->profile_image) }}" class="rounded-circle avatar-lg img-thumbnail" alt="thumbnail_image" />
                        @else
                            <img src="{{ asset('hyper/images/avator.png') }}" class="rounded-circle avatar-lg img-thumbnail" alt="thumbnail_image" />
                        @endif

                        <div class="row mb-2">
                            <div class="text-start mt-3">
                                <form action="{{ url('admin-panel/users', $user->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
            
                                    @include('admin_panel.users.form')
                                    
                                    <div class="float-end">
                                        <a href="{{ url('admin-panel/users') }}" class="btn btn-primary button-last"> Go Back </a>
                                        <button type="submit" class="btn btn-success button-last"> Save </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- script -->
    <x-slot name="script">
        <script src="{{ asset('hyper/vendor/select2/js/select2.min.js') }}"></script>
        <script src="{{ asset('hyper/vendor/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}"></script>
        <script src="{{ asset('hyper/vendor/flatpickr/flatpickr.min.js') }}"></script>
        <script src="{{ asset('hyper/js/pages/demo.timepicker.js') }}"></script>
    </x-slot>
</x-app-layout>
