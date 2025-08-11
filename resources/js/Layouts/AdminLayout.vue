<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg">
      <div class="flex items-center justify-center h-16 bg-blue-600">
        <h1 class="text-xl font-bold text-white">Admin Panel</h1>
      </div>

      <nav class="mt-8">
        <div class="px-4 space-y-2">
          <Link
            :href="route('dashboard')"
            class="flex items-center px-4 py-2 text-gray-700 rounded-md hover:bg-blue-50 hover:text-blue-700"
            :class="{ 'bg-blue-100 text-blue-700': page.url === route('dashboard') }"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
            </svg>
            Dashboard
          </Link>

          <Link
            :href="route('knowledge.index')"
            class="flex items-center px-4 py-2 text-gray-700 rounded-md hover:bg-blue-50 hover:text-blue-700"
            :class="{ 'bg-blue-100 text-blue-700': page.url.startsWith('/knowledge') }"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Knowledge
          </Link>

          <Link
            :href="route('users.index')"
            class="flex items-center px-4 py-2 text-gray-700 rounded-md hover:bg-blue-50 hover:text-blue-700"
            :class="{ 'bg-blue-100 text-blue-700': page.url.startsWith('/users') }"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
            User Management
          </Link>

          <Link
            :href="route('ai.index')"
            class="flex items-center px-4 py-2 text-gray-700 rounded-md hover:bg-blue-50 hover:text-blue-700"
            :class="{ 'bg-blue-100 text-blue-700': page.url.startsWith('/ai') }"
          >
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
            AI Assistant
          </Link>
        </div>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="ml-64">
      <!-- Top Navigation -->
      <header class="bg-white shadow-sm border-b">
        <div class="flex items-center justify-between px-6 py-4">
          <div class="flex items-center space-x-4">
            <h2 class="text-xl font-semibold text-gray-900">{{ pageTitle }}</h2>
          </div>

          <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-600">{{ user?.name }}</span>
            <form :action="route('logout')" method="POST" class="inline">
              <button
                type="submit"
                class="text-sm text-red-600 hover:text-red-800"
              >
                Logout
              </button>
            </form>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { route } from '@/core/helpers/route';
import { Link, usePage } from '@inertiajs/vue3';
import { onMounted, watch } from 'vue';
import { toast } from 'vue3-toastify';

defineProps({
  pageTitle: {
    type: String,
    default: 'Dashboard'
  },
  user: {
    type: Object,
    default: null
  }
});

const page = usePage();

function resolveFlashVal(val) {
  return typeof val === 'function' ? val() : val;
}

onMounted(() => {
  const flash = page.props?.flash || {};
  const success = resolveFlashVal(flash.success);
  const error = resolveFlashVal(flash.error);
  const message = resolveFlashVal(flash.message);
  if (success) toast.success(success);
  if (error) toast.error(error);
  if (message) toast.info(message);
});

watch(
  () => page.props?.flash,
  (flash) => {
    if (!flash) return;
    const success = resolveFlashVal(flash.success);
    const error = resolveFlashVal(flash.error);
    const message = resolveFlashVal(flash.message);
    if (success) toast.success(success);
    if (error) toast.error(error);
    if (message) toast.info(message);
  },
  { deep: true }
);
</script>
