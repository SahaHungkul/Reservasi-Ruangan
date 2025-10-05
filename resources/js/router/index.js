import { createRouter, createWebHistory } from "vue-router";
import { useUserStore } from "@/stores/user";

import AdminLayout from "../layouts/AdminLayout.vue";
import KaryawanLayout from "@/layouts/KaryawanLayout.vue";
// contoh komponen (pastikan file App.vue atau lainnya sudah ada)

import Register from "@/pages/Register.vue";
import Login from "@/pages/Login.vue";
import Home from "../pages/Home.vue";
import Dashboard from "@/pages/admin/Dashboard.vue";

import IndexRoomAdmin from "@/pages/admin/rooms/Index.vue";
import CreateRoom from "@/pages/admin/rooms/CreateRoom.vue";
import EditRoom from "@/pages/admin/rooms/Edit.vue";

import FixedSchedulesAdmin from "@/pages/admin/fixed-schedules/FixedSchedule.vue";
import FixedScheduleCreate from "@/pages/admin/fixed-schedules/Create.vue";
import FixedScheduleEdit from "@/pages/admin/fixed-schedules/FixedScheduleEdit.vue";

import ReservationAdmin from "@/pages/admin/Reservation.vue";

import About from "@/pages/About.vue";

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
            {
                path: "dashboard",
                name: "AdminDashboard",
                component: Dashboard,
            },
            {
                path: "rooms",
                name: "IndexRoomAdmin",
                component: IndexRoomAdmin,
            },
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
            {
                path: "fixed-schedules/create",
                name: "fixed-schedules.create",
                component: FixedScheduleCreate,
            },
            {
                path: "fixed-schedules/:id/edit",
                name: "fixed-schedules.edit",
                component: FixedScheduleEdit,
            },
            {
                path: "rooms/create",
                name: "CreateRoom",
                component: CreateRoom,
            },
            {
                path: "rooms/:id/edit",
                name: "admin.rooms.edit",
                component: EditRoom,
            },
        ],

    },
    {
    path: "/karyawan",
    component: KaryawanLayout,
    children: [
    //   {
    //     path: "fixed-schedules",
    //     name: "fixed-schedules.index",
    //     component: FixedScheduleIndex,
    //   },
    //   {
    //     path: "fixed-schedules/create",
    //     name: "fixed-schedules.create",
    //     component: FixedScheduleCreate,
    //   },
    //   {
    //     path: "fixed-schedules/:id/edit",
    //     name: "fixed-schedules.edit",
    //     component: FixedScheduleEdit,
    //     props: true,
    //   },
      // nanti bisa tambahin rooms, reservations dsb.
      {
        path: "about",
        name: "about",
        component: About,
      }
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
