<template>
  <div class="min-h-screen bg-gradient-to-b from-white to-blue-50 text-gray-900">
    <!-- Mobile top bar -->
    <div class="md:hidden sticky top-0 z-50 bg-white/80 backdrop-blur border-b border-blue-100">
      <div class="flex items-center justify-between px-4 py-3">
        <button @click="sidebarOpen = true" class="p-2 rounded-md text-blue-700 hover:bg-blue-50">
          <i class="pi pi-bars text-lg"></i>
        </button>
        <div class="flex items-center gap-2">
          <span class="text-base font-semibold">{{ pageTitle }}</span>
        </div>
        <Link :href="route('logout')" method="post" as="button" class="px-3 py-1.5 rounded-md text-sm text-white bg-blue-700 hover:bg-blue-800">Logout</Link>
      </div>
    </div>

    <!-- Sidebar mobile (PrimeVue) -->
    <Sidebar v-model:visible="sidebarOpen" position="left" class="md:hidden">
      <template #header>
        <div class="text-lg font-semibold">SIMPAN</div>
      </template>
      <div class="space-y-1">
        <Link v-for="item in navItems" :key="item.href" :href="item.href"
              class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm"
              :class="item.active ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700'">
          <i class="pi pi-angle-right text-sm"></i>
          <span class="font-medium">{{ item.label }}</span>
        </Link>
      </div>
    </Sidebar>

    <div class="flex">
      <!-- Sidebar desktop -->
      <div class="hidden md:block fixed inset-y-0 left-0 z-40 w-72 border-r border-blue-100 bg-white/85 backdrop-blur">
        <aside class="h-full flex flex-col">
          <div class="h-16 bg-gradient-to-r from-blue-700 to-blue-600 text-white flex items-center px-5">
            <div class="h-8 w-8 mr-3 rounded-lg bg-white/10 flex items-center justify-center">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div class="text-lg font-semibold tracking-wide">SIMPAN</div>
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
          <div class="px-4 py-3 border-t border-blue-100 bg-white/70 relative" ref="userMenuWrapper">
            <button @click="userMenuOpen = !userMenuOpen" ref="userMenuButton" class="w-full flex items-center gap-2 rounded-md hover:bg-blue-50 px-2 py-1.5">
              <div class="h-9 w-9 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center text-sm font-semibold">
                {{ (user?.name || 'U').slice(0,1) }}
              </div>
              <div class="min-w-0 text-left flex-1">
                <div class="text-sm font-semibold truncate">{{ user?.name || 'User' }}</div>
                <div class="text-xs text-blue-700">{{ user?.skpd?.name || 'SKPD' }}</div>
              </div>
              <svg class="w-4 h-4 text-gray-500" viewBox="0 0 20 20" fill="currentColor"><path d="M5.23 7.21a.75.75 0 011.06.02L10 11.14l3.71-3.91a.75.75 0 111.08 1.04l-4.24 4.46a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z"/></svg>
            </button>
            <div v-if="userMenuOpen" ref="userMenu" class="absolute right-3 bottom-14 z-50 w-48 rounded-lg bg-white shadow-lg border border-blue-100 overflow-hidden">
              <Link href="/profile" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-blue-50">
                <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>Profil</span>
              </Link>
              <Link :href="route('logout')" method="post" as="button" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-rose-700 hover:bg-rose-50">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12H3m12 0l-4-4m4 4l-4 4m6-9V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                <span>Logout</span>
              </Link>
            </div>
          </div>
        </aside>
      </div>

      <!-- Content -->
      <div class="flex-1 min-w-0 md:ml-72">
        <!-- Desktop topbar -->
        <div class="hidden md:flex items-center justify-between sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-blue-100 px-6 h-16">
          <span class="text-xl font-semibold">{{ pageTitle }}</span>
        </div>

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
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import { toast } from 'vue3-toastify'
import Sidebar from 'primevue/sidebar'

const props = defineProps({
  pageTitle: { type: String, default: 'Dashboard SKPD' },
  user: { type: Object, default: null }
})

const page = usePage()
const sidebarOpen = ref(false)
const userMenuOpen = ref(false)
const userMenuWrapper = ref(null)
const userMenu = ref(null)
const userMenuButton = ref(null)

function handleClickOutside(e) {
  const w = userMenuWrapper.value
  if (!w) return
  if (!w.contains(e.target)) userMenuOpen.value = false
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})

const navItems = computed(() => {
  const url = page.url || ''
  
  return [
    { label: 'Dashboard', href: route('dashboard.skpd'), active: url === route('dashboard.skpd'), icon: 'dashboard' },
    { label: 'Knowledge SKPD', href: route('skpd.knowledge.index'), active: String(url).startsWith('/skpd/knowledge'), icon: 'doc' },
    { label: 'AI Assistant', href: route('ai.index'), active: String(url).startsWith('/ai'), icon: 'ai' },
  ]
})

const iconPaths = {
  dashboard: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
  doc: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
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