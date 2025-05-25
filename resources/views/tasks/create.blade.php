@extends('layouts.app')

@section('title', 'Create Task')

@section('content')
  <h1 class="text-2xl font-semibold mb-4">Create Task</h1>
  <form action="{{ route('tasks.store') }}" method="POST">
    @csrf

    @if ($errors->any())
      <div class="text-red-600 mb-4">
        <ul class="list-disc list-inside">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="mb-4">
      <label for="project_id" class="block mb-1">Project</label>
      <select id="project_id" name="project_id" class="select2 border w-full px-2 py-1" required>
        <option value="" disabled selected>Pilih project…</option>
        @foreach($projects as $id => $name)
          <option value="{{ $id }}" {{ old('project_id') == $id ? 'selected' : '' }}>
            {{ $name }}
          </option>
        @endforeach
      </select>
      @error('project_id')
        <p class="text-red-500 mt-1">{{ $message }}</p>
      @enderror
    </div>

    <div class="mb-4">
      <label for="title" class="block mb-1">Title</label>
      <input type="text" id="title" name="title" value="{{ old('title') }}" class="border w-full px-2 py-1" required>
      @error('title')
        <p class="text-red-500 mt-1">{{ $message }}</p>
      @enderror
    </div>

    <div class="mb-4">
      <label for="description" class="block mb-1">Description</label>
      <textarea name="description" class="border w-full px-2 py-1">{{ old('description') }}</textarea>
    </div>

    <div class="mb-4">
      <label for="due_at" class="block mb-1">Due Date</label>
      <input type="date" name="due_at" value="{{ old('due_at') }}" class="border w-full px-2 py-1" required>
    </div>

    <div class="mb-4">
      <label class="inline-flex items-center">
        <input type="checkbox" name="done" value="1" {{ old('done') ? 'checked' : '' }}>
        <span class="ml-2">Done</span>
      </label>
    </div>

    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
      Save
    </button>
  </form>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    $('.select2').select2({
      placeholder: 'Pilih project…',
      allowClear: true
    });
  });
</script>
@endpush
