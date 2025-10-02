import { createRouter, createWebHistory } from "vue-router";

import AdminLayout from "../layouts/AdminLayout.vue";
// contoh komponen (pastikan file App.vue atau lainnya sudah ada)

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
        name: "LoginPage",
        component: Login,
    },
    {
        path: "/admin",
        component: AdminLayout,
        children: [
            { path: "dashboard", name: "AdminDashboard", component: Dashboard },
            { path: 'rooms', name: 'RoomAdmin', component:RoomAdmin},
            { path: 'reservations',name: 'ReservationAdmin', component:ReservationAdmin},
            { path: 'fixed-schedules',name: 'FixedSchedulesAdmin', component:FixedSchedulesAdmin},
            { path: 'rooms/create', name:'CreateRoom', component:CreateRoom}
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
  const role = localStorage.getItem('role')
  if (to.meta.role && to.meta.role !== role) {
    return next('/login')
  }
  next()
})

export default router;
