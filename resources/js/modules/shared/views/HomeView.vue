<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <div class="text-center">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
          Sistem Manajemen Pengetahuan
        </h1>
        <p class="text-xl text-gray-600 mb-8">
          Platform modern untuk mengelola dan berbagi pengetahuan dengan integrasi AI
        </p>
        <div class="space-x-4">
          <router-link 
            to="/login" 
            class="btn btn-primary"
          >
            Masuk
          </router-link>
          <router-link 
            to="/dashboard" 
            class="btn btn-secondary"
          >
            Dashboard
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Home view component
</script>
