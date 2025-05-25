@extends('layouts.app')

@section('title','Upload Document')

@section('content')

<h1 class="text-2xl font-semibold mb-4">Upload Document</h1>

<form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
  @csrf

  @if($errors->any())
    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
      <ul class="list-disc pl-5">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="mb-4">
    <label class="block mb-1">Task</label>
    <select name="task_id" class="border w-full px-2 py-1" required>
      <option value="">-- Select Task --</option>
      @foreach($tasks as $task)
        <option value="{{ $task->id }}" {{ old('task_id') == $task->id ? 'selected' : '' }}>
          {{ $task->title }}
        </option>
      @endforeach
    </select>
    @error('task_id')
      <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
  </div>

  <div class="mb-4">
    <label class="block mb-1">
      PDF File <span class="text-gray-500 text-sm">(100KB – 500KB)</span>
    </label>
    <input
      type="file"
      name="file"
      accept="application/pdf"
      class="border w-full px-2 py-1"
      required
    >
    @error('file')
      <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
  </div>

  <div class="mb-4">
    <label class="inline-flex items-center">
      <input type="hidden" name="is_verified" value="0">
      <input
        type="checkbox"
        name="is_verified"
        class="form-checkbox"
        value="1"
        {{ old('is_verified', true) ? 'checked' : '' }}
      >
      <span class="ml-2">Verified</span>
    </label>
    @error('is_verified')
      <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
  </div>

  <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Upload</button>
</form>

@endsection

@push('scripts')
<script>
  // custom scripts if needed
</script>
@endpush