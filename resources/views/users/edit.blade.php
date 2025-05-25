@extends('layouts.app')
@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">Edit User</h2>
    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-4">
            <label class="block mb-1">Nama</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="border px-2 py-1 w-full" required>
            @error('name')<p class="text-red-500">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="border px-2 py-1 w-full" required>
            @error('email')<p class="text-red-500">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1">Role</label>
            <select name="role_id" class="border px-2 py-1 w-full" required>
                @foreach(App\Models\Role::all() as $role)
                    <option value="{{ $role->id }}" {{ (old('role_id', $user->role_id) == $role->id) ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            @error('role_id')<p class="text-red-500">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded">Update</button>
    </form>
</div>
@endsection
