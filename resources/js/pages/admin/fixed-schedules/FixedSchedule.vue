<template>
    <div class="max-w-7xl mx-auto px-4 py-8">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Jadwal Tetap</h1>
                <p class="text-gray-600 text-sm mt-1">Kelola jadwal tetap untuk ruangan</p>
            </div>
            <router-link to="/admin/fixed-schedules/create"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Jadwal</span>
            </router-link>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ruangan</label>
                    <select v-model="filters.room_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="">Semua Ruangan</option>
                        <option v-for="room in rooms" :key="room.id" :value="room.id">{{ room.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hari</label>
                    <select v-model="filters.day_of_week"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="">Semua Hari</option>
                        <option value="monday">Senin</option>
                        <option value="tuesday">Selasa</option>
                        <option value="wednesday">Rabu</option>
                        <option value="thursday">Kamis</option>
                        <option value="friday">Jumat</option>
                        <option value="saturday">Sabtu</option>
                        <option value="sunday">Minggu</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select v-model="filters.status"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="">Semua Status</option>
                        <option value="active">Aktif</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button @click="applyFilters"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Terapkan Filter
                </button>
                <button @click="resetFilters"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Reset
                </button>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="isLoading" class="bg-white rounded-lg shadow p-12 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent">
            </div>
            <p class="mt-2 text-gray-600">Memuat data...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="schedules.length === 0" class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Jadwal</h3>
            <p class="text-gray-600 mb-4">Mulai dengan menambahkan jadwal tetap pertama</p>
            <router-link to="/admin/fixed-schedules/create"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Jadwal</span>
            </router-link>
        </div>

        <!-- Table -->
        <div v-else class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ruangan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Hari
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Waktu
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Deskripsi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="schedule in schedules" :key="schedule.id" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ schedule.room?.name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ getDayLabel(schedule.day_of_week) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ formatTime(schedule.start_time) }} - {{ formatTime(schedule.end_time) }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500">{{ schedule.description || '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <router-link :to="`/admin/fixed-schedules/${schedule.id}/edit`"
                                        class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </router-link>
                                    <button @click="confirmDelete(schedule)"
                                        class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-red-100 rounded-full">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Hapus Jadwal</h3>
                        <p class="text-sm text-gray-600 mt-1">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>
                <p class="text-gray-700 mb-6">
                    Apakah Anda yakin ingin menghapus jadwal <strong>{{ getDayLabel(scheduleToDelete?.day_of_week) }}
                        ({{ formatTime(scheduleToDelete?.start_time) }} - {{ formatTime(scheduleToDelete?.end_time)
                        }})</strong>?
                </p>
                <div class="flex gap-3">
                    <button @click="deleteSchedule" :disabled="loading"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition disabled:opacity-50">
                        {{ loading ? 'Menghapus...' : 'Hapus' }}
                    </button>
                    <button @click="showDeleteModal = false"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Batal
                    </button>
                </div>
            </div>
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
import { useRoute } from 'vue-router';
import fixedScheduleService from '@/services/fixedScheduleService';
import roomService from '@/services/roomService';

const route = useRoute();
const isLoading = ref(true);
const loading = ref(false);
const message = ref(null);
const schedules = ref([]);
const rooms = ref([]);
const showDeleteModal = ref(false);
const scheduleToDelete = ref(null);

const filters = ref({
    room_id: '',
    day_of_week: '',
    status: ''
});

const dayLabels = {
    monday: 'Senin',
    tuesday: 'Selasa',
    wednesday: 'Rabu',
    thursday: 'Kamis',
    friday: 'Jumat',
    saturday: 'Sabtu',
    sunday: 'Minggu'
};

const getDayLabel = (day) => dayLabels[day] || day;

const formatTime = (time) => {
    if (!time) return '';
    return time.substring(0, 5); // HH:MM
};

const fetchSchedules = async () => {
    isLoading.value = true;
    try {
        const response = await fixedScheduleService.getAllSchedules(filters.value);
        console.log('📦 FixedSchedule Response:', response.data); // <-- lihat struktur data

        if (response.status === 200) {
            const res = response.data;

            // Tangani struktur yang berbeda
            if (Array.isArray(res.data)) {
                schedules.value = res.data; // jika pakai ->get()
            } else if (res.data && Array.isArray(res.data.data)) {
                schedules.value = res.data.data; // jika pakai ->paginate()
            } else {
                schedules.value = []; // fallback
            }
        }
    } catch (error) {
        console.error('❌ Fetch error:', error);
        showMessage('Gagal memuat data jadwal', 'error');
    } finally {
        isLoading.value = false;
    }
};

const fetchRooms = async () => {
    try {
        const response = await roomService.getAllRooms();
        if (response.status === 200) {
            rooms.value = response.data.data || response.data;
        }
    } catch (error) {
        console.error('Fetch rooms error:', error);
    }
};

const applyFilters = () => {
    fetchSchedules();
};

const resetFilters = () => {
    filters.value = {
        room_id: '',
        day_of_week: '',
        status: ''
    };
    fetchSchedules();
};

const confirmDelete = (schedule) => {
    scheduleToDelete.value = schedule;
    showDeleteModal.value = true;
};

const deleteSchedule = async () => {
    loading.value = true;
    try {
        await fixedScheduleService.delete(scheduleToDelete.value.id);
        schedules.value = schedules.value.filter(s => s.id !== scheduleToDelete.value.id);
        showMessage('Jadwal berhasil dihapus', 'success');
        showDeleteModal.value = false;
    } catch (error) {
        showMessage('Gagal menghapus jadwal', 'error');
    } finally {
        loading.value = false;
    }
};

const showMessage = (text, type = 'info') => {
    message.value = { text, type };
    setTimeout(() => message.value = null, 3000);
};

onMounted(() => {
    console.log("🚀 Mounted FixedSchedule.vue");
    fetchSchedules();
    fetchRooms();

    // Check for success message from create/edit
    if (route.query.success) {
        showMessage(route.query.success, 'success');
    }
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
