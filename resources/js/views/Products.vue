<template>
  <div>
    <div class="px-4 sm:px-6 lg:px-8">
      <div class="sm:flex sm:items-center mb-6">
        <div class="sm:flex-auto">
          <h1 class="text-2xl font-semibold text-gray-900">Products</h1>
          <p class="mt-2 text-sm text-gray-700">Manage your product catalog</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
          <button
            type="button"
            @click="openCreateModal"
            class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none"
          >
            Add Product
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
        <p class="mt-2 text-gray-600">Loading products...</p>
      </div>

      <!-- Error State -->
      <div v-if="error" class="bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ error }}
        <button 
          @click="error = ''" 
          class="ml-2 text-red-700 hover:text-red-900 font-bold"
        >
          ×
        </button>
      </div>

      <!-- Products Table -->
      <div v-if="!loading && !error" class="bg-white shadow overflow-hidden sm:rounded-md">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="product in products" :key="product.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ product.name }}</td>
              <td class="px-6 py-4 text-sm text-gray-500">{{ product.description || 'N/A' }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ parseFloat(product.price).toFixed(2) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ product.stock }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="product.out_of_stock ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'"
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                >
                  {{ product.out_of_stock ? 'Out of Stock' : 'In Stock' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button
                  type="button"
                  @click="openEditModal(product)"
                  class="text-indigo-600 hover:text-indigo-900 mr-4"
                >
                  Edit
                </button>
                <button
                  type="button"
                  @click="confirmDelete(product)"
                  class="text-red-600 hover:text-red-900"
                >
                  Delete
                </button>
              </td>
            </tr>
            <tr v-if="products.length === 0">
              <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No products found</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Create/Edit Modal -->
      <div v-if="showModal" class="fixed z-50 inset-0 overflow-y-auto" @click.self="closeModal">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click.self="closeModal"></div>
          <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full z-50">
            <form @submit.prevent="saveProduct" @keydown.enter.prevent>
              <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                  {{ editingProduct ? 'Edit Product' : 'Create Product' }}
                </h3>
                <div v-if="error" class="mb-4 bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded">
                  {{ error }}
                </div>
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input
                      v-model="form.name"
                      type="text"
                      required
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea
                      v-model="form.description"
                      rows="3"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    ></textarea>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Price</label>
                    <input
                      v-model="form.price"
                      type="number"
                      step="0.01"
                      min="0"
                      required
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Stock</label>
                    <input
                      v-model="form.stock"
                      type="number"
                      min="0"
                      required
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>
                </div>
              </div>
              <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button
                  type="submit"
                  :disabled="saving"
                  class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                >
                  {{ saving ? 'Saving...' : 'Save' }}
                </button>
                <button
                  type="button"
                  @click="closeModal"
                  class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                >
                  Cancel
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';

const products = ref([]);
const loading = ref(true);
const error = ref('');
const showModal = ref(false);
const editingProduct = ref(null);
const saving = ref(false);

const form = ref({
  name: '',
  description: '',
  price: '',
  stock: ''
});

const fetchProducts = async () => {
  loading.value = true;
  error.value = '';

  try {
    const response = await api.get('/products');
    if (response && response.data && response.data.success) {
      products.value = response.data.data || [];
    } else {
      error.value = 'Failed to load products';
      products.value = [];
    }
  } catch (err) {
    console.error('Products error:', err);
    error.value = 'Failed to load products';
    products.value = [];
    // Don't crash the app if fetch fails
  } finally {
    loading.value = false;
  }
};

const openCreateModal = () => {
  console.log('Opening create modal');
  editingProduct.value = null;
  form.value = {
    name: '',
    description: '',
    price: '',
    stock: ''
  };
  error.value = ''; // Clear any previous errors
  showModal.value = true;
  console.log('Modal state:', showModal.value);
};

const openEditModal = (product) => {
  console.log('Opening edit modal for product:', product);
  editingProduct.value = product;
  form.value = {
    name: product.name,
    description: product.description || '',
    price: product.price,
    stock: product.stock
  };
  error.value = ''; // Clear any previous errors
  showModal.value = true;
  console.log('Modal state:', showModal.value);
};

const closeModal = () => {
  showModal.value = false;
  editingProduct.value = null;
};

const saveProduct = async () => {
  saving.value = true;
  error.value = '';

  try {
    // Validate form data
    if (!form.value.name || !form.value.price || form.value.stock === '') {
      error.value = 'Please fill in all required fields';
      saving.value = false;
      return;
    }

    let response;
    if (editingProduct.value) {
      // Update
      response = await api.put(`/products/${editingProduct.value.id}`, {
        name: form.value.name,
        description: form.value.description || null,
        price: parseFloat(form.value.price),
        stock: parseInt(form.value.stock)
      });
    } else {
      // Create
      response = await api.post('/products', {
        name: form.value.name,
        description: form.value.description || null,
        price: parseFloat(form.value.price),
        stock: parseInt(form.value.stock)
      });
    }
    
    if (response && response.data && response.data.success) {
      // Clear any previous errors
      error.value = '';
      // Close modal first
      closeModal();
      // Refresh products list
      try {
        await fetchProducts();
        console.log('Products refreshed successfully');
      } catch (fetchErr) {
        console.error('Error fetching products after save:', fetchErr);
        // Show error but don't prevent modal from closing
        error.value = 'Product saved but failed to refresh list. Please refresh the page.';
      }
    } else {
      // Don't close modal on error - let user see the error
      error.value = response?.data?.message || 'Failed to save product';
      if (response?.data?.errors) {
        const errorMessages = Object.values(response.data.errors).flat().join(', ');
        error.value = errorMessages;
      }
    }
  } catch (err) {
    console.error('Save error:', err);
    // Prevent the error from crashing the app
    if (err.response) {
      // Handle 401 separately to avoid redirect loop
      if (err.response.status === 401) {
        error.value = 'Your session has expired. Please log in again.';
        // Don't let the interceptor redirect - handle it here
        setTimeout(() => {
          window.location.href = '/login';
        }, 2000);
        return;
      }
      
      if (err.response.data && err.response.data.errors) {
        const errorMessages = Object.values(err.response.data.errors).flat().join(', ');
        error.value = errorMessages;
      } else {
        error.value = err.response.data?.message || 'Failed to save product';
      }
    } else if (err.request) {
      error.value = 'Network error. Please check your connection and try again.';
    } else {
      error.value = 'An unexpected error occurred. Please try again.';
    }
  } finally {
    saving.value = false;
  }
};

const confirmDelete = async (product) => {
  if (confirm(`Are you sure you want to delete "${product.name}"?`)) {
    try {
      const response = await api.delete(`/products/${product.id}`);
      if (response.data.success) {
        await fetchProducts();
      }
    } catch (err) {
      error.value = 'Failed to delete product';
      console.error('Delete error:', err);
    }
  }
};

onMounted(() => {
  fetchProducts();
});
</script>

