<!-- resources/views/landing.blade.php -->
@extends('layouts.app')  <!-- Mewarisi layout 'app' yang sudah ada -->

@section('content')
    <div class="landing-page">
        <h1>Welcome to Our Website!</h1>

        @guest
            <p>Sign in to access the dashboard</p>
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required autofocus>
                </div>

                <div>
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit">Login</button>
            </form>
        @else
            <p>Welcome back, {{ Auth::user()->name }}!</p>
        @endguest
    </div>
@endsection
