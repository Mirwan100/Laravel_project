@extends('layouts.app')
@section('title', 'Projects')

@section('content')

<div class="flex justify-between items-center mb-4">
  <h1 class="text-2xl font-semibold">Projects</h1>
  <div class="space-x-2">
    <a href="{{ route('projects.create') }}" class="px-3 py-1 bg-blue-600 text-white rounded">Add Project</a>
    <a href="{{ route('projects.index', ['show_deleted' => !$showDeleted] + request()->query()) }}"
       class="px-3 py-1 {{ $showDeleted ? 'bg-green-600' : 'bg-gray-600' }} text-white rounded">
      {{ $showDeleted ? 'Hide Deleted' : 'Show Deleted' }}
    </a>
  </div>
</div>

<!-- Filter Form -->
<form method="GET" action="{{ route('projects.index') }}" class="mb-4">
  <div class="flex flex-wrap space-x-4 items-center">
    <select name="project_id" id="project_id" class="border px-2 py-1 w-64 select2">
      <option value="">-- All Projects --</option>
      @foreach($allProjects as $p)
        <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>
          {{ $p->name }}
        </option>
      @endforeach
    </select>

    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
           class="border px-2 py-1" />

    <button type="submit" class="px-2 py-1 bg-gray-200">Filter</button>
  </div>
</form>

<!-- Export & Import Section -->
<div class="bg-gray-50 p-4 rounded-lg mb-6 border border-gray-200">
  <div class="flex flex-wrap gap-4 items-center">
    <!-- Export Form -->
    <form action="{{ route('projects.export') }}" method="POST" class="flex-1">
      @csrf
      <div class="flex items-center gap-2">
        <select name="fields[]" multiple class="select2-export w-full min-w-[300px]"
                data-placeholder="Pilih kolom untuk export...">
          @foreach($exportableFields as $field)
            @if($field !== 'id')
              <option value="{{ $field }}" {{ in_array($field, request('fields', [])) ? 'selected' : '' }}>
                {{ Str::title(str_replace('_',' ',$field)) }}
              </option>
            @endif
          @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 inline-flex items-center">
          <i class="fas fa-file-export mr-2"></i>Export Excel
        </button>
      </div>
    </form>

    <!-- Import Form -->
    <form action="{{ route('projects.import') }}" method="POST" enctype="multipart/form-data" class="flex-1">
      @csrf
      <div class="flex items-center gap-2">
        <input type="file" name="file" id="excelFile"
               class="block cursor-pointer file:px-4 file:py-2 file:border-0 file:text-gray-700 file:bg-gray-100 hover:file:bg-gray-200"
               accept=".xlsx,.xls" required>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 inline-flex items-center">
          <i class="fas fa-file-import mr-2"></i>Import Excel
        </button>
      </div>
    </form>
  </div>

  @if(session('export_status'))
    <div class="mt-4 p-3 bg-blue-50 text-blue-800 rounded-lg flex items-center">
      <i class="fas fa-info-circle mr-2"></i>
      {{ session('export_status') }}
      <a href="{{ route('projects.export.download') }}" class="ml-4 text-green-600 underline">
        Download Hasil Export
      </a>
    </div>
  @endif
</div>

@php
  $currentSort = request('sort_by');
  $currentDir = request('sort_direction', 'asc');
  function sortLink($column, $label) {
    $dir = request('sort_by') === $column && request('sort_direction') === 'asc' ? 'desc' : 'asc';
    $query = array_merge(request()->query(), ['sort_by' => $column, 'sort_direction' => $dir]);
    $url = request()->url() . '?' . http_build_query($query);
    $arrow = request('sort_by') === $column ? (request('sort_direction') === 'asc' ? '↑' : '↓') : '';
    return '<a href="'.$url.'" class="hover:underline">'.$label.' '.$arrow.'</a>';
  }
@endphp

<!-- Projects Table -->
<div class="overflow-x-auto mb-6">
  <table class="min-w-full bg-white rounded-lg overflow-hidden">
    <thead class="bg-gray-100">
      <tr>
        <th class="px-4 py-2">{!! sortLink('id', 'ID') !!}</th>
        <th class="px-4 py-2">{!! sortLink('name', 'Name') !!}</th>
        <th class="px-4 py-2">{!! sortLink('start_at', 'Start At') !!}</th>
        <th class="px-4 py-2">Active</th>
        <th class="px-4 py-2">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse($projects as $proj)
        <tr class="border-b">
          <td class="px-4 py-2">
            <span title="{{ $proj->id }}">{{ Str::limit($proj->id, 4, '...') }}</span>
          </td>
          <td class="px-4 py-2">{{ $proj->name }}</td>
          <td class="px-4 py-2">{{ $proj->start_at }}</td>
          <td class="px-4 py-2">{{ $proj->is_active ? 'Yes' : 'No' }}</td>
          <td class="px-4 py-2 space-x-2">
            <a href="{{ route('projects.show', $proj) }}" class="text-blue-600">View</a>
            <a href="{{ route('projects.edit', $proj) }}" class="text-yellow-600">Edit</a>
            <form action="{{ route('projects.destroy', $proj) }}" method="POST" class="inline">
              @csrf
              @method('DELETE')
              <button type="submit" class="text-red-600" onclick="return confirm('Delete?')">Delete</button>
            </form>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="text-center px-4 py-4 text-gray-500">No projects found.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

<!-- Pagination -->
<div class="mb-6">
  {{ $projects->links() }}
</div>

<!-- Audit Trail -->
<div class="bg-white p-4 rounded-lg border border-gray-200">
  <h2 class="text-xl font-semibold mb-4">Audit Trail</h2>
  <table class="w-full text-sm">
    <thead>
      <tr>
        <th class="px-2 py-1">Time</th>
        <th class="px-2 py-1">User</th>
        <th class="px-2 py-1">Event</th>
      </tr>
    </thead>
    <tbody>
      @forelse($audits as $audit)
        <tr class="border-t">
          <td class="px-2 py-1">{{ $audit->created_at }}</td>
          <td class="px-2 py-1">{{ $audit->user?->name ?? '—' }}</td>
          <td class="px-2 py-1">{{ $audit->event }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="3" class="text-center px-4 py-4 text-gray-500">No audit logs available.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div class="mt-4">
    {{ $audits->links() }}
  </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#project_id').select2({
        placeholder: '-- All Projects --',
        allowClear: true,
        width: '100%'
    });

    $('.select2-export').select2({
        placeholder: $(this).data('placeholder'),
        allowClear: true,
        width: '100%',
        closeOnSelect: false
    });
});
</script>
@endpush
