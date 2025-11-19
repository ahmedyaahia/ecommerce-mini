<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <router-view v-if="!hasError" />
    <div v-else class="min-h-screen flex items-center justify-center">
      <div class="text-center">
        <h1 class="text-2xl font-bold text-red-600 mb-4">Something went wrong</h1>
        <p class="text-gray-600 mb-4">{{ errorMessage }}</p>
        <button 
          @click="reloadPage" 
          class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
        >
          Reload Page
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onErrorCaptured } from 'vue';

const hasError = ref(false);
const errorMessage = ref('');

onErrorCaptured((err, instance, info) => {
  console.error('Vue Error:', err, info);
  errorMessage.value = err.message || 'An unexpected error occurred';
  hasError.value = true;
  return false; // Prevent error from propagating
});

const reloadPage = () => {
  window.location.reload();
};
</script>

