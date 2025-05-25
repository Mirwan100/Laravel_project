<!-- resources/views/layouts/app.blade.php -->
<nav class="bg-white shadow p-4 flex justify-between items-center">
  <div class="flex items-center space-x-2">
    <a href="{{ route('dashboard') }}"
       class="px-3 py-1 bg-gray-200 rounded">Dashboard</a>

    @auth
      <!-- Semua user login lihat Role Management -->
      <a href="{{ route('roles.index') }}"
         class="px-3 py-1 bg-blue-500 text-white rounded">
        Role Management
      </a>

      <!-- Semua user login juga lihat User Management,
           tapi route-nya hanya berhasil untuk admin -->
      <a href="{{ route('users.index') }}"
         class="px-3 py-1 bg-green-500 text-white rounded">
        User Management
      </a>
    @endauth
  </div>

  <div class="flex items-center space-x-4">
    @auth
      <span>{{ auth()->user()->name }}</span>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="px-2 py-1 bg-red-500 text-white rounded">
          Logout
        </button>
      </form>
    @endauth
  </div>
</nav>

