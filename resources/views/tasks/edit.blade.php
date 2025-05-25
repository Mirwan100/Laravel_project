@extends('layouts.app')

@section('title', 'Edit Task')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Edit Task</h1>
<form action="{{ route('tasks.update', $task) }}" method="POST">
  @csrf
  @method('PUT')

  <div class="mb-4">
    <label>Project</label>
    <select name="project_id" class="select2 border w-full px-2 py-1" required>
      @foreach($projects as $id => $name)
        <option value="{{ $id }}" {{ old('project_id', $task->project_id) == $id ? 'selected' : '' }}>
          {{ $name }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="mb-4">
    <label>Title</label>
    <input type="text" name="title" value="{{ old('title', $task->title) }}" class="border w-full px-2 py-1" required>
  </div>

  <div class="mb-4">
    <label>Description</label>
    <textarea name="description" class="border w-full px-2 py-1">{{ old('description', $task->description) }}</textarea>
  </div>

  <div class="mb-4">
    <label>Due At</label>
    <input type="date" name="due_at" value="{{ old('due_at', $task->due_at->format('Y-m-d')) }}" class="border w-full px-2 py-1" required>
  </div>

  <div class="mb-4">
    <label>
      <input type="checkbox" name="done" value="1" {{ old('done', $task->done) ? 'checked' : '' }}>
      Done
    </label>
  </div>

  <button type="submit" class="px-4 py-2 bg-green-600 text-white">Update</button>
</form>

<script>
  $('.select2').select2();
</script>
@endsection
