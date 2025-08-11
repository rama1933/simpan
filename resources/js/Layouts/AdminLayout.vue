<template>
  <div data-theme="kmsblue" class="min-h-screen bg-base-100 text-base-content">
    <!-- Mobile navbar -->
    <div class="md:hidden navbar bg-base-100 border-b border-blue-100 sticky top-0 z-50">
      <div class="flex-none">
        <button class="btn btn-ghost btn-square" @click="sidebarOpen = true">
          <i class="pi pi-bars"></i>
        </button>
      </div>
      <div class="flex-1">
        <a class="btn btn-ghost normal-case text-lg font-semibold">{{ pageTitle }}</a>
      </div>
      <div class="flex-none">
        <form :action="route('logout')" method="POST">
          <button class="btn btn-sm btn-primary">Logout</button>
        </form>
      </div>
    </div>

    <!-- Drawer for mobile -->
    <Sidebar v-model:visible="sidebarOpen" position="left" class="md:hidden">
      <template #header>
        <div class="text-lg font-semibold">KMS Admin</div>
      </template>
      <ul class="menu w-64">
        <li v-for="item in navItems" :key="item.href">
          <Link :href="item.href" :class="item.active ? 'active' : ''">{{ item.label }}</Link>
        </li>
      </ul>
    </Sidebar>

    <div class="flex">
      <!-- Sidebar desktop -->
      <aside class="hidden md:flex w-72 border-r bg-base-100/90 backdrop-blur min-h-screen flex-col">
        <div class="h-16 bg-gradient-to-r from-primary to-secondary text-primary-content flex items-center px-5">
          <div class="avatar placeholder mr-3">
            <div class="bg-primary/20 text-primary-content rounded-lg w-8">
              <span class="text-sm">K</span>
            </div>
          </div>
          <span class="font-semibold">KMS Admin</span>
        </div>
        <ul class="menu px-3 py-2">
          <li v-for="item in navItems" :key="item.href">
            <Link :href="item.href" :class="item.active ? 'active' : ''">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" :d="iconPaths[item.icon]" />
              </svg>
              {{ item.label }}
            </Link>
          </li>
        </ul>
        <div class="mt-auto p-4 border-t">
          <div class="flex items-center gap-3">
            <div class="avatar placeholder">
              <div class="bg-primary text-primary-content rounded-full w-9">
                <span>{{ (user?.name || 'U').slice(0,1) }}</span>
              </div>
            </div>
            <div>
              <div class="text-sm font-semibold">{{ user?.name || 'User' }}</div>
              <div class="text-xs text-primary">Admin</div>
            </div>
          </div>
        </div>
      </aside>

      <div class="flex-1 md:ml-72">
        <!-- Desktop navbar -->
        <div class="hidden md:flex navbar bg-base-100 border-b sticky top-0 z-40">
          <div class="flex-1">
            <a class="btn btn-ghost normal-case text-xl font-semibold">{{ pageTitle }}</a>
          </div>
          <div class="flex-none">
            <form :action="route('logout')" method="POST">
              <button class="btn btn-primary btn-sm">Logout</button>
            </form>
          </div>
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
