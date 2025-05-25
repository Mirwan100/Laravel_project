@extends('layouts.app')
@section('title','Projects')
@section('content')

<div class="flex justify-between items-center mb-4">
  <h1 class="text-2xl font-semibold">Projects</h1>
  <div class="space-x-2">
    <a href="{{ route('projects.create') }}" class="px-3 py-1 bg-blue-600 text-white rounded">Add Project</a>

    <a href="{{ route('projects.index', ['show_deleted' => !$showDeleted]) }}"
       class="px-3 py-1 {{ $showDeleted ? 'bg-green-600' : 'bg-gray-600' }} text-white rounded">
      {{ $showDeleted ? 'Hide Deleted' : 'Show Deleted' }}
    </a>

    @if($showDeleted && $projects->count())
      <form action="{{ route('projects.restoreAll') }}" method="POST" class="inline">
        @csrf
        <button type="submit"
                onclick="return confirm('Pulihkan semua project?')"
                class="px-3 py-1 bg-purple-600 text-white rounded">
          Recover All
        </button>
      </form>
    @endif
  </div>
</div>

{{-- Form filter --}}
<form method="GET" action="{{ route('projects.index') }}" class="mb-4">
  <div class="flex flex-wrap space-x-4 items-center">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="border px-2 py-1" />

    {{-- Dropdown Select2 (Project dari database) --}}
    <select name="project_id" class="select2 border px-2 py-1 min-w-[180px]">
      <option value="">-- Semua Project --</option>
      @foreach($allProjects as $p)
        <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>
          {{ $p->name }}
        </option>
      @endforeach
    </select>

    <select name="sort_by" class="border px-2 py-1">
      <option value="name" {{ request('sort_by')=='name' ? 'selected':'' }}>Name</option>
      <option value="start_at" {{ request('sort_by')=='start_at' ? 'selected':'' }}>Start Date</option>
    </select>

    <select name="sort_order" class="border px-2 py-1">
      <option value="asc" {{ request('sort_order')=='asc' ? 'selected':'' }}>Ascending</option>
      <option value="desc" {{ request('sort_order')=='desc' ? 'selected':'' }}>Descending</option>
    </select>

    <button type="submit" class="px-2 py-1 bg-gray-200">Filter</button>
  </div>
</form>

{{-- Table projects --}}
@include('projects.partials.table')

{{-- Pagination --}}
<div class="mt-4">
  {{ $projects->links() }}
</div>

{{-- Audit Trail jika satu project dipilih --}}
@if(request('project_id') && $projects->count() === 1)
  @php
    $project = $projects->first();
    $audits = $project->audits()->with('user')->latest()->get();
  @endphp
  <h2 class="text-xl font-semibold mt-8">Audit Trail untuk "{{ $project->name }}"</h2>

  @if($audits->isEmpty())
    <p class="text-gray-600">Tidak ada perubahan tercatat.</p>
  @else
    <div class="overflow-x-auto mt-2">
      <table class="min-w-full bg-white border">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 text-left">Waktu</th>
            <th class="px-4 py-2 text-left">User</th>
            <th class="px-4 py-2 text-left">Event</th>
            <th class="px-4 py-2 text-left">Perubahan</th>
          </tr>
        </thead>
        <tbody>
          @foreach($audits as $audit)
            <tr class="border-t @if($loop->even) bg-gray-50 @endif">
              <td class="px-4 py-2">{{ $audit->created_at->format('Y-m-d H:i') }}</td>
              <td class="px-4 py-2">{{ optional($audit->user)->name ?? 'System' }}</td>
              <td class="px-4 py-2 capitalize">{{ $audit->event }}</td>
              <td class="px-4 py-2">
                @if($audit->event === 'created')
                  <div>Record awal dibuat.</div>
                @elseif($audit->event === 'updated')
                  @foreach($audit->getModified() as $field => $changes)
                    <div class="mb-1">
                      <strong>{{ $field }}:</strong>
                      <span class="line-through text-red-600">{{ $changes['old'] }}</span>
                      &rarr;
                      <span class="font-semibold">{{ $changes['new'] }}</span>
                    </div>
                  @endforeach
                @elseif($audit->event === 'deleted')
                  <div>Record dihapus.</div>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
@endif

@push('scripts')
  {{-- JQuery & Select2 init --}}
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
  <script>
    $(document).ready(function () {
      $('.select2').select2({ placeholder: 'Pilih Project', allowClear: true, width: 'resolve' });
    });
  </script>
@endpush

@endsection
