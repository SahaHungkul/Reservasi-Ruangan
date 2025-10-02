<script setup>
import { useRoute } from 'vue-router';
import { useUserStore } from '@/stores/user';
import { Button } from '@/components/ui/button';

const router = useRoute();
const userStore = useUserStore();

function handleLogout() {
  userStore.logout();
  router.push('/login');
}
</script>

<template>
  <Button @click="handleLogout" variant="destructive"
    class="text-sm bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded cursor-pointer">Logout</Button>
</template>

<style scoped></style>
