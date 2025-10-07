<template>
    <div class="max-w-2xl mx-auto px-4 py-8">

        <!-- Header -->
        <div class="flex items-center gap-4 mb-6">
            <router-link to="/admin/rooms" class="p-2 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </router-link>
            <h1 class="text-2xl font-bold text-gray-800">Tambah Ruangan</h1>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <form @submit.prevent="submitForm">

                <!-- Nama Ruangan -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Ruangan <span class="text-red-500">*</span>
                    </label>
                    <input v-model="form.name" type="text" placeholder="Contoh: Ruang Meeting A"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        :class="{ 'border-red-500': errors.name }" />
                    <p v-if="errors.name" class="mt-1 text-sm text-red-600">
                        {{ errors.name[0] }}
                    </p>
                </div>

                <!-- Kapasitas -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kapasitas
                    </label>
                    <input v-model="form.capacity" type="number" min="1" placeholder="Contoh: 20"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        :class="{ 'border-red-500': errors.capacity }" />
                    <p v-if="errors.capacity" class="mt-1 text-sm text-red-600">
                        {{ errors.capacity[0] }}
                    </p>
                    <p class="mt-1 text-sm text-gray-500">
                        Kosongkan jika tidak ada batasan
                    </p>
                </div>

                <!-- Deskripsi -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea v-model="form.description" rows="4" placeholder="Deskripsi ruangan..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition resize-none"
                        :class="{ 'border-red-500': errors.description }"></textarea>
                    <p v-if="errors.description" class="mt-1 text-sm text-red-600">
                        {{ errors.description[0] }}
                    </p>
                </div>

                <!-- Info Box -->
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex gap-2">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-1">Status Default</p>
                            <p>Ruangan baru akan dibuat dengan status <strong>Tidak Aktif</strong>. Anda dapat mengubah
                                status setelah ruangan dibuat.</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3">
                    <button type="submit" :disabled="loading"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed font-medium">
                        {{ loading ? 'Menyimpan...' : 'Simpan' }}
                    </button>
                    <router-link to="/admin/rooms"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                        Batal
                    </router-link>
                </div>
            </form>
        </div>

        <!-- Toast -->
        <Transition name="slide-fade">
            <div v-if="message"
                :class="message.type === 'success' ? 'bg-green-600' : 'bg-red-600'"
                class="fixed bottom-4 right-4 px-6 py-3 text-white rounded-lg shadow-lg flex items-center gap-3 z-50">
                <svg v-if="message.type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>{{ message.text }}</span>
            </div>
        </Transition>

    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import roomService from '@/services/roomService';

const router = useRouter();
const loading = ref(false);
const message = ref(null);
const errors = ref({});

const form = ref({
    name: '',
    capacity: null,
    description: ''
});

const submitForm = async () => {
    loading.value = true;
    errors.value = {};

    console.log("🛰️ Submit form data:", form.value);

    try {
        const response = await roomService.createRoom(form.value);

        // ✅ DEBUG: Cek response structure
        console.log("📦 Full Response:", response);
        console.log("📦 Response Data:", response.data);
        console.log("📦 Success Flag:", response.data.success);

        // ✅ PERBAIKAN: Cek multiple conditions
        // ✅ FIX: Cek berdasarkan HTTP status code saja
        if (response.status === 200 || response.status === 201) {
            console.log("✅ Success! Redirecting...");

            // Tampilkan message
            showMessage('Ruangan berhasil ditambahkan', 'success');

            // Redirect LANGSUNG (tanpa delay dulu untuk testing)
            router.push('/admin/rooms');

            // Atau jika mau delay:
            // setTimeout(() => {
            //     router.push('/admin/rooms');
            // }, 1500);
        } else {
            console.log("❌ Success flag is false");
            showMessage('Gagal menyimpan data', 'error');
        }
    } catch (error) {
        console.error("❌ Error:", error);
        console.error("❌ Error Response:", error.response);

        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
            showMessage('Periksa kembali form Anda', 'error');
        } else {
            showMessage(error.response?.data?.message || 'Gagal menyimpan data', 'error');
        }
    } finally {
        loading.value = false;
    }
};

const showMessage = (text, type = 'info') => {
    message.value = { text, type };
    setTimeout(() => message.value = null, 2000);
};
</script>

<style scoped>
/* Animasi untuk toast */
.slide-fade-enter-active {
    transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
    transition: all 0.2s ease-in;
}

.slide-fade-enter-from {
    transform: translateX(20px);
    opacity: 0;
}

.slide-fade-leave-to {
    transform: translateX(20px);
    opacity: 0;
}
</style>
