@extends('layouts.app')
@section('title','Edit Document')
@section('content')
<h1 class="text-2xl font-semibold mb-4">Edit Document</h1>
<form action="{{ route('documents.update', $document) }}" method="POST" enctype="multipart/form-data">
  @csrf @method('PUT')
  <div class="mb-4">
    <label>Task</label>
    <select name="task_id" class="border w-full px-2 py-1" required>
      @foreach($tasks as $task)
        <option value="{{ $task->id }}" {{ old('task_id', $document->task_id) == $task->id ? 'selected' : '' }}>{{ $task->title }}</option>
      @endforeach
    </select>
  </div>
  <div class="mb-4">
    <label>PDF File (leave empty to keep current)</label>
    <input type="file" name="file" accept="application/pdf" class="border w-full px-2 py-1">
  </div>

  <div class="mb-4">
  <label>
    <input type="checkbox" name="is_verivied" value="1" {{ old('is_verivied', $document->is_verivied) ? 'checked' : '' }}>
    Aktifkan dokumen ini
  </label>
</div>

  <button type="submit" class="px-4 py-2 bg-green-600 text-white">Update</button>
</form>
@endsection
