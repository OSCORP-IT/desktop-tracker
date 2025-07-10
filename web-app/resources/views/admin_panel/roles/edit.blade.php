<x-app-layout>
    <x-slot name="page_title">{{ $page_title ?? 'Role & Permission Edit |' }}</x-slot>

    <x-slot name="style">
        <link href="{{ asset('assets/vendor/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    </x-slot>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}"> {{ config('app.name', 'Laravel') }} </a></li>
                            <li class="breadcrumb-item"><a href="{{ url('admin-panel/dashboard') }}"> Dashboard </a></li>
                            <li class="breadcrumb-item"><a href="{{ url('admin-panel/dashboard/roles') }}"> roles </a></li>
                            <li class="breadcrumb-item active"> Edit </li>
                        </ol>
                    </div>

                    <h4 class="page-title"> Role & Permission Edit </h4>
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
                        <div class="row mb-2">
                            <div class="text-start mt-3">
                                <form action="{{ url('admin-panel/roles', $role->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
            
                                    <div class="row g-2">
                                        <div class="mb-1 col-md-5">
                                            <label for="name">Role Name</label>
                                            <input type="text" name="name" value="{{ old('name', $role->name ?? '') }}" class="form-control" id="name" placeholder="e.g: Executive" required>
                                        </div>
                                        <div class="mb-1 col-md-7">
                                            <label for="description">Role Description</label>
                                            <input type="text" name="description" value="{{ old('description', $role->description ?? '') }}" class="form-control" id="description">
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="form-group">
                                            <strong>Permission:</strong>
                                            @foreach($permission as $key => $value)
                                                {{-- <br>
                                                <input type="checkbox" value="{{ $value->id }}" {{ in_array($value->id, $role_permissions) ? 'checked' : '' }} name="permission[]" data-permission-name="{{$value->name}}">
                                                <span>{{ $value->name }}</span> --}}

                                                <div class="form-check mb-2">
                                                    <input type="checkbox" class="form-check-input" id="permissions_{{ $key }}" name="permission[]" value="{{ $value->id }}" data-permission-name="{{ $value->name }}" {{ in_array($value->id, $role_permissions) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="permissions_{{ $key }}">{{ $value->name }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    
                                    <div class="float-end">
                                        @can('role_view')
                                            <a href="{{ url('admin-panel/dashboard/roles') }}" class="btn btn-primary button-last"> Go Back </a>
                                        @endcan
                                        
                                        <button type="submit" class="btn btn-success button-last"> Update </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
