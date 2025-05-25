    @extends('layouts.app')
    @section('content')
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-bold mb-4">Tambah User</h2>
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" class="border px-2 py-1 w-full" required>
                @error('name')<p class="text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="border px-2 py-1 w-full" required>
                @error('email')<p class="text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1">Password</label>
                <input type="password" name="password" class="border px-2 py-1 w-full" required>
                @error('password')<p class="text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1">Role</label>
                <select name="role_id" class="border px-2 py-1 w-full" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')<p class="text-red-500">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded">Simpan</button>
        </form>
    </div>
    @endsection
