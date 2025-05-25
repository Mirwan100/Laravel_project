<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>@yield('title', 'Dashboard')</title>
  <script src="https://cdn.tailwindcss.com"></script>
    <!-- **SELECT2 CSS** -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

</head>
<body class="bg-gray-50 font-sans">
  <nav class="bg-white shadow px-6 py-4 flex justify-between items-center">
    <div class="flex space-x-4">
      <a href="{{ route('dashboard') }}"
         class="text-gray-700 hover:text-gray-900 font-medium">
        Dashboard
      </a>
      @auth
        <a href="{{ route('roles.index') }}"
           class="text-blue-600 hover:underline">
          Role Management
        </a>
        <a href="{{ route('users.index') }}"
           class="text-green-600 hover:underline">
          User Management
        </a>
      @endauth
    </div>
    <div class="flex items-center space-x-4">
      @auth
        <span class="text-gray-600">{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit"
                  class="text-red-500 hover:underline">
            Logout
          </button>
        </form>
      @endauth
    </div>
  </nav>

  <main class="max-w-5xl mx-auto p-6">
    @if(session('success'))
      <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        {{ session('success') }}
      </div>
    @endif

    @yield('content')
  </main>

    <!-- **SELECT2 JS** -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
  @stack('scripts') {{-- untuk inline script per-view --}}
</body>
</html>
