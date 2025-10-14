<template>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Kelola Reservasi</h1>
            <p class="text-gray-600 text-sm mt-1">Kelola semua reservasi ruangan</p>
        </div>
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ stats.pending }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Approved</p>
                        <p class="text-2xl font-bold text-green-600">{{ stats.approved }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Rejected</p>
                        <p class="text-2xl font-bold text-red-600">{{ stats.rejected }}</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-full">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Canceled</p>
                        <p class="text-2xl font-bold text-gray-600">{{ stats.canceled }}</p>
                    </div>
                    <div class="p-3 bg-gray-100 rounded-full">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select v-model="filters.status"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="canceled">Canceled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ruangan</label>
                    <select v-model="filters.room_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="">Semua Ruangan</option>
                        <option v-for="room in rooms" :key="room.id" :value="room.id">{{ room.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                    <input v-model="filters.date" type="date"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <input v-model="filters.search" type="text" placeholder="Cari nama pemohon..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
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
        <div v-else-if="reservations.length === 0" class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Reservasi</h3>
            <p class="text-gray-600">Tidak ada data reservasi yang ditemukan</p>
        </div>

        <!-- Table -->
        <div v-else class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pemohon
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ruangan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal & Waktu
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
                        <tr v-for="reservation in reservations" :key="reservation.id"
                            class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ reservation.user?.name }}</div>
                                <div class="text-xs text-gray-500">{{ reservation.user?.email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ reservation.room?.name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ formatDate(reservation.date) }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ formatTime(reservation.start_time) }} - {{ formatTime(reservation.end_time) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="getStatusClass(reservation.status)"
                                    class="px-2 py-1 text-xs font-medium rounded-full">
                                    {{ getStatusLabel(reservation.status) }}
                                </span>
                                <div v-if="reservation.reason" class="text-xs text-gray-500 mt-1">
                                    {{ reservation.reason }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="viewDetail(reservation)"
                                        class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                        title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button v-if="reservation.status === 'pending'"
                                        @click="openApproveModal(reservation)"
                                        class="p-2 text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg transition"
                                        title="Setujui">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                    <button v-if="reservation.status === 'pending'"
                                        @click="openRejectModal(reservation)"
                                        class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                        title="Tolak">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                    <button @click="confirmDelete(reservation)"
                                        class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                        title="Hapus">
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

        <!-- Detail Modal -->
        <div v-if="showDetailModal" @click="showDetailModal = false"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div @click.stop class="bg-white rounded-lg max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Detail Reservasi</h3>
                    <button @click="showDetailModal = false" class="p-2 hover:bg-gray-100 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div v-if="selectedReservation" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Pemohon</p>
                            <p class="font-medium">{{ selectedReservation.user?.name }}</p>
                            <p class="text-sm text-gray-500">{{ selectedReservation.user?.email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Ruangan</p>
                            <p class="font-medium">{{ selectedReservation.room?.name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tanggal</p>
                            <p class="font-medium">{{ formatDate(selectedReservation.date) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Waktu</p>
                            <p class="font-medium">{{ formatTime(selectedReservation.start_time) }} - {{
                                formatTime(selectedReservation.end_time) }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm text-gray-600">Status</p>
                            <span :class="getStatusClass(selectedReservation.status)"
                                class="inline-block px-3 py-1 text-sm font-medium rounded-full mt-1">
                                {{ getStatusLabel(selectedReservation.status) }}
                            </span>
                        </div>
                        <div v-if="selectedReservation.reason" class="col-span-2">
                            <p class="text-sm text-gray-600">Alasan (Reject/Cancel)</p>
                            <p class="font-medium">{{ selectedReservation.reason }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approve Modal -->
        <div v-if="showApproveModal" @click="showApproveModal = false"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div @click.stop class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Setujui Reservasi</h3>
                        <p class="text-sm text-gray-600 mt-1">Konfirmasi persetujuan reservasi</p>
                    </div>
                </div>
                <p class="text-gray-700 mb-6">
                    Apakah Anda yakin ingin menyetujui reservasi dari <strong>{{ reservationToApprove?.user?.name
                    }}</strong>?
                </p>
                <div class="flex gap-3">
                    <button @click="approveReservation" :disabled="loading"
                        class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition disabled:opacity-50">
                        {{ loading ? 'Memproses...' : 'Setujui' }}
                    </button>
                    <button @click="showApproveModal = false"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Batal
                    </button>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div v-if="showRejectModal" @click="showRejectModal = false"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div @click.stop class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-red-100 rounded-full">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Tolak Reservasi</h3>
                        <p class="text-sm text-gray-600 mt-1">Berikan alasan penolakan</p>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span
                            class="text-red-500">*</span></label>
                    <textarea v-model="rejectReason" rows="4"
                        placeholder="Contoh: Ruangan sudah dipesan untuk acara lain"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent outline-none transition resize-none"></textarea>
                </div>
                <div class="flex gap-3">
                    <button @click="rejectReservation" :disabled="loading || !rejectReason"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition disabled:opacity-50">
                        {{ loading ? 'Memproses...' : 'Tolak' }}
                    </button>
                    <button @click="showRejectModal = false"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Batal
                    </button>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" @click="showDeleteModal = false"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div @click.stop class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-red-100 rounded-full">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Hapus Reservasi</h3>
                        <p class="text-sm text-gray-600 mt-1">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>
                <p class="text-gray-700 mb-6">
                    Apakah Anda yakin ingin menghapus reservasi dari <strong>{{ reservationToDelete?.user?.name
                    }}</strong>?
                </p>
                <div class="flex gap-3">
                    <button @click="deleteReservation" :disabled="loading"
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
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import reservationService from '@/services/reservationService';
import roomService from '@/services/roomService';

const route = useRoute();
const isLoading = ref(true);
const loading = ref(false);
const message = ref(null);
const reservations = ref([]);
const rooms = ref([]);

const showDetailModal = ref(false);
const showApproveModal = ref(false);
const showRejectModal = ref(false);
const showDeleteModal = ref(false);

const selectedReservation = ref(null);
const reservationToApprove = ref(null);
const reservationToReject = ref(null);
const reservationToDelete = ref(null);
const rejectReason = ref('');

const filters = ref({
    status: '',
    room_id: '',
    date: '',
    search: ''
});

const stats = computed(() => {
    return {
        pending: reservations.value.filter(r => r.status === 'pending').length,
        approved: reservations.value.filter(r => r.status === 'approved').length,
        rejected: reservations.value.filter(r => r.status === 'rejected').length,
        canceled: reservations.value.filter(r => r.status === 'canceled').length
    };
});

const getStatusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-800',
        approved: 'bg-green-100 text-green-800',
        rejected: 'bg-red-100 text-red-800',
        canceled: 'bg-gray-100 text-gray-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status) => {
    const labels = {
        pending: 'Pending',
        approved: 'Disetujui',
        rejected: 'Ditolak',
        canceled: 'Dibatalkan'
    };
    return labels[status] || status;
};

const formatDate = (date) => {
    if (!date) return '';
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(date).toLocaleDateString('id-ID', options);
};

const formatTime = (time) => {
    if (!time) return '';
    return time.substring(0, 5); // HH:MM
};

const fetchReservations = async () => {
    isLoading.value = true;
    try {
        const response = await reservationService.getAll(filters.value);
        if (response.status === 200) {
            reservations.value = response.data.data || response.data;
        }

        // DUMMY DATA untuk testing
        // await new Promise(resolve => setTimeout(resolve, 1000));
        // reservations.value = [
        //     {
        //         id: 1,
        //         user: { id: 1, name: 'John Doe', email: 'john@example.com' },
        //         room: { id: 1, name: 'Ruang Meeting A' },
        //         date: '2025-10-15',
        //         start_time: '08:00:00',
        //         end_time: '10:00:00',
        //         status: 'pending',
        //         reason: null
        //     },
        //     {
        //         id: 2,
        //         user: { id: 2, name: 'Jane Smith', email: 'jane@example.com' },
        //         room: { id: 2, name: 'Ruang Kuliah 101' },
        //         date: '2025-10-16',
        //         start_time: '13:00:00',
        //         end_time: '15:00:00',
        //         status: 'approved',
        //         reason: null
        //     },
        //     {
        //         id: 3,
        //         user: { id: 3, name: 'Bob Johnson', email: 'bob@example.com' },
        //         room: { id: 1, name: 'Ruang Meeting A' },
        //         date: '2025-10-17',
        //         start_time: '09:00:00',
        //         end_time: '11:00:00',
        //         status: 'rejected',
        //         reason: 'Ruangan sudah dipesan untuk acara lain'
        //     }
        // ];
    } catch (error) {
        console.error('Fetch error:', error);
        showMessage('Gagal memuat data reservasi', 'error');
    } finally {
        isLoading.value = false;
    }
};

const fetchRooms = async () => {
    try {
        const response = await roomService.getAll();
        if (response.status === 200) {
            rooms.value = response.data.data || response.data;
        }

        // DUMMY DATA
        // rooms.value = [
        //     { id: 1, name: 'Ruang Meeting A' },
        //     { id: 2, name: 'Ruang Kuliah 101' },
        //     { id: 3, name: 'Ruang Seminar' }
        // ];
    } catch (error) {
        console.error('Fetch rooms error:', error);
    }
};

const applyFilters = () => {
    fetchReservations();
};

const resetFilters = () => {
    filters.value = {
        status: '',
        room_id: '',
        date: '',
        search: ''
    };
    fetchReservations();
};

const viewDetail = (reservation) => {
    selectedReservation.value = reservation;
    showDetailModal.value = true;
};

const openApproveModal = (reservation) => {
    reservationToApprove.value = reservation;
    showApproveModal.value = true;
};

const openRejectModal = (reservation) => {
    reservationToReject.value = reservation;
    rejectReason.value = '';
    showRejectModal.value = true;
};

const confirmDelete = (reservation) => {
    reservationToDelete.value = reservation;
    showDeleteModal.value = true;
};

const approveReservation = async () => {
    loading.value = true;
    try {
        console.log("✅ Approving reservation:", reservationToApprove.value.id);

        // const response = await reservationService.approve(reservationToApprove.value.id);
        // if (response.status === 200) {
        const index = reservations.value.findIndex(r => r.id === reservationToApprove.value.id);
        if (index !== -1) {
            reservations.value[index].status = 'approved';
        }
        showMessage('Reservasi berhasil disetujui', 'success');
        showApproveModal.value = false;
        // }
    } catch (error) {
        console.error("❌ Approve error:", error);
        showMessage('Gagal menyetujui reservasi', 'error');
    } finally {
        loading.value = false;
    }
};

const rejectReservation = async () => {
    if (!rejectReason.value.trim()) {
        showMessage('Alasan penolakan harus diisi', 'error');
        return;
    }

    loading.value = true;
    try {
        console.log("❌ Rejecting reservation:", reservationToReject.value.id);

        // const response = await reservationService.reject(reservationToReject.value.id, {
        //     reason: rejectReason.value
        // });
        // if (response.status === 200) {
        const index = reservations.value.findIndex(r => r.id === reservationToReject.value.id);
        if (index !== -1) {
            reservations.value[index].status = 'rejected';
            reservations.value[index].reason = rejectReason.value;
        }
        showMessage('Reservasi berhasil ditolak', 'success');
        showRejectModal.value = false;
        // }
    } catch (error) {
        console.error("❌ Reject error:", error);
        showMessage('Gagal menolak reservasi', 'error');
    } finally {
        loading.value = false;
    }
};

const deleteReservation = async () => {
    loading.value = true;
    try {
        console.log("🗑️ Deleting reservation:", reservationToDelete.value.id);

        // await reservationService.delete(reservationToDelete.value.id);
        reservations.value = reservations.value.filter(r => r.id !== reservationToDelete.value.id);
        showMessage('Reservasi berhasil dihapus', 'success');
        showDeleteModal.value = false;
    } catch (error) {
        console.error("❌ Delete error:", error);
        showMessage('Gagal menghapus reservasi', 'error');
    } finally {
        loading.value = false;
    }
};

const showMessage = (text, type = 'info') => {
    message.value = { text, type };
    setTimeout(() => message.value = null, 3000);
};

onMounted(() => {
    fetchReservations();
    fetchRooms();

    // Check for success message from query params
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
