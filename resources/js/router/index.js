import { createRouter, createWebHistory } from "vue-router";

import AdminLayout from "../layouts/AdminLayout.vue";
// contoh komponen (pastikan file App.vue atau lainnya sudah ada)
import Home from "../pages/Home.vue";
import Dashboard from "../pages/admin/Dashboard.vue";
import RoomAdmin from "@/pages/admin/Room.vue";
import ReservationAdmin from "@/pages/admin/Reservation.vue";
import FixedSchedulesAdmin from "@/pages/admin/FixedSchedule.vue";

const routes = [
    {
        path: "/",
        name: "home",
        component: Home,
    },
    {
        path: "/admin",
        component: AdminLayout,
        children: [
            { path: "dashboard", name: "AdminDashboard", component: Dashboard },
            { path: 'rooms', name: 'RoomAdmin', component:RoomAdmin},
            { path: 'reservations',name: 'ReservationAdmin', component:ReservationAdmin},
            { path: 'fixed-schedules',name: 'FixedSchedulesAdmin', component:FixedSchedulesAdmin},
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
