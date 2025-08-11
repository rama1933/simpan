<template>
  <div class="min-h-screen bg-gradient-to-b from-white to-blue-50 text-gray-900">
    <!-- Mobile top bar -->
    <div class="md:hidden sticky top-0 z-50 bg-white/80 backdrop-blur border-b border-blue-100">
      <div class="flex items-center justify-between px-4 py-3">
        <button @click="sidebarOpen = true" class="p-2 rounded-md text-blue-600 hover:bg-blue-50">
          <i class="pi pi-bars text-lg"></i>
        </button>
        <div class="flex items-center gap-2">
          <span class="text-base font-semibold">{{ pageTitle }}</span>
        </div>
        <form :action="route('logout')" method="POST">
          <button class="px-3 py-1.5 rounded-md text-sm text-blue-700 bg-blue-50 border border-blue-100">Logout</button>
        </form>
      </div>
    </div>

    <!-- PrimeVue Sidebar for mobile -->
    <Sidebar v-model:visible="sidebarOpen" position="left" class="md:hidden">
      <template #header>
        <div class="text-lg font-semibold">KMS Admin</div>
      </template>
      <div class="space-y-1">
        <Link v-for="item in navItems" :key="item.href" :href="item.href"
              class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm"
              :class="item.active ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700'">
          <i class="pi pi-chevron-right text-sm"></i>
          <span class="font-medium">{{ item.label }}</span>
        </Link>
      </div>
    </Sidebar>

    <!-- Shell -->
    <div class="flex">
      <!-- Sidebar desktop -->
      <div class="hidden md:block fixed inset-y-0 left-0 z-40 w-72 border-r border-blue-100 bg-white/85 backdrop-blur">
        <aside class="h-full flex flex-col">
          <div class="h-16 bg-gradient-to-r from-blue-600 to-indigo-600 text-white flex items-center px-5">
            <div class="h-8 w-8 mr-3 rounded-lg bg-white/10 flex items-center justify-center">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v6c0 5-7 8-7 8s-7-3-7-8V7l7-4z"/></svg>
            </div>
            <div class="text-lg font-semibold tracking-wide">KMS Admin</div>
          </div>
          <nav class="flex-1 overflow-y-auto py-3">
            <div class="px-3 space-y-1">
              <Link v-for="item in navItems" :key="item.href"
                    :href="item.href"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors"
                    :class="item.active ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700'">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" :d="iconPaths[item.icon]" />
                </svg>
                <span class="font-medium">{{ item.label }}</span>
              </Link>
            </div>
          </nav>
          <div class="px-4 py-3 border-t border-blue-100 bg-white/70">
            <div class="flex items-center gap-2">
              <div class="h-9 w-9 rounded-full bg-gradient-to-br from-blue-500 to-indigo-500 text-white flex items-center justify-center text-sm font-semibold">
                {{ (user?.name || 'U').slice(0,1) }}
              </div>
              <div class="min-w-0">
                <div class="text-sm font-semibold truncate">{{ user?.name || 'User' }}</div>
                <div class="text-xs text-blue-600">Admin</div>
              </div>
            </div>
          </div>
        </aside>
      </div>

      <!-- Content -->
      <div class="flex-1 min-w-0 md:ml-72">
        <!-- Topbar desktop with PrimeVue Menubar -->
        <Menubar class="hidden md:flex sticky top-0 z-30 bg-white/90 backdrop-blur border-b border-blue-100"
                 :model="[]">
          <template #start>
            <span class="self-center whitespace-nowrap text-xl font-semibold">{{ pageTitle }}</span>
          </template>
          <template #end>
            <form :action="route('logout')" method="POST">
              <button class="inline-flex items-center text-sm text-blue-700 hover:text-blue-800 px-3 py-1.5 bg-blue-50 rounded-md border border-blue-100">Logout</button>
            </form>
          </template>
        </Menubar>

        <!-- Main -->
        <main class="p-4 md:p-6">
          <div class="mx-auto max-w-[1400px]">
            <slot />
          </div>
        </main>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { route } from '@/core/helpers/route'
import { ref, computed, onMounted, watch } from 'vue'
import { toast } from 'vue3-toastify'
import Menubar from 'primevue/menubar'
import Sidebar from 'primevue/sidebar'

const props = defineProps({
  pageTitle: { type: String, default: 'Dashboard' },
  user: { type: Object, default: null }
})

const page = usePage()
const sidebarOpen = ref(false)

const navItems = computed(() => {
  const url = page.url || ''
  return [
    { label: 'Dashboard', href: route('dashboard'), active: url === route('dashboard'), icon: 'dashboard' },
    { label: 'Knowledge', href: route('knowledge.index'), active: String(url).startsWith('/knowledge'), icon: 'doc' },
    { label: 'User Management', href: route('users.index'), active: String(url).startsWith('/users'), icon: 'users' },
    { label: 'AI Assistant', href: route('ai.index'), active: String(url).startsWith('/ai'), icon: 'ai' },
  ]
})

const iconPaths = {
  dashboard: 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z',
  doc: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
  users: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z',
  ai: 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'
}

function resolveFlashVal(val) { return typeof val === 'function' ? val() : val }

onMounted(() => {
  const flash = page.props?.flash || {}
  const success = resolveFlashVal(flash.success)
  const error = resolveFlashVal(flash.error)
  const message = resolveFlashVal(flash.message)
  if (success) toast.success(success)
  if (error) toast.error(error)
  if (message) toast.info(message)
})

watch(() => page.props?.flash, (flash) => {
  if (!flash) return
  const success = resolveFlashVal(flash.success)
  const error = resolveFlashVal(flash.error)
  const message = resolveFlashVal(flash.message)
  if (success) toast.success(success)
  if (error) toast.error(error)
  if (message) toast.info(message)
}, { deep: true })
</script>
