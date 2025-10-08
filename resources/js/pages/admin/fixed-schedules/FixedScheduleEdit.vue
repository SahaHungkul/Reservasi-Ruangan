<template>
    <div class="max-w-2xl mx-auto px-4 py-8">

        <!-- Header -->
        <div class="flex items-center gap-4 mb-6">
            <router-link to="/admin/fixed-schedules" class="p-2 hover:bg-gray-100 rounded-lg transition">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </router-link>
            <h1 class="text-2xl font-bold text-gray-800">Edit Jadwal Tetap</h1>
        </div>

        <!-- Loading State -->
        <div v-if="isLoading" class="bg-white rounded-lg shadow p-12 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent">
            </div>
            <p class="mt-2 text-gray-600">Memuat data...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="loadError" class="bg-white rounded-lg shadow p-6">
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Gagal Memuat Data</h3>
                <p class="text-gray-600 mb-4">{{ loadError }}</p>
                <button @click="fetchSchedule" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Coba Lagi
                </button>
            </div>
        </div>

        <!-- Form Card -->
        <div v-else class="bg-white rounded-lg shadow p-6">

            <form @submit.prevent="submitForm">

                <!-- Ruangan -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Ruangan <span class="text-red-500">*</span>
                    </label>
                    <select v-model="form.room_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        :class="{ 'border-red-500': errors.room_id }">
                        <option value="">Pilih Ruangan</option>
                        <option v-for="room in rooms" :key="room.id" :value="room.id">{{ room.name }}</option>
                    </select>
                    <p v-if="errors.room_id" class="mt-1 text-sm text-red-600">
                        {{ errors.room_id[0] }}
                    </p>
                </div>

                <!-- Hari -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Hari <span class="text-red-500">*</span>
                    </label>
                    <select v-model="form.day_of_week"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                        :class="{ 'border-red-500': errors.day_of_week }">
                        <option value="">Pilih Hari</option>
                        <option value="monday">Senin</option>
                        <option value="tuesday">Selasa</option>
                        <option value="wednesday">Rabu</option>
                        <option value="thursday">Kamis</option>
                        <option value="friday">Jumat</option>
                        <option value="saturday">Sabtu</option>
                        <option value="sunday">Minggu</option>
                    </select>
                    <p v-if="errors.day_of_week" class="mt-1 text-sm text-red-600">
                        {{ errors.day_of_week[0] }}
                    </p>
                </div>

                <!-- Waktu -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Waktu Mulai <span class="text-red-500">*</span>
                        </label>
                        <input v-model="form.start_time" type="time"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                            :class="{ 'border-red-500': errors.start_time }" />
                        <p v-if="errors.start_time" class="mt-1 text-sm text-red-600">
                            {{ errors.start_time[0] }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Waktu Selesai <span class="text-red-500">*</span>
                        </label>
                        <input v-model="form.end_time" type="time"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                            :class="{ 'border-red-500': errors.end_time }" />
                        <p v-if="errors.end_time" class="mt-1 text-sm text-red-600">
                            {{ errors.end_time[0] }}
                        </p>
                    </div>
                </div>

                <!-- Deskripsi -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea v-model="form.description" rows="4" placeholder="Contoh: Weekly Standup"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition resize-none"
                        :class="{ 'border-red-500': errors.description }"></textarea>
                    <p v-if="errors.description" class="mt-1 text-sm text-red-600">
                        {{ errors.description[0] }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500">Contoh: "Weekly Standup"</p>
                </div>

                <!-- Actions -->
                <div class="flex gap-3">
                    <button type="submit" :disabled="loading"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed font-medium">
                        {{ loading ? 'Menyimpan...' : 'Update' }}
                    </button>
                    <router-link to="/admin/fixed-schedules"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                        Batal
                    </router-link>
                </div>
            </form>
        </div>

        <!-- Toast -->
        <Transition name="slide-fade">
            <div v-if="message" :class="message.type === 'success' ? 'bg-green-600' : 'bg-red-600'"
                class="fixed bottom-4 right-4 px-6 py-3 text-white rounded-lg shadow-lg flex items-center gap-3 z-50">
                <svg v-if="message.type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
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
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import fixedScheduleService from '@/services/fixedScheduleService';
import roomService from '@/services/roomService';

const router = useRouter();
const route = useRoute();
const loading = ref(false);
const isLoading = ref(true);
const loadError = ref(null);
const message = ref(null);
const errors = ref({});
const schedule = ref(null);
const rooms = ref([]);

const form = ref({
    room_id: '',
    day_of_week: '',
    start_time: '',
    end_time: '',
    description: ''
});

const fetchSchedule = async () => {
    isLoading.value = true;
    loadError.value = null;

    try {
        console.log("🔍 Fetching schedule ID:", route.params.id);

        const response = await fixedScheduleService.getById(route.params.id);

        if (response.status === 200) {
            const data = response.data.data || response.data;
            console.log("✅ Schedule data:", data);

            schedule.value = data;
            form.value = {
                room_id: data.room_id || '',
                day_of_week: data.day_of_week || '',
                start_time: data.start_time || '',
                end_time: data.end_time || '',
                description: data.description || ''
            };

            console.log("✅ Form populated:", form.value);
        } else {
            throw new Error('Response tidak valid');
        }
    } catch (error) {
        console.error("❌ Fetch error:", error);
        loadError.value = error.response?.data?.message || 'Gagal memuat data jadwal';

        setTimeout(() => {
            router.push('/admin/fixed-schedules');
        }, 3000);
    } finally {
        isLoading.value = false;
    }
};

const fetchRooms = async () => {
    try {
        const response = await roomService.getAllRooms({ is_active: 1 });
        if (response.status === 200) {
            rooms.value = response.data.data || response.data;
        }
    } catch (error) {
        console.error('Fetch rooms error:', error);
    }
};

const submitForm = async () => {
    loading.value = true;
    errors.value = {};

    try {
        console.log("📤 Updating schedule:", form.value);

        const response = await fixedScheduleService.update(route.params.id, form.value);

        if (response.status === 200) {
            showMessage('Jadwal berhasil diupdate', 'success');
            setTimeout(() => {
                router.push('/admin/fixed-schedules');
            }, 1500);
        }
    } catch (error) {
        console.error("❌ Update error:", error);

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
    setTimeout(() => message.value = null, 3000);
};

onMounted(() => {
    fetchSchedule();
    fetchRooms();
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
