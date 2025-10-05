import axios from './axios';

const roomService = {
  /**
   * Get all rooms
   * @returns {Promise}
   */
  getAllRooms() {
    console.log('🔍Fetching all rooms');
    return axios.get('/api/rooms');
  },

  /**
   * Get room by ID
   * @param {number|string} id - Room ID
   * @returns {Promise}
   */
  getRoomById(id) {
    console.log(`🔍Fetching room with ID: ${id}`);
    return axios.get(`/api/rooms/${id}`);
  },

  /**
   * Get active rooms only
   * @returns {Promise}
   */
  getActiveRooms() {
    console.log('🔍Fetching active rooms');
    return axios.get('/api/rooms/active');
  },

  /**
   * Create new room
   * @param {Object} data - Room data
   * @param {string} data.name - Room name (required)
   * @param {number} data.capacity - Room capacity (optional)
   * @param {string} data.description - Room description (optional)
   * @returns {Promise}
   */
  createRoom(data) {
    console.log('➕Creating new room:', data);
    return axios.post('/api/rooms', {
      name: data.name,
      capacity: data.capacity || null,
      description: data.description || null
    });
  },

  /**
   * Update room
   * @param {number|string} id - Room ID
   * @param {Object} data - Room data to update
   * @returns {Promise}
   */
  updateRoom(id, data) {
    console.log(`✏️ Updating room ID ${id} with data:`, data);
    return axios.put(`/api/rooms/${id}`, data);
  },

  /**
   * Delete room
   * @param {number|string} id - Room ID
   * @returns {Promise}
   */
  deleteRoom(id) {
    console.log(`🗑️ Deleting room with ID: ${id}`);
    return axios.delete(`/api/rooms/${id}`);
  },

  /**
   * Toggle room status (active/inactive)
   * @param {number|string} id - Room ID
   * @returns {Promise}
   */
  toggleStatus(id) {
    console.log(`🔄 Toggling status for room ID: ${id}`);
    return axios.patch(`/api/rooms/${id}/toggle-status`);
  }
};

export default roomService;
