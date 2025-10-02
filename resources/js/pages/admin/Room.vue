<script setup>
import { ref, onMounted } from "vue"
import axios from "axios"

const rooms = ref([])
const loading = ref(true)
const error = ref(null)

onMounted(async () => {
    try {
        const res = await axios.get("/api/rooms")
        rooms.value = res.data.data;
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
        <h1 class="text-2xl font-bold mb-4">Daftar Room</h1>

        <div v-if="loading">Loading...</div>
        <div v-else-if="error" class="text-red-500">{{ error }}</div>
        <ul v-else class="space-y-2">
            <li v-for="room in rooms" :key="room.id" class="p-2 border rounded bg-white shadow">
                {{ room.name }} - Kapasitas: {{ room.capacity }} - Status: {{ room.status }}
            </li>
        </ul>
    </div>
</template>
