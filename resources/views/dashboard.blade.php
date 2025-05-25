@extends('layouts.app')

@section('content')
<div class="container mx-auto p-8">
    <h1 class="text-3xl font-semibold mb-4">Dashboard</h1>
    <p>Halo, {{ auth()->user()->name }}! Anda masuk sebagai <strong>{{ auth()->user()->role->name }}</strong>.</p>
</div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <!-- Card Projects -->
        <a href="{{ route('projects.index') }}" class="block p-6 bg-white rounded-2xl shadow hover:shadow-lg text-center">
            <div class="text-xl font-semibold">Projects</div>
            <div class="mt-2 text-gray-500">Kelola Projects</div>
        </a>

        <!-- Card Tasks -->
        <a href="{{ route('tasks.index') }}" class="block p-6 bg-white rounded-2xl shadow hover:shadow-lg text-center">
            <div class="text-xl font-semibold">Tasks</div>
            <div class="mt-2 text-gray-500">Kelola Tasks</div>
        </a>

        <!-- Card Documents -->
        <a href="{{ route('documents.index') }}" class="block p-6 bg-white rounded-2xl shadow hover:shadow-lg text-center">
            <div class="text-xl font-semibold">Documents</div>
            <div class="mt-2 text-gray-500">Kelola Documents</div>
        </a>
    </div>
            


@endsection