<script setup>
import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const router = useRouter();

const room_id = ref("");
const date = ref("");
const start_time = ref("");
const end_time = ref("");
const loading = ref(false);
const errorMessage = ref("");

async function handleSubmit() {
  loading.value = true;
  errorMessage.value = "";

  try {
    await axios.post("/api/fixed-schedules", {
      room_id: room_id.value,
      date: date.value,
      start_time: start_time.value,
      end_time: end_time.value,
    });

    alert("Fixed Schedule berhasil dibuat!");
    router.push({name:"FixedSchedulesAdmin"}); // kembali ke list schedule
  } catch (err) {
    errorMessage.value = err.response?.data?.message || "Gagal membuat schedule";
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold mb-6">Create Fixed Schedule</h2>
    <form @submit.prevent="handleSubmit" class="space-y-4">

      <!-- Room -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Room ID</label>
        <input
          v-model="room_id"
          type="number"
          class="w-full border rounded p-2"
          placeholder="Masukkan Room ID"
          required
        />
      </div>

      <!-- Date -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Date</label>
        <input
          v-model="date"
          type="date"
          class="w-full border rounded p-2"
          required
        />
      </div>

      <!-- Start Time -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Start Time</label>
        <input
          v-model="start_time"
          type="time"
          class="w-full border rounded p-2"
          required
        />
      </div>

      <!-- End Time -->
      <div>
        <label class="block text-sm font-medium text-gray-700">End Time</label>
        <input
          v-model="end_time"
          type="time"
          class="w-full border rounded p-2"
          required
        />
      </div>

      <p v-if="errorMessage" class="text-red-500 text-sm">{{ errorMessage }}</p>

      <button
        type="submit"
        :disabled="loading"
        class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700"
      >
        {{ loading ? "Saving..." : "Save Schedule" }}
      </button>
    </form>
  </div>
</template>
