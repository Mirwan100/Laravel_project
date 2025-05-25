@extends('layouts.app')
@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">Detail Role</h2>
    <p><strong>Nama:</strong> {{ $role->name }}</p>
    <a href="{{ route('roles.index') }}" class="px-3 py-1 bg-gray-500 text-white rounded">Kembali</a>
</div>
@endsection