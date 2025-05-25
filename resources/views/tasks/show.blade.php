@extends('layouts.app')

@section('title', 'Task Details')

@section('content')
  <div class="mb-4">
    <h1 class="text-2xl font-semibold">{{ $task->title }}</h1>
    <p class="text-gray-600">{{ optional($task->project)->name ?? 'No project' }}</p>
  </div>

  <div class="mb-4">
    <strong>Description:</strong>
    <p>{{ $task->description ?? 'No description available' }}</p>
  </div>

  <div class="mb-4">
    <strong>Due Date:</strong>
    <p>{{ $task->due_at->format('Y-m-d') }}</p>
  </div>

  <div class="mb-4">
    <strong>Status:</strong>
    <p>{{ $task->done ? 'Completed' : 'Pending' }}</p>
  </div>

  {{-- Documents Section --}}
  <div class="mb-6">
    <h2 class="text-xl font-semibold mb-2">Attached Documents</h2>
    @if($task->documents->isNotEmpty())
      <ul class="list-disc list-inside">
        @foreach($task->documents as $doc)
          <li>
            <a href="{{ asset('storage/' . $doc->filepath) }}" target="_blank" class="text-blue-600 hover:underline">
              {{ $doc->filename }}
            </a>
          </li>
        @endforeach
      </ul>
    @else
      <p class="text-gray-600">No documents attached.</p>
    @endif
  </div>

  <div class="mt-4">
    <a href="{{ route('tasks.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Back to Tasks</a>
  </div>
@endsection
