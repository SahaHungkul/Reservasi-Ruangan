<script setup>
import { ref, onMounted } from "vue"
import axios from "axios"

const fixedSchedule = ref([])
const loading = ref(true)
const error = ref(null)

onMounted(async () => {
    try {
        const res = await axios.get("/api/fixed-schedules")
        fixedSchedule.value = res.data.data;
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
         <h1 class="text-2xl font-bold mb-4">Daftar Schedule</h1>

        <p>Selamat datang di panel admin 🚀</p>
        <div v-if="loading">Loading...</div>
        <div v-else-if="error" class="text-red-500">{{ error }}</div>

        <ul v-else class="space-y-2">
            <li v-for="fixedSchedule in fixedSchedule" :key="fixedSchedule.id" class="p-2 border rounded bg-white shadow">
                -id:{{ fixedSchedule.id }} - room: {{ fixedSchedule.room?.id }} - Hari: {{ fixedSchedule.day_of_week }} - start {{ fixedSchedule.start_time }} - end: {{ fixedSchedule.end_time }} - deskripsi {{ fixedSchedule.description }}
            </li>
        </ul>
    </div>
</template>
