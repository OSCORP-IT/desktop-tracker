<div class="row">
    <div class="col-7">
        <div class="row g-2">
            <div class="mb-2 col-md-12">
                <label for="name"> Name <span class="text-danger">*</span> </label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $project->name ?? "") }}" placeholder="" required>
            </div>
        </div>
        
        <div class="row g-2">
            <div class="mb-2 col-md-12">
                <label for="overview"> Overview </label>
                <textarea class="form-control" id="overview" name="overview" rows="3">{{ old('overview', $project->overview ?? "") }}</textarea>
            </div>
        </div>

        <div class="row g-2">
            <div class="mb-2 col-md-6">
                <label for="start_date"> Start Date <span class="text-danger">*</span> </label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', $project->start_date ?? "") }}" placeholder="" required>
            </div>

            <div class="mb-2 col-md-6">
                <label for="end_date"> End Date <span class="text-danger">*</span> </label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date', $project->end_date ?? "") }}" placeholder="" required>
            </div>
        </div>
        
        <div class="row g-2">
            <div class="mb-2 col-md-12">
                <label for="team_member_ids"> Team Members <span class="text-danger">*</span> </label>
                <select class="select2 form-control select2-multiple" id="team_member_ids" name="team_member_ids[]" data-toggle="select2" multiple="multiple" data-placeholder="Choose ..." required>
                    @foreach ($project_team_members as $project_team_member)
                        <option value="{{ $project_team_member->id }}" {{ in_array($project_team_member->id, (old('team_member_ids', []) ? old('team_member_ids', []) : $team_member_ids ?? [])) ? 'selected' : '' }}>
                            {{ $project_team_member->name ?? "" }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="row g-2">
            <div class="mb-2 col-md-6">
                <label for="thumbnail_image"> Thumbnail Image </label>
                <input type="file" class="form-control" id="thumbnail_image" name="thumbnail_image" accept="image/png, image/gif, image/jpeg">
        
                @if ($project->thumbnail_image)
                    <img src="{{ url('images/projects', $project->thumbnail_image) }}" alt="thumbnail_image" class="mt-1 img-fluid img-thumbnail" width="200" />
                @endif
            </div>
        </div>
    </div>

    <div class="col-5">
        <div class="row g-2">
            <div class="mb-2 col-md-12">
                <label for="thumbnail_image"> Files </label>
                <input type="file" class="mb-2" name="files[]" multiple>

                @if ($project->files)
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
                @endif
            </div>
        </div>
    </div>
</div>
