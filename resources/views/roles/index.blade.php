@extends('layouts.app')
@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">Role Management</h2>
    <a href="{{ route('roles.create') }}" class="px-3 py-1 bg-blue-500 text-white rounded">Tambah Role</a>
    <table class="table-auto w-full mt-4">
        <thead><tr><th>Nama</th><th>Aksi</th></tr></thead>
        <tbody>
        @foreach($roles as $role)
            <tr>
                <td>{{ $role->name }}</td>
                <td>
                    <a href="{{ route('roles.edit', $role) }}" class="mr-2">Edit</a>
                    <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection