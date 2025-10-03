<script setup>
//
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <!-- Navbar -->
    <header class="bg-blue-600 text-white px-6 py-4 flex justify-between items-center">
      <h1 class="text-xl font-bold">Karyawan Dashboard</h1>
      <nav class="space-x-4">
        <router-link to="/karyawan/fixed-schedules" class="hover:underline">Fixed Schedules</router-link>
        <router-link to="/karyawan/rooms" class="hover:underline">Rooms</router-link>
        <router-link to="/karyawan/reservations" class="hover:underline">Reservations</router-link>
        <router-link to="/karyawan/about">About</router-link>
      </nav>
    </header>

    <!-- Content -->
    <main class="flex-1 bg-gray-50 p-6">
      <router-view />
    </main>

    <!-- Footer -->
    <footer class="bg-gray-200 text-center py-3 text-sm">
      &copy; {{ new Date().getFullYear() }} Karyawan Panel
    </footer>
  </div>
</template>


