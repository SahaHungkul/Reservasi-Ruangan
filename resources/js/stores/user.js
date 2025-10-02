import { defineStore } from "pinia";
import axios from "axios";

export const useUserStore = defineStore("user", {
    state: () => ({
        user: null,
        role: null,
        token: null,
    }),
    actions: {
        async login(email, password) {
            const { data } = await axios.post("/api/auth/login", {
                email,
                password,
            });
            console.log('Login response:', data)
            console.log('Role:', data.data.user.role)

            this.user = data.user;
            this.role = data.data.user.role;
            this.token = data.token;

            localStorage.setItem("token", this.token);
            localStorage.setItem("role", this.role);

            return this.role;
        },
        logout() {
            this.user = null;
            this.role = null;
            this.token = null;
            localStorage.clear();
        },
    },
});
