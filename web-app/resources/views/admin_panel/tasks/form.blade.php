<div class="row g-2">
    <div class="mb-2 col-md-12">
        <label for="project_id"> Project <span class="text-danger">*</span> </label>
        <select id="project_id" name="project_id" class="form-select" required>
            <option value=""> Choose Project </option>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}" {{ (old('project_id') ?? ($task->project_id ?? '')) == $project->id ? 'selected' : '' }}>
                    {{ $project->name ?? "" }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row g-2">
    <div class="mb-2 col-md-8">
        <label for="title"> Title <span class="text-danger">*</span> </label>
        <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $task->title ?? "") }}" placeholder="" required>
    </div>

    <div class="mb-2 col-md-4">
        <label for="priority"> Priority <span class="text-danger">*</span> </label>
        <select id="priority" name="priority" class="form-select" required>
            <option value=""> Choose Priority </option>
            @foreach ($priorities as $priority)
                <option value="{{ $priority }}" {{ (old('priority') ?? ($task->priority ?? '')) == $priority ? 'selected' : '' }}>
                    {{ $priority }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row g-2">
    <div class="mb-2 col-md-12">
        <label for="description"> Description </label>
        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $task->description ?? "") }}</textarea>
    </div>
</div>

<div class="row g-2">
    <div class="mb-2 col-md-6">
        <label for="assigned_to"> Assigned To <span class="text-danger">*</span> </label>
        <select id="assigned_to" name="assigned_to" class="form-select" required>
            <option value=""> Choose Project </option>
            @foreach ($project_team_members as $project_team_member)
                <option value="{{ $project_team_member->id }}" {{ (old('assigned_to') ?? ($task->assigned_to ?? '')) == $project_team_member->id ? 'selected' : '' }}>
                    {{ $project_team_member->name ?? "" }} - {{ $project_team_member->email ?? "" }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-2 col-md-3">
        <label for="start_time"> Start Time <span class="text-danger">*</span> </label>
        <input type="datetime-local" class="form-control" id="start_time" name="start_time" value="{{ old('start_time', $task->start_time ?? "") }}" placeholder="" required>
    </div>

    <div class="mb-2 col-md-3">
        <label for="end_time"> End Time <span class="text-danger">*</span> </label>
        <input type="datetime-local" class="form-control" id="end_time" name="end_time" value="{{ old('end_time', $task->end_time ?? "") }}" placeholder="" required>
    </div>
</div>
