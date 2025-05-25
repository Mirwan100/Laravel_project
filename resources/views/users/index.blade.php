@extends('layouts.app')
@section('title', 'User Management')

@section('content')
  <div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-semibold">User Management</h1>
    <a href="{{ route('users.create') }}"
       class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">
      Tambah User
    </a>
  </div>

  <div class="overflow-x-auto bg-white rounded shadow">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Nama</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Email</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Role</th>
          <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @foreach($users as $user)
          <tr>
            <td class="px-4 py-2 text-sm">{{ $user->name }}</td>
            <td class="px-4 py-2 text-sm">{{ $user->email }}</td>
            <td class="px-4 py-2 text-sm">{{ $user->role->name }}</td>
            <td class="px-4 py-2 text-sm text-center space-x-2">
              <a href="{{ route('users.edit', $user) }}"
                 class="text-blue-600 hover:underline">
                Edit
              </a>
              <form action="{{ route('users.destroy', $user) }}"
                    method="POST" class="inline">
                @csrf @method('DELETE')
                <button type="submit"
                        class="text-red-600 hover:underline"
                        onclick="return confirm('Yakin ingin hapus?')">
                  Hapus
                </button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endsection
