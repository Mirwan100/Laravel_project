@extends('layouts.app') {{-- atau layout barumu --}}

@section('content')
    <div class="max-w-md mx-auto mt-10">
        <h2 class="text-2xl font-bold mb-4">Login</h2>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-4">
                <label>Email</label>
                <input type="email" name="email" class="border w-full px-2 py-1" required>
            </div>
            <div class="mb-4">
                <label>Password</label>
                <input type="password" name="password" class="border w-full px-2 py-1" required>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Login</button>
        </form>
    </div>
@endsection

