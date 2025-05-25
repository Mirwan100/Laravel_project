@extends('layouts.app')

@section('title','Documents')

@section('content')

<nav class="flex text-gray-500 text-sm mb-4">
  <a href="{{ route('dashboard') }}" class="hover:underline">Dashboard</a>
  <span class="mx-2">/</span>
  <span>Documents</span>
</nav>

<div class="flex justify-between items-center mb-4">
  <h1 class="text-2xl font-semibold">Documents</h1>
  <a href="{{ route('documents.create') }}" class="px-3 py-1 bg-blue-600 text-white rounded">Upload Document</a>
</div>

@if(session('success'))
  <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
    {{ session('success') }}
  </div>
@endif

<form method="GET" action="{{ route('documents.index') }}" class="mb-4 flex space-x-4 items-center">
  <div class="w-64">
    <select name="task_id" id="task_id" class="w-full border px-2 py-1 select2">
      <option value="">-- All Tasks --</option>
      @foreach($tasks as $t)
        <option value="{{ $t->id }}" {{ request('task_id') == $t->id ? 'selected' : '' }}>{{ $t->title }}</option>
      @endforeach
    </select>
  </div>
  <input type="text" name="filename" value="{{ request('filename') }}" placeholder="Search by Filename..."
         class="flex-1 border px-2 py-1 rounded" />
  <button type="submit" class="px-3 py-1 bg-gray-200 rounded">Filter</button>
  <a href="{{ route('documents.index') }}" class="px-3 py-1 bg-gray-300 rounded">Clear</a>
</form>

<table class="table-auto w-full border-collapse border border-gray-300 text-sm">
  <thead class="bg-gray-100">
    <tr>
      <th class="px-4 py-2 border text-xs">UUID</th>
      <th class="px-4 py-2 border text-xs">Filename</th>
      <th class="px-4 py-2 border text-xs">Task</th>
      <th class="px-4 py-2 border text-xs">Uploaded At</th>
      <th class="px-4 py-2 border text-xs">Verified</th>
      <th class="px-4 py-2 border text-xs">Actions</th>
    </tr>
  </thead>
  <tbody class="text-xs">
    @forelse($documents as $doc)
      <tr class="{{ $doc->deleted_at ? 'opacity-50' : '' }}">
        <td class="px-4 py-2 border"><span title="{{ $doc->id }}">{{ Str::limit($doc->id, 8, '...') }}</span></td>
        <td class="px-4 py-2 border">{{ $doc->filename }}</td>
        <td class="px-4 py-2 border">{{ $doc->task->title ?? '-' }}</td>
        <td class="px-4 py-2 border">{{ $doc->uploaded_at->format('Y-m-d H:i') }}</td>
        <td class="px-4 py-2 border">
          @if($doc->is_verified)
            <span class="px-2 py-1 bg-green-200 rounded text-green-800 text-xs">Yes</span>
          @else
            <span class="px-2 py-1 bg-yellow-200 rounded text-yellow-800 text-xs">No</span>
          @endif
        </td>
        <td class="px-4 py-2 border space-x-2">
          <a href="{{ route('documents.edit', $doc) }}" class="text-yellow-600 hover:underline text-xs">Edit</a>
          <form action="{{ route('documents.destroy', $doc) }}" method="POST" class="inline" onsubmit="return confirm('Delete this document?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-600 hover:underline text-xs">Delete</button>
          </form>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="6" class="px-4 py-2 border text-center text-gray-500 text-xs">No documents found.</td>
      </tr>
    @endforelse
  </tbody>
</table>

<div class="mt-4">
  {{ $documents->links() }}
</div>

<h2 class="mt-8 text-xl font-semibold">Audit Trail</h2>
<div class="overflow-x-auto mt-2">
  <table class="min-w-full bg-white border text-xs">
    <thead class="bg-gray-100">
      <tr>
        <th class="px-4 py-2 border">Time</th>
        <th class="px-4 py-2 border">Event</th>
        <th class="px-4 py-2 border">User</th>
        <th class="px-4 py-2 border">Details</th>
      </tr>
    </thead>
    <tbody>
      @forelse($audits as $audit)
        @php
          $auditable = $audit->auditable;
          $user = $audit->user;
        @endphp
        <tr class="border-t">
          <td class="px-4 py-2">{{ $audit->created_at->format('Y-m-d H:i') }}</td>
          <td class="px-4 py-2 capitalize">{{ ucfirst($audit->event) }}</td>
          <td class="px-4 py-2">{{ $user ? $user->name : 'System' }}</td>
          <td class="px-4 py-2">
            @if($audit->event === 'created')
              {{ Str::limit($auditable->filename ?? 'N/A', 20) }} created
            @elseif($audit->event === 'deleted')
              {{ Str::limit($auditable->filename, 20) }} deleted
            @else
              @foreach($audit->getModified() as $field => $chg)
                <div class="mb-1">
                  <strong>{{ $field }}:</strong>
                  @if(isset($chg['old']))
                    <span class="line-through">{{ Str::limit($chg['old'], 20) }}</span> →
                  @endif
                  <span>{{ Str::limit($chg['new'], 20) }}</span>
                </div>
              @endforeach
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="4" class="px-4 py-2 text-center text-gray-500">No audit entries found.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
  <div class="mt-4">
    @if($audits->hasPages())
      {{ $audits->links() }}
    @endif
  </div>
</div>
@endsection

@push('scripts')
<script>
  $(function(){
    $('#task_id').select2({ placeholder: '-- All Tasks --', allowClear: true, width: 'resolve' });
  });
</script>
@endpush
