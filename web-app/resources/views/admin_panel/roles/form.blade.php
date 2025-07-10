<div class="row g-2">
    <div class="mb-2 col-md-6">
        <label for="journal_id"> Journal </label>
        <select class="form-select" id="journal_id" name="journal_id" required>
            <option value="" selected disabled> Choose Journal </option>
            @foreach ($journals as $journal)
                <option value="{{ $journal->id }}" {{ (old('journal_id') ?? ($issue->volume->journal_id ?? "")) == $journal->id ? "selected" : "" }}>
                    {{ $journal->name ?? "" }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-2 col-md-6">
        <label for="volume_id"> Volume </label>
        <select class="form-select" id="volume_id" name="volume_id" required>
            <option value="" selected disabled> Choose Volume </option>
            @foreach ($volumes as $volume)
                <option value="{{ $volume->id }}" {{ (old('volume_id') ?? ($issue->volume_id ?? "")) == $volume->id ? "selected" : "" }}>
                    {{ $volume->name ?? "" }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="row g-2">
    <div class="mb-2 col-md-8">
        <label for="name"> Issue Name </label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $issue->name ?? "") }}" placeholder="" required>
    </div>

    <div class="mb-2 col-md-4">
        <label> Date </label>
        <input type="text" id="basic-datepicker" name="date" value="{{ old('date', $issue->date ?? "") }}" class="form-control" placeholder="" required>
    </div>
</div>

<div class="row g-2">
    <div class="mb-2 col-md-6">
        <label for="input_status"> Status </label>
        <select class="form-select" id="input_status" name="input_status" required>
            <option value="" selected disabled> Choose Status </option>
            <option value="active" {{ (old('input_status') ?? ($issue->status ?? "")) == "active" ? 'selected' : "" }}> Active </option>
            <option value="inactive" {{ (old('input_status') ?? ($issue->status ?? "")) == "inactive" ? 'selected' : "" }}> Inactive </option>
        </select>
    </div>
</div>
