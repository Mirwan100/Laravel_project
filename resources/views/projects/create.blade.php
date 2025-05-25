@extends('layouts.app')
@section('title','Create Project')
@section('content')
<h1 class="text-2xl font-semibold mb-4">Create Project</h1>
<form action="{{ route('projects.store') }}" method="POST">
  @csrf
  <div class="mb-4">
    <label>Name</label>
    <input type="text" name="name" value="{{ old('name') }}" class="border w-full px-2 py-1" required>
    @error('name')<p class="text-red-500">{{ $message }}</p>@enderror
  </div>
  <div class="mb-4">
    <label>Settings (JSON)</label>
    <textarea name="settings" class="border w-full px-2 py-1">{{ old('settings') }}</textarea>
    @error('settings')<p class="text-red-500">{{ $message }}</p>@enderror
  </div>
  <div class="mb-4">
    <label>Start At</label>
    <input type="date" name="start_at" value="{{ old('start_at') }}" class="border px-2 py-1" required>
    @error('start_at')<p class="text-red-500">{{ $message }}</p>@enderror
  </div>
  <div class="mb-4">
    <label><input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}> Active</label>
  </div>
  <button type="submit" class="px-4 py-2 bg-blue-600 text-white">Save</button>
</form>
@endsection