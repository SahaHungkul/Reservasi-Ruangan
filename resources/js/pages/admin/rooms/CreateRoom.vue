<script setup>
import { ref } from 'vue'
import axios from 'axios'

const name = ref('')
const capacity = ref('')
const description = ref('')
// const status = ref('inactive') // default aktif
const loading = ref(false)
const message = ref('')
const error = ref('')

async function createRoom() {
  loading.value = true
  message.value = ''
  error.value = ''
  try {
    const response = await axios.post('/api/rooms', {
      name: name.value,
      capacity: capacity.value,
      description: description.value,
    //   status: status.value

    })
    message.value = 'Room berhasil dibuat ✅'
    console.log('Response:', response.data)

    // reset form
    name.value = ''
    capacity.value = ''
    description.value = ''
    // status.value = 'active'
  } catch (err) {
    error.value = err.response?.data?.message || 'Gagal membuat room'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="max-w-md mx-auto mt-10 p-6 bg-white rounded-lg shadow">
    <h2 class="text-xl font-bold mb-4">Create Room</h2>

    <div class="mb-3">
      <label class="block mb-1 font-medium">Nama Room</label>
      <input v-model="name" type="text" placeholder="Nama Room"
             class="w-full border p-2 rounded"/>
    </div>

    <div class="mb-3">
      <label class="block mb-1 font-medium">Kapasitas</label>
      <input v-model="capacity" type="number" placeholder="Kapasitas"
             class="w-full border p-2 rounded"/>
    </div>
    <div class="mb-3">
      <label class="block">Deskripsi</label>
      <textarea v-model="deskripsi" class="border rounded w-full p-2"></textarea>
    </div>

    <!-- <div class="mb-3">
      <label class="block mb-1 font-medium">Status</label>
      <select v-model="status" class="w-full border p-2 rounded">
        <option value="active">Aktif</option>
        <option value="inactive">Tidak Aktif</option>
      </select>
    </div> -->

    <button @click="createRoom" :disabled="loading"
            class="w-full bg-blue-500 text-white py-2 rounded">
      {{ loading ? 'Menyimpan...' : 'Simpan' }}
    </button>

    <p v-if="message" class="text-green-600 mt-3">{{ message }}</p>
    <p v-if="error" class="text-red-600 mt-3">{{ error }}</p>
  </div>
</template>
