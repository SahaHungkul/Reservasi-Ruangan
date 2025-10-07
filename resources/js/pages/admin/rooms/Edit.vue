<template>
    <div class="max-w-2xl mx-auto px-4 py-8">

        <!-- Header -->
        <div class="flex items-center gap-4 mb-6">
            <router-link to="/admin/rooms" class="p-2 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </router-link>
            <h1 class="text-2xl font-bold text-gray-800">Edit Ruangan</h1>
        </div>

        <!-- ✅ Loading State - Lebih Simple -->
        <div v-if="isLoading" class="bg-white rounded-lg shadow p-12 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent">
            </div>
            <p class="mt-2 text-gray-600">Memuat data...</p>
        </div>

        <!-- ✅ Error State -->
        <div v-else-if="loadError" class="bg-white rounded-lg shadow p-6">
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Gagal Memuat Data</h3>
                <p class="text-gray-600 mb-4">{{ loadError }}</p>
                <button @click="fetchRoom" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Coba Lagi
                </button>
            </div>
        </div>

        <!-- ✅ Form Card - Tanpa v-else-if -->
        <div v-else class="bg-white rounded-lg shadow p-6">

            <!-- Warning Alert -->
            <div v-if="room && (room.is_active || room.has_approved_reservation)"
                class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex gap-2">
                    <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="text-sm text-yellow-800">
                        <p class="font-medium mb-1">Perhatian!</p>
                        <p v-if="room.is_active">
                            Ruangan ini sedang <strong>Aktif</strong>. Ubah status menjadi <strong>Tidak Aktif</strong>
                            terlebih dahulu untuk mengedit.
                        </p>
                        <p v-else-if="room.has_approved_reservation">
                            Ruangan ini memiliki <strong>reservasi yang disetujui</strong>. Batalkan reservasi terlebih
                            dahulu untuk mengedit.
                        </p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submitForm">

                <!-- Nama Ruangan -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Ruangan <span class="text-red-500">*</span>
                    </label>
                    <input v-model="form.name" type="text" placeholder="Contoh: Ruang Meeting A" :disabled="isDisabled"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition disabled:bg-gray-100 disabled:cursor-not-allowed"
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
                    <input v-model="form.capacity" type="number" min="1" placeholder="Contoh: 20" :disabled="isDisabled"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition disabled:bg-gray-100 disabled:cursor-not-allowed"
                        :class="{ 'border-red-500': errors.capacity }" />
                    <p v-if="errors.capacity" class="mt-1 text-sm text-red-600">
                        {{ errors.capacity[0] }}
                    </p>
                    <p class="mt-1 text-sm text-gray-500">
                        Kosongkan jika tidak ada batasan
                    </p>
                </div>

                <!-- Deskripsi -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea v-model="form.description" rows="4" placeholder="Deskripsi ruangan..."
                        :disabled="isDisabled"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition resize-none disabled:bg-gray-100 disabled:cursor-not-allowed"
                        :class="{ 'border-red-500': errors.description }"></textarea>
                    <p v-if="errors.description" class="mt-1 text-sm text-red-600">
                        {{ errors.description[0] }}
                    </p>
                </div>

                <!-- Status Info -->
                <div v-if="room" class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Status Ruangan</p>
                            <p class="text-xs text-gray-500 mt-1">Status tidak dapat diubah dari halaman edit</p>
                        </div>
                        <span :class="room.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                            class="px-3 py-1 rounded-full text-sm font-medium">
                            {{ room.status_label || (room.is_active ? 'Aktif' : 'Tidak Aktif') }}
                        </span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3">
                    <button type="submit" :disabled="loading || isDisabled"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed font-medium">
                        {{ loading ? 'Menyimpan...' : 'Update' }}
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
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import roomService from '@/services/roomService';

const router = useRouter();
const route = useRoute();
const loading = ref(false);
const isLoading = ref(true); // ✅ Separate loading untuk initial fetch
const loadError = ref(null); // ✅ Track error
const message = ref(null);
const errors = ref({});
const room = ref(null);

const form = ref({
    name: '',
    capacity: null,
    description: ''
});

const isDisabled = computed(() => {
    return room.value?.is_active || room.value?.has_approved_reservation;
});

const fetchRoom = async () => {
    isLoading.value = true;
    loadError.value = null;

    try {
        console.log("🔍 Fetching room ID:", route.params.id);
        const response = await roomService.getRoomById(route.params.id);

        console.log("📦 Response:", response);
        console.log("📦 Response Data:", response.data);

        // ✅ FIX: Cek berdasarkan status code, bukan success flag
        if (response.status === 200) {
            // ✅ Handle berbagai struktur response
            const data = response.data.data || response.data;

            console.log("✅ Room data:", data);

            room.value = data;
            form.value = {
                name: data.name || '',
                capacity: data.capacity || null,
                description: data.description || ''
            };

            console.log("✅ Form populated:", form.value);
        } else {
            throw new Error('Response tidak valid');
        }
    } catch (error) {
        console.error("❌ Fetch error:", error);
        loadError.value = error.response?.data?.message || 'Gagal memuat data ruangan';

        // Redirect ke index setelah 3 detik jika gagal
        setTimeout(() => {
            router.push('/admin/rooms');
        }, 3000);
    } finally {
        isLoading.value = false;
    }
};

const submitForm = async () => {
    if (isDisabled.value) {
        showMessage('Ruangan tidak dapat diubah saat ini', 'error');
        return;
    }

    loading.value = true;
    errors.value = {};

    try {
        console.log("📤 Updating room:", form.value);
        const response = await roomService.updateRoom(route.params.id, form.value);

        console.log("📦 Update response:", response);

        // ✅ FIX: Cek berdasarkan status code
        if (response.status === 200) {
            showMessage('Ruangan berhasil diupdate', 'success');
            setTimeout(() => {
                router.push('/admin/rooms');
            }, 1500);
        }
    } catch (error) {
        console.error("❌ Update error:", error);

        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
            showMessage('Periksa kembali form Anda', 'error');
        } else if (error.response?.status === 400) {
            showMessage(error.response.data.message, 'error');
        } else {
            showMessage(error.response?.data?.message || 'Gagal menyimpan data', 'error');
        }
    } finally {
        loading.value = false;
    }
};

const showMessage = (text, type = 'info') => {
    message.value = { text, type };
    setTimeout(() => message.value = null, 3000);
};

onMounted(() => {
    fetchRoom();
});
</script>

<style scoped>
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
