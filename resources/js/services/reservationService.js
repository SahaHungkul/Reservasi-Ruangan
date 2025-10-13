import axios from './axios';

const reservationService = {
    getAllReservations(params = {}) {
        return axios.get('/api/reservations', { params });
    },
    getReservationsById(id) {
        return axios.get(`/api/reservations/${id}`);
    },
    createReservation(data) {
        return axios.post('/api/reservations', data);
    },
    approveReservation(id) {
        return axios.patch(`/api/reservations/${id}/approve`);
    },
    rejectReservation(id) {
        return axios.patch(`/api/reservations/${id}/reject`);
    },
    cancelReservation(id) {
        return axios.patch(`/api/reservations/${id}/cancel`);
    },
};

export default reservationService;
