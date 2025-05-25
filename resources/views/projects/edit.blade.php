@extends('layouts.app')
@section('title','Edit Project')
@section('content')
<h1 class="text-2xl font-semibold mb-4">Edit Project</h1>
<form action="{{ route('projects.update',$project) }}" method="POST">
  @csrf @method('PUT')
  <div class="mb-4">
    <label>Name</label>
    <input type="text" name="name" value="{{ old('name',$project->name) }}" class="border w-full px-2 py-1" required>
  </div>
  <div class="mb-4">
    <label>Settings</label>
    <textarea name="settings" class="border w-full px-2 py-1">{{ old('settings',json_encode($project->settings)) }}</textarea>
  </div>
  <div class="mb-4">
    <label>Start At</label>
    <input type="date" name="start_at" value="{{ old('start_at',$project->start_at->format('Y-m-d')) }}" class="border px-2 py-1" required>
  </div>
  <div class="mb-4">
    <label><input type="checkbox" name="is_active" value="1" {{ $project->is_active ? 'checked':'' }}> Active</label>
  </div>
  <button type="submit" class="px-4 py-2 bg-green-600 text-white">Update</button>
</form>
@endsection