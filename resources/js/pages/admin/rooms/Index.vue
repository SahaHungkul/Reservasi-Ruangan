<!-- ===============================================
FILE: resources/js/pages/rooms/Index.vue
=============================================== -->

<template>
    <div class="max-w-6xl mx-auto px-4 py-8">

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Daftar Ruangan</h1>
            <router-link to="/admin/rooms/create"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                + Tambah Ruangan
            </router-link>
        </div>

        <!-- Filters -->
        <div class="flex gap-2 mb-6">
            <button @click="filterStatus = 'all'"
                :class="filterStatus === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                class="px-4 py-2 rounded-lg transition">
                Semua ({{ rooms.length }})
            </button>
            <button @click="filterStatus = 'active'"
                :class="filterStatus === 'active' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
                class="px-4 py-2 rounded-lg transition">
                Aktif ({{ activeCount }})
            </button>
            <button @click="filterStatus = 'inactive'"
                :class="filterStatus === 'inactive' ? 'bg-gray-600 text-white' : 'bg-gray-200 text-gray-700'"
                class="px-4 py-2 rounded-lg transition">
                Tidak Aktif ({{ inactiveCount }})
            </button>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent">
            </div>
            <p class="mt-2 text-gray-600">Memuat data...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="filteredRooms.length === 0" class="text-center py-12 bg-white rounded-lg shadow">
            <p class="text-gray-600 mb-4">Belum ada ruangan</p>
            <router-link to="/admin/rooms/create"
                class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                + Tambah Ruangan
            </router-link>
        </div>

        <!-- Table -->
        <div v-else class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Nama Ruangan</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Kapasitas</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Status</th>
                        <th class="px-6 py-3 text-center text-sm font-medium text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="room in filteredRooms" :key="room.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ room.name }}</div>
                            <div v-if="room.description" class="text-sm text-gray-500">{{ room.description }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ room.capacity || '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <span :class="room.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                class="px-3 py-1 rounded-full text-xs font-medium">
                                {{ room.status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">
                                <!-- Toggle Status -->
                                <button @click="toggleStatus(room)"
                                    :class="room.is_active ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-green-100 text-green-600 hover:bg-green-200'"
                                    class="px-3 py-1 rounded text-sm transition"
                                    :title="room.is_active ? 'Nonaktifkan' : 'Aktifkan'">
                                    {{ room.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>

                                <!-- Edit -->
                                <router-link :to="`/admin/rooms/${room.id}/edit`"
                                    :class="room.is_active ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-100'"
                                    class="px-3 py-1 bg-blue-50 text-blue-600 rounded text-sm transition"
                                    @click.prevent="room.is_active ? showMessage('Nonaktifkan ruangan terlebih dahulu', 'error') : $router.push(`/admin/rooms/${room.id}/edit`)">
                                    Edit
                                </router-link>

                                <!-- Delete -->
                                <button @click="deleteRoom(room)" :disabled="room.is_active"
                                    :class="room.is_active ? 'opacity-50 cursor-not-allowed' : 'hover:bg-red-100'"
                                    class="px-3 py-1 bg-red-50 text-red-600 rounded text-sm transition">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Toast -->
        <div v-if="message" :class="message.type === 'success' ? 'bg-green-600' : 'bg-red-600'"
            class="fixed bottom-4 right-4 px-6 py-3 text-white rounded-lg shadow-lg">
            {{ message.text }}
        </div>

    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import roomService from '@/services/roomService';

const router = useRouter();
const rooms = ref([]);
const loading = ref(false);
const filterStatus = ref('all');
const message = ref(null);

const filteredRooms = computed(() => {
    if (filterStatus.value === 'all') return rooms.value;
    return rooms.value.filter(room =>
        filterStatus.value === 'active' ? room.is_active : !room.is_active
    );
});

const activeCount = computed(() => rooms.value.filter(r => r.is_active).length);
const inactiveCount = computed(() => rooms.value.filter(r => !r.is_active).length);

const fetchRooms = async () => {
    loading.value = true;
    try {
        const response = await roomService.getAllRooms();
        if (response.data.success) {
            rooms.value = response.data.data;
        }
    } catch (error) {
        showMessage('Gagal memuat data', 'error');
    } finally {
        loading.value = false;
    }
};

const toggleStatus = async (room) => {
    const action = room.is_active ? 'menonaktifkan' : 'mengaktifkan';
    if (!confirm(`Yakin ${action} "${room.name}"?`)) return;

    try {
        const response = await roomService.toggleStatus(room.id);
        if (response.data.success) {
            showMessage(response.data.message, 'success');
            fetchRooms();
        }
    } catch (error) {
        showMessage('Gagal mengubah status', 'error');
    }
};

const deleteRoom = async (room) => {
    if (room.is_active) {
        showMessage('Nonaktifkan ruangan terlebih dahulu', 'error');
        return;
    }

    if (!confirm(`Hapus "${room.name}"?\n\nTindakan ini tidak dapat dibatalkan.`)) return;

    try {
        console.log('🗑️ Deleting room:', room.id);
        const response = await roomService.deleteRoom(room.id);
        console.log('✅ Delete response:', response.data);

        if (response.data.success) {
            // Langsung update UI tanpa fetch ulang
            rooms.value = rooms.value.filter(r => r.id !== room.id);
            showMessage(response.data.message, 'success');
        }
    } catch (error) {
        console.error('❌ Delete error:', error);
        console.error('Response:', error.response?.data);

        // Handle specific error messages
        if (error.response?.status === 400) {
            showMessage(error.response.data.message, 'error');
        } else if (error.response?.status === 404) {
            showMessage('Ruangan tidak ditemukan', 'error');
        } else {
            showMessage(error.response?.data?.message || 'Gagal menghapus ruangan', 'error');
        }
    }
};

const showMessage = (text, type = 'info') => {
    message.value = { text, type };
    setTimeout(() => message.value = null, 3000);
};

onMounted(() => {
    fetchRooms();
});
</script>
