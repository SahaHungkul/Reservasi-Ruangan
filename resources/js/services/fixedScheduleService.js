import axios from "./axios";

const fixedScheduleService = {
    getAllSchedules(params = {}) {
        return axios.get("/api/fixed-schedules", { params });
    },

    getById(id) {
        return axios.get(`/api/fixed-schedules/${id}`);
    },

    create(data) {
        return axios.post("/api/fixed-schedules", data);
    },

    update(id, data) {
        return axios.put(`/api/fixed-schedules/${id}`, data);
    },

    delete(id) {
        return axios.delete(`/api/fixed-schedules/${id}`);
    },
};

export default fixedScheduleService;
