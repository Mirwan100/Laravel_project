@extends('layouts.app')
@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">Edit Role</h2>
    <form action="{{ route('roles.update', $role) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-4">
            <label class="block mb-1">Nama Role</label>
            <input type="text" name="name" value="{{ old('name', $role->name) }}" class="border px-2 py-1 w-full" required>
            @error('name')<p class="text-red-500">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded">Update</button>
    </form>
</div>
@endsection