<script setup>
import { ref, onMounted } from "vue"
import axios from "axios"
import AddButton from "@/components/AddButton.vue"
import EditButton from "@/components/EditButton.vue"
import DeleteButton from "@/components/DeleteButton.vue"
import { useRouter } from "vue-router"

const router = useRouter();
const fixedSchedule = ref([]);
const loading = ref(true);
const error = ref(null);

onMounted(async () => {
    try {
        const res = await axios.get("/fixed-schedules")
        fixedSchedule.value = res.data.data;
    } catch (err) {
        error.value = "Gagal memuat data"
        console.error(err)
    } finally {
        loading.value = false
    }

    async function deleteSchedule(id) {
        if (!confirm("Yakin ingin menghapus Schedule ini?")) return;
        try {
            await axios.delete(`/api/fixed-schedules/${id}`);
            alert("Schedule deleted!");
            // reload data atau emit event
        } catch (err) {
            alert("Gagal menghapus schedule");
        }
    }
});

function goToCreateFixedSchedule() {
    router.push({ name: "fixed-schedules.create" })
}
function goToEdit(id) {
    router.push({ name: "fixed-schedules.edit", params: { id } })
}
</script>

<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Daftar Schedule</h1>

        <AddButton @click="goToCreateFixedSchedule()" class="ml-4"></AddButton>

        <div v-if="loading">Loading...</div>
        <div v-else-if="error" class="text-red-500">{{ error }}</div>

        <ul v-else class="space-y-2">
            <li v-for="fixedSchedule in fixedSchedule" :key="fixedSchedule.id"
                class="p-2 border rounded bg-white shadow">
                -id:{{ fixedSchedule.id }} - room: {{ fixedSchedule.room?.id }} - Hari: {{ fixedSchedule.day_of_week }}
                - start {{ fixedSchedule.start_time }} - end: {{ fixedSchedule.end_time }} - deskripsi {{
                    fixedSchedule.description }}
                <EditButton @click="goToEdit(fixedSchedule.id)" />
                <DeleteButton @click="deleteSchedule(fixedSchedule.id)" />
            </li>
        </ul>
    </div>
</template>
