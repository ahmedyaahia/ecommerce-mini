import axios from 'axios';

const api = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Add token from localStorage if available
const token = localStorage.getItem('token');
if (token) {
  api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

// Response interceptor for handling errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    // Only handle 401 errors here, let components handle other errors
    if (error.response?.status === 401) {
      // Unauthorized - clear token and redirect to login
      // Only redirect if we're not already on the login page and not in a modal
      const currentPath = window.location.pathname;
      if (!currentPath.includes('/login') && !currentPath.includes('/register')) {
        localStorage.removeItem('token');
        delete api.defaults.headers.common['Authorization'];
        // Use a small delay to allow error messages to show
        setTimeout(() => {
          window.location.href = '/login';
        }, 100);
      }
    }
    // Don't redirect for validation errors (422) or other client errors
    return Promise.reject(error);
  }
);

export default api;

