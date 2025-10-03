<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

const route = useRoute();
const router = useRouter();

const room_id = ref("");
const date = ref("");
const start_time = ref("");
const end_time = ref("");
const loading = ref(false);
const errorMessage = ref("");

const id = route.params.id;

// load data dari API
async function fetchSchedule() {
  try {
    const { data } = await axios.get(`/api/schedules/fixed/${id}`);
    const schedule = data.data;
    room_id.value = schedule.room_id;
    date.value = schedule.date;
    start_time.value = schedule.start_time;
    end_time.value = schedule.end_time;
  } catch (err) {
    errorMessage.value = "Gagal memuat data schedule";
  }
}

async function handleUpdate() {
  loading.value = true;
  errorMessage.value = "";

  try {
    await axios.put(`/api/schedules/fixed/${id}`, {
      room_id: room_id.value,
      date: date.value,
      start_time: start_time.value,
      end_time: end_time.value,
    });

    alert("Fixed Schedule berhasil diupdate!");
    router.push("/schedules");
  } catch (err) {
    errorMessage.value = err.response?.data?.message || "Gagal update schedule";
  } finally {
    loading.value = false;
  }
}

onMounted(fetchSchedule);
</script>

<template>
  <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6">Edit Fixed Schedule</h2>
    <form @submit.prevent="handleUpdate" class="space-y-4">

      <!-- Room -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Room ID</label>
        <input v-model="room_id" type="number" class="w-full border rounded p-2" required />
      </div>

      <!-- Date -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Date</label>
        <input v-model="date" type="date" class="w-full border rounded p-2" required />
      </div>

      <!-- Start Time -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Start Time</label>
        <input v-model="start_time" type="time" class="w-full border rounded p-2" required />
      </div>

      <!-- End Time -->
      <div>
        <label class="block text-sm font-medium text-gray-700">End Time</label>
        <input v-model="end_time" type="time" class="w-full border rounded p-2" required />
      </div>

      <p v-if="errorMessage" class="text-red-500 text-sm">{{ errorMessage }}</p>

      <button
        type="submit"
        :disabled="loading"
        class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700"
      >
        {{ loading ? "Updating..." : "Update Schedule" }}
      </button>
    </form>
  </div>
</template>
