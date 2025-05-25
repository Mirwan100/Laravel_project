@extends('layouts.app')

@section('title', 'Tasks List')

@section('content')

<div class="mb-4 flex justify-between items-center">
  <div>
    <h1 class="text-2xl font-semibold">
      {{ request()->routeIs('tasks.trash') ? 'Trash: Tasks Terhapus' : 'Daftar Tasks' }}
    </h1>
  </div>
  <div class="space-x-2">
    @if(request()->routeIs('tasks.trash'))
      <a href="{{ route('tasks.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded">
        Kembali ke List
      </a>
    @else
      <a href="{{ route('tasks.trash') }}" class="px-4 py-2 bg-red-600 text-white rounded">
        Lihat Trash
      </a>
      <a href="{{ route('tasks.create') }}" class="px-4 py-2 bg-green-600 text-white rounded">
        Tambah Task
      </a>
    @endif
  </div>
</div>

{{-- Filter & Search --}}
<form method="GET" class="mb-4 flex space-x-4 items-end">
  {{-- Filter Project --}}
  <div>
    <label class="block text-sm font-medium mb-1">Filter Project</label>
    <select name="project_id" id="project_id" class="w-64 select2">
      <option value="">— Semua Project —</option>
      @foreach($projects as $id => $name)
        <option value="{{ $id }}" @selected(request('project_id') == $id)>{{ $name }}</option>
      @endforeach
    </select>
  </div>

  {{-- Search Task --}}
  <div>
    <label class="block text-sm font-medium mb-1">Search Task</label>
    <input type="text" name="search" value="{{ request('search') }}"
           class="w-64 border rounded px-2 py-1" placeholder="Cari judul task...">
  </div>

  <div>
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Apply</button>
  </div>
</form>

{{-- Tabel Task --}}
<div class="overflow-x-auto bg-white shadow rounded">
  <table class="min-w-full divide-y divide-gray-200 text-sm">
    <thead class="bg-gray-50">
      <tr>
        <th class="px-4 py-2">{!! th_sort('UUID','id') !!}</th>
        <th class="px-4 py-2">{!! th_sort('Task','title') !!}</th>
        <th class="px-4 py-2 w-56">{!! th_sort('Project','project') !!}</th>
        <th class="px-4 py-2">{!! th_sort('Due Date','due_at') !!}</th>
        <th class="px-4 py-2">Status</th>
        <th class="px-4 py-2">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
      @foreach($tasks as $task)
        <tr>
          <td class="px-4 py-2">
            <span title="{{ $task->id }}">
              {{ \Illuminate\Support\Str::limit($task->id, 4, '...') }}
            </span>
          </td>
          <td class="px-4 py-2">{{ $task->title }}</td>
          <td class="px-4 py-2">{{ optional($task->project)->name ?? '-' }}</td>
          <td class="px-4 py-2">{{ $task->due_at->format('Y-m-d') }}</td>
          <td class="px-4 py-2">{{ $task->done ? 'Completed' : 'Pending' }}</td>
          <td class="px-4 py-2 space-x-2">
            @if(request()->routeIs('tasks.trash'))
              <form action="{{ route('tasks.restore', $task->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-green-600">Restore</button>
              </form>
              <form action="{{ route('tasks.forceDelete', $task->id) }}" method="POST" class="inline">
                @csrf @method('DELETE')
                <button onclick="return confirm('Hapus permanen?')" class="text-red-600">Force Delete</button>
              </form>
            @else
              <a href="{{ route('tasks.show', $task) }}" class="text-blue-600">Show</a>
              <a href="{{ route('tasks.edit', $task) }}" class="text-yellow-600">Edit</a>
              <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline">
                @csrf @method('DELETE')
                <button onclick="return confirm('Yakin?')" class="text-red-600">Trash</button>
              </form>
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $tasks->links() }}</div>

{{-- Audit Trail --}}
<div class="mt-8">
  <h2 class="text-xl font-semibold mb-4">Audit Trail</h2>
  <div class="overflow-x-auto bg-white shadow rounded">
    <table class="min-w-full divide-y divide-gray-200 text-xs">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2">Waktu</th>
          <th class="px-4 py-2">User</th>
          <th class="px-4 py-2">Event</th>
          <th class="px-4 py-2">Perubahan</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($audits as $audit)
          <tr>
            <td class="px-4 py-2">{{ $audit->created_at->format('Y-m-d H:i:s') }}</td>
            <td class="px-4 py-2">{{ optional($audit->user)->name ?? 'System' }}</td>
            <td class="px-4 py-2 capitalize">{{ $audit->event }}</td>
            <td class="px-4 py-2">
              @php
                $event   = $audit->event;
                $changes = $audit->getModified();
              @endphp
              @if ($event === 'created')
                Buat tugas baru
              @elseif ($event === 'deleted')
                Tugas dihapus
              @elseif ($event === 'updated' && is_array($changes))
                @php
                  $fields = collect(array_keys($changes))
                              ->map(fn($f) => ucfirst($f))
                              ->implode(', ');
                @endphp
                Update: {{ $fields }}
              @else
                {{ ucfirst($event) }}
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="px-4 py-2 text-center text-gray-500">Belum ada audit.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-4">{{ $audits->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
  $(function(){
    $('#project_id').select2({
      placeholder: 'Pilih project...',
      allowClear: true,
      width: '100%'
    });
  });
</script>
@endpush