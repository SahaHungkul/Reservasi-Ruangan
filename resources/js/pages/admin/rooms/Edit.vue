<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

const route = useRoute();
const router = useRouter();

const room = ref({
    name: "",
    capacity: "",
    status: "active",
});

const loading = ref(false);
const errorMessage = ref("");

async function fetchRoom() {
    try {
        const { data } = await axios.get(`/api/rooms/${route.params.id}`);
        room.value = data; // pastikan API balikin { name, capacity, status }
    } catch (err) {
        errorMessage.value = "Gagal mengambil data ruangan";
    }
}

async function updateRoom() {
    loading.value = true;
    errorMessage.value = "";

    try {
        await axios.put(`/api/rooms/${route.params.id}`, room.value);
        alert("Room updated successfully!");
        router.push("/rooms");
    } catch (err) {
        errorMessage.value =
            err.response?.data?.message || "Gagal update ruangan";
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    fetchRoom();
});
</script>

<template>
    <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded shadow">
        <h1 class="text-xl font-bold mb-4">Edit Room</h1>

        <form @submit.prevent="updateRoom">
            <div class="mb-3">
                <label>Room Name</label>
                <input v-model="room.name" class="w-full border p-2 rounded" required />
            </div>

            <div class="mb-3">
                <label>Capacity</label>
                <input type="number" v-model="room.capacity" class="w-full border p-2 rounded" required />
            </div>

            <div class="mb-3">
                <label>Status</label>
                <select v-model="room.status" class="w-full border p-2 rounded">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded" :disabled="loading">
                {{ loading ? "Updating..." : "Update Room" }}
            </button>
        </form>

        <p v-if="errorMessage" class="text-red-600 mt-3">{{ errorMessage }}</p>
    </div>
</template>
