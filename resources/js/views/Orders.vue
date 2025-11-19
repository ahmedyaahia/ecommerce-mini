<template>
  <div>
    <div class="px-4 sm:px-6 lg:px-8">
      <div class="sm:flex sm:items-center mb-6">
        <div class="sm:flex-auto">
          <h1 class="text-2xl font-semibold text-gray-900">Orders</h1>
          <p class="mt-2 text-sm text-gray-700">View and manage orders</p>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
        <p class="mt-2 text-gray-600">Loading orders...</p>
      </div>

      <!-- Error State -->
      <div v-if="error" class="bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ error }}
      </div>

      <!-- Orders Table -->
      <div v-if="!loading && !error" class="bg-white shadow overflow-hidden sm:rounded-md">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Number</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="order in orders" :key="order.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ order.order_number }}</td>
              <td class="px-6 py-4 text-sm text-gray-500">
                <div>{{ order.user?.name || 'N/A' }}</div>
                <div class="text-xs text-gray-400">{{ order.phone }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ parseFloat(order.total).toFixed(2) }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800"
                >
                  {{ order.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ new Date(order.created_at).toLocaleDateString() }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button
                  @click="openDetailsModal(order)"
                  class="text-indigo-600 hover:text-indigo-900"
                >
                  View Details
                </button>
              </td>
            </tr>
            <tr v-if="orders.length === 0">
              <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No orders found</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Order Details Modal -->
      <div v-if="showDetailsModal" class="fixed z-10 inset-0 overflow-y-auto" @click.self="closeDetailsModal">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
          <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div v-if="selectedOrder" class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Order Details</h3>
              
              <div class="space-y-4">
                <div>
                  <h4 class="text-sm font-medium text-gray-700">Order Information</h4>
                  <dl class="mt-2 grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                    <div>
                      <dt class="text-sm text-gray-500">Order Number</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ selectedOrder.order_number }}</dd>
                    </div>
                    <div>
                      <dt class="text-sm text-gray-500">Status</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ selectedOrder.status }}</dd>
                    </div>
                    <div>
                      <dt class="text-sm text-gray-500">Total</dt>
                      <dd class="mt-1 text-sm font-medium text-gray-900">${{ parseFloat(selectedOrder.total).toFixed(2) }}</dd>
                    </div>
                    <div>
                      <dt class="text-sm text-gray-500">Date</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ new Date(selectedOrder.created_at).toLocaleString() }}</dd>
                    </div>
                  </dl>
                </div>

                <div>
                  <h4 class="text-sm font-medium text-gray-700">Customer Information</h4>
                  <dl class="mt-2 grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                    <div>
                      <dt class="text-sm text-gray-500">Name</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ selectedOrder.user?.name || 'N/A' }}</dd>
                    </div>
                    <div>
                      <dt class="text-sm text-gray-500">Phone</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ selectedOrder.phone }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                      <dt class="text-sm text-gray-500">Address</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ selectedOrder.address }}</dd>
                    </div>
                  </dl>
                </div>

                <div>
                  <h4 class="text-sm font-medium text-gray-700 mb-2">Order Items</h4>
                  <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-md">
                    <table class="min-w-full divide-y divide-gray-300">
                      <thead class="bg-gray-50">
                        <tr>
                          <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Product</th>
                          <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Quantity</th>
                          <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Price</th>
                          <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Subtotal</th>
                        </tr>
                      </thead>
                      <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="item in selectedOrder.order_items" :key="item.id">
                          <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                            {{ item.product?.name || 'N/A' }}
                          </td>
                          <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ item.quantity }}</td>
                          <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">${{ parseFloat(item.price).toFixed(2) }}</td>
                          <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">${{ (parseFloat(item.price) * item.quantity).toFixed(2) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                type="button"
                @click="closeDetailsModal"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';

const orders = ref([]);
const loading = ref(true);
const error = ref('');
const showDetailsModal = ref(false);
const selectedOrder = ref(null);

const fetchOrders = async () => {
  loading.value = true;
  error.value = '';

  try {
    const response = await api.get('/orders');
    if (response.data.success) {
      orders.value = response.data.data;
    }
  } catch (err) {
    error.value = 'Failed to load orders';
    console.error('Orders error:', err);
  } finally {
    loading.value = false;
  }
};

const openDetailsModal = (order) => {
  selectedOrder.value = order;
  showDetailsModal.value = true;
};

const closeDetailsModal = () => {
  showDetailsModal.value = false;
  selectedOrder.value = null;
};

onMounted(() => {
  fetchOrders();
});
</script>

