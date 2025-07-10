<x-app-layout>
    <x-slot name="page_title">{{ $page_title ?? 'Project Edit |' }}</x-slot>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('admin-panel/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('admin-panel/projects') }}"> Projects </a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>

                    <h4 class="page-title">Edit Project</h4>
                </div>
            </div>
        </div>

        <div id="form-success" class="alert alert-success" style="display: none;">
            <span id="success-message"></span>
        </div>

        <div id="form-errors" class="alert alert-danger" style="display: none;">
            <strong>Whoops!</strong> There were some problems with your input. <br><br>
            <ul id="error-list"></ul>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <form action="{{ url('admin-panel/projects/' . $project->id) }}" method="POST" enctype="multipart/form-data" id="project_form">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row g-2">
                                            <div class="mb-2 col-md-8">
                                                <label for="name"> Project Name <span class="text-danger">*</span> </label>
                                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $project->name) }}" placeholder="" maxlength="255" required>
                                            </div>
                                        
                                            <div class="mb-2 col-md-4">
                                                <label for="manager_id"> Project Manager <span class="text-danger">*</span> </label>
                                                <select id="manager_id" name="manager_id" class="form-select" required>
                                                    <option value=""> Choose Project Manager </option>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}" {{ old('manager_id', $project->manager_id) == $user->id ? 'selected' : '' }}>
                                                            {{ $user->name ?? "" }} (ID: {{ $user->employee_id ?? "N/A" }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="row g-2">
                                            <div class="mb-2 col-md-12">
                                                <label for="overview"> Overview </label>
                                                <textarea class="form-control" id="overview" name="overview" rows="3">{{ old('overview', $project->overview) }}</textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="row g-2">
                                            <div class="mb-2 col-md-4">
                                                <label for="start_date"> Start Date <span class="text-danger">*</span> </label>
                                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', $project->start_date->format('Y-m-d')) }}" placeholder="" required>
                                            </div>
                                        
                                            <div class="mb-2 col-md-4">
                                                <label for="end_date"> End Date <span class="text-danger">*</span> </label>
                                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date', $project->end_date->format('Y-m-d')) }}" placeholder="" required>
                                            </div>

                                            <div class="mb-2 col-md-4">
                                                <label for="budget"> Budget </label>
                                                <input type="number" step="0.01" class="form-control" id="budget" name="budget" value="{{ old('budget', $project->budget) }}" placeholder="0.00">
                                            </div>
                                        </div>
                                        
                                        <div class="row g-2">
                                            <div class="mb-2 col-md-6">
                                                <label for="thumbnail_image"> Thumbnail Image </label>
                                                <input type="file" class="form-control" id="thumbnail_image" name="thumbnail_image" accept="image/png, image/gif, image/jpeg">
                                        
                                                @if ($project->thumbnail_image)
                                                    <img src="{{ url($project->thumbnail_image) }}" alt="thumbnail_image" class="mt-1 img-fluid img-thumbnail" width="200" />
                                                @endif
                                            </div>

                                            <div class="mb-2 col-md-6">
                                                <label for="input_status"> Status <span class="text-danger">*</span> </label>
                                                <select id="input_status" name="input_status" class="form-select" required>
                                                    <option value=""> Choose Status </option>
                                                    @foreach ($status as $stat)
                                                        <option value="{{ $stat }}" {{ old('input_status', $project->status) == $stat ? 'selected' : '' }}>
                                                            {{ $stat }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="row g-2 mt-3">
                                            <div class="mb-2 col-md-12">
                                                <h3>Team Members</h3>

                                                <div id="team-members-container">
                                                    @foreach ($project->project_team_members as $index => $member)
                                                        <div class="team-member">
                                                            <div class="row">
                                                                <div class="col-md-5 mb-1">
                                                                    <label for="project_team_members[{{ $index }}][id]" class="form-label">Member <span class="text-danger">*</span></label>
                                                                    <select class="form-select @error('project_team_members.' . $index . '.id') is-invalid @enderror" name="project_team_members[{{ $index }}][id]" required>
                                                                        <option value="">Select Member</option>
                                                                        @foreach ($users as $user)
                                                                            <option value="{{ $user->id }}" {{ old('project_team_members.' . $index . '.id', $member->member->id) == $user->id ? 'selected' : '' }}>{{ $user->name }} (ID: {{ $user->employee_id ?? "N/A" }})</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('project_team_members.' . $index . '.id')
                                                                        <div class="error">{{ $message }}</div>
                                                                    @enderror
                                                                </div>

                                                                <div class="col-md-5 mb-1">
                                                                    <label for="project_team_members[{{ $index }}][role]" class="form-label">Role <span class="text-danger">*</span></label>
                                                                    <select class="form-select @error('project_team_members.' . $index . '.role') is-invalid @enderror" name="project_team_members[{{ $index }}][role]" required>
                                                                        @foreach ($project_team_member_roles as $role)
                                                                            <option value="{{ $role }}" {{ old('project_team_members.' . $index . '.role', $member->role) == $role ? 'selected' : '' }}>{{ $role }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('project_team_members.' . $index . '.role')
                                                                        <div class="error">{{ $message }}</div>
                                                                    @enderror
                                                                </div>

                                                                <div class="col-md-2 mb-1">
                                                                    <label class="form-label"> </label>
                                                                    <button type="button" class="btn btn-danger mt-3 remove-btn" onclick="removeTeamMember(this)">Remove</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <button type="button" class="btn btn-secondary mt-2" onclick="addTeamMember()">Add Team Member</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 border">
                                        <h3>Attachments</h3>
                                        
                                        <div id="attachments-container">
                                            @foreach ($project->attachments as $index => $attachment)
                                                <div class="attachment">
                                                    <div class="row">
                                                        <div class="col-md-10 mb-3">
                                                            <label class="form-label">Existing File</label>
                                                            <p><a href="{{ url($attachment->file_path) }}" target="_blank">{{ $attachment->original_name }}</a></p>
                                                            <input type="hidden" name="existing_attachments[{{ $index }}][id]" value="{{ $attachment->id }}">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <label class="form-label"> </label>
                                                            <button type="button" class="btn remove-btn text-danger" onclick="removeAttachment(this)">Remove</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach

                                            <div class="attachment">
                                                <div class="row">
                                                    <div class="col-md-10 mb-3">
                                                        <label for="attachments[0]" class="form-label">New File</label>
                                                        <input type="file" class="form-control @error('attachments.0') is-invalid @enderror" name="attachments[0]" accept=".jpeg,.png,.jpg,.gif,.pdf,.doc,.docx,.zip">
                                                        @error('attachments.0')
                                                            <div class="error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label"> </label>
                                                        <button type="button" class="btn remove-btn text-danger" onclick="removeAttachment(this)">Remove</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="button" class="btn btn-secondary mt-2" onclick="addAttachment()">Add Attachment</button>
                                    </div>
                                </div>
                                
                                <div class="float-end">
                                    <a href="{{ url('admin-panel/projects') }}" class="btn btn-primary button-last"> Go Back </a>
                                    <button type="submit" class="btn btn-success button-last"> Update </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="script">
        <script type="text/javascript">
            let teamMemberIndex = {{ $project->project_team_members->count() }};
            let attachmentIndex = {{ $project->attachments->count() + 1 }};

            function addTeamMember() {
                const container = document.getElementById('team-members-container');
                const template = `
                    <div class="team-member">
                        <div class="row">
                            <div class="col-md-5 mb-1">
                                <label for="project_team_members[${teamMemberIndex}][id]" class="form-label">Member <span class="text-danger">*</span></label>
                                <select class="form-select" name="project_team_members[${teamMemberIndex}][id]" required>
                                    <option value="">Select Member</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} (ID: {{ $user->employee_id ?? "N/A" }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-5 mb-1">
                                <label for="project_team_members[${teamMemberIndex}][role]" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select" name="project_team_members[${teamMemberIndex}][role]" required>
                                    @foreach ($project_team_member_roles as $role)
                                        <option value="{{ $role }}">{{ $role }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-1">
                                <label class="form-label"> </label>
                                <button type="button" class="btn btn-danger mt-3 remove-btn" onclick="removeTeamMember(this)">Remove</button>
                            </div>
                        </div>
                    </div>`;

                container.insertAdjacentHTML('beforeend', template);
                teamMemberIndex++;
            }

            function addAttachment() {
                const container = document.getElementById('attachments-container');
                const template = `
                    <div class="attachment">
                        <div class="row">
                            <div class="col-md-10 mb-3">
                                <label for="attachments[${attachmentIndex}]" class="form-label">New File</label>
                                <input type="file" class="form-control" name="attachments[${attachmentIndex}]" accept=".jpeg,.png,.jpg,.gif,.pdf,.doc,.docx,.zip">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label"> </label>
                                <button type="button" class="btn remove-btn text-danger" onclick="removeAttachment(this)">Remove</button>
                            </div>
                        </div>
                    </div>`;

                container.insertAdjacentHTML('beforeend', template);
                attachmentIndex++;
            }

            function removeTeamMember(element) {
                if (document.querySelectorAll('.team-member').length > 1) {
                    element.closest('.team-member').remove();
                }
            }

            function removeAttachment(element) {
                if (document.querySelectorAll('.attachment').length > 1) {
                    element.closest('.attachment').remove();
                }
            }

            $(document).ready(function() {
                $("#project_form").on("submit", function(e) {
                    e.preventDefault();

                    let formData = new FormData(this);
                    let errorContainer = $("#form-errors");
                    let errorList = $("#error-list");

                    $.ajax({
                        url: $(this).attr('action'),
                        method: 'POST', // Laravel handles PUT via _method
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                $('#form-success').show();
                                $('#success-message').text(response.success);

                                setTimeout(function() {
                                    window.location.href = "{{ url('admin-panel/projects') }}";
                                }, 500);
                            } 
                            else {
                                $('#form-errors').show();
                                $('#error-list').empty();

                                if (response.error) {
                                    $('#error-list').append(`<li>${response.error}</li>`);
                                }
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                var errors = xhr.responseJSON.errors;
                                $('#form-errors').show();
                                $('#error-list').empty();
                                
                                $.each(errors, function(key, value) {
                                    $('#error-list').append(`<li>${value[0]}</li>`);
                                    $(`#${key.replace('.', '_')}`).addClass('is-invalid');
                                    $(`#${key.replace('.', '_')}-error`).text(value[0]);
                                });

                                $('html, body').animate({
                                    scrollTop: $('#form-errors').offset().top - 100
                                }, 500);
                            } 
                            else {
                                $('#form-errors').show();
                                $('#error-list').html('<li>An unexpected error occurred. Please try again.</li>');
                            }
                        }
                    });
                });
            });
        </script>
    </x-slot>
</x-app-layout>
