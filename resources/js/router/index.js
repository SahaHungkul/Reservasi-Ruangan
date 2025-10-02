import { createRouter, createWebHistory } from "vue-router";
import { useUserStore } from "@/stores/user";

import AdminLayout from "../layouts/AdminLayout.vue";
// contoh komponen (pastikan file App.vue atau lainnya sudah ada)

import Register from "@/pages/Register.vue";
import Login from "@/pages/Login.vue";
import Home from "../pages/Home.vue";
import Dashboard from "../pages/admin/Dashboard.vue";

import RoomAdmin from "@/pages/admin/rooms/Room.vue";
import CreateRoom from "@/pages/admin/rooms/CreateRoom.vue";
import ReservationAdmin from "@/pages/admin/Reservation.vue";
import FixedSchedulesAdmin from "@/pages/admin/FixedSchedule.vue";

const routes = [
    {
        path: "/",
        name: "home",
        component: Home,
    },
    {
        path: "/login",
        name: "login",
        component: Login,
    },
    {
        path: "/register",
        name: "register",
        component: Register,
    },
    {
        path: "/admin",
        component: AdminLayout,
        children: [
            { path: "dashboard", name: "AdminDashboard", component: Dashboard },
            { path: "rooms", name: "RoomAdmin", component: RoomAdmin },
            {
                path: "reservations",
                name: "ReservationAdmin",
                component: ReservationAdmin,
            },
            {
                path: "fixed-schedules",
                name: "FixedSchedulesAdmin",
                component: FixedSchedulesAdmin,
            },
            { path: "rooms/create", name: "CreateRoom", component: CreateRoom },
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const userStore = useUserStore();
    const role = localStorage.getItem("role");

    // cek kalau butuh login
    if (to.meta.requiresAuth && !userStore.token) {
        return next({ name: "login" });
    }

    // cek kalau butuh role tertentu
    if (to.meta.role && to.meta.role !== role) {
        return next({ name: "login" });
    }

    next();
});

export default router;
