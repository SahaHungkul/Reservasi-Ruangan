<script setup>
import { ref, onMounted } from "vue"
import axios from "axios"

const reservations = ref([])
const loading = ref(true)
const error = ref(null)

onMounted(async () => {
    try {
        const res = await axios.get("/api/reservations")
        reservations.value = res.data.data;
    } catch (err) {
        error.value = "Gagal memuat data"
        console.error(err)
    } finally {
        loading.value = false
    }
})
</script>

<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Daftar Reservasi</h1>

        <p>Selamat datang di panel admin 🚀</p>
        <div v-if="loading">Loading...</div>
        <div v-else-if="error" class="text-red-500">{{ error }}</div>

        <ul v-else class="space-y-2">
            <li v-for="reservations in reservations" :key="reservations.id" class="p-2 border rounded bg-white shadow">
               id: {{ reservations.user_id }} - Room: {{ reservations.room?.id }} - date: {{ reservations.date }} -start: {{ reservations.start_time }} - end: {{ reservations.end_time }} - status: {{ reservations.status }}
            </li>
        </ul>
    </div>
</template>
