@extends('layouts.app')
@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">Detail User</h2>
    <p><strong>Nama:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Role:</strong> {{ $user->role->name }}</p>
    <a href="{{ route('users.index') }}" class="px-3 py-1 bg-gray-500 text-white rounded">Kembali</a>
</div>
@endsection
