<template>
  <AdminLayout :page-title="'Dashboard Admin'" :user="$page.props.auth.user">
    <div class="space-y-6">
      <!-- Welcome Section -->
      <div class="bg-white rounded-xl shadow-sm border border-brand-100 p-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard Admin</h1>
            <p class="text-gray-600 mt-1">Kelola sistem knowledge management</p>
          </div>
          <div class="h-16 w-16 rounded-full bg-gradient-to-br from-brand-500 to-brand-700 text-white flex items-center justify-center text-xl font-bold">
            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 4v6c0 5-7 8-7 8s-7-3-7-8V7l7-4z"/>
            </svg>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-brand-100 p-6">
          <div class="flex items-center">
            <div class="h-12 w-12 rounded-lg bg-brand-100 flex items-center justify-center">
              <svg class="w-6 h-6 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Total Knowledge</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats?.total_knowledge || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-blue-100 p-6">
          <div class="flex items-center">
            <div class="h-12 w-12 rounded-lg bg-blue-100 flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Total Users</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats?.total_users || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-green-100 p-6">
          <div class="flex items-center">
            <div class="h-12 w-12 rounded-lg bg-green-100 flex items-center justify-center">
              <svg class="w-6 h-6 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Terverifikasi</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats?.verified_knowledge || 0 }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-yellow-100 p-6">
          <div class="flex items-center">
            <div class="h-12 w-12 rounded-lg bg-yellow-100 flex items-center justify-center">
              <svg class="w-6 h-6 text-yellow-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Menunggu Verifikasi</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats?.pending_knowledge || 0 }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white rounded-xl shadow-sm border border-brand-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <Link :href="route('knowledge.create')" class="flex items-center gap-3 p-4 rounded-lg border border-brand-200 hover:bg-brand-50 transition-colors">
            <div class="h-10 w-10 rounded-lg bg-brand-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
              </svg>
            </div>
            <div>
              <p class="font-medium text-gray-900">Tambah Knowledge</p>
              <p class="text-sm text-gray-600">Buat knowledge baru</p>
            </div>
          </Link>

          <Link :href="route('users.create')" class="flex items-center gap-3 p-4 rounded-lg border border-blue-200 hover:bg-blue-50 transition-colors">
            <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
            </div>
            <div>
              <p class="font-medium text-gray-900">Tambah User</p>
              <p class="text-sm text-gray-600">Buat user baru</p>
            </div>
          </Link>

          <Link :href="route('knowledge.index')" class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
            <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
            <div>
              <p class="font-medium text-gray-900">Kelola Knowledge</p>
              <p class="text-sm text-gray-600">Verifikasi & kelola</p>
            </div>
          </Link>

          <Link :href="route('users.index')" class="flex items-center gap-3 p-4 rounded-lg border border-purple-200 hover:bg-purple-50 transition-colors">
            <div class="h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-purple-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
              </svg>
            </div>
            <div>
              <p class="font-medium text-gray-900">Kelola Users</p>
              <p class="text-sm text-gray-600">Manajemen pengguna</p>
            </div>
          </Link>
        </div>
      </div>

      <!-- Recent Activities -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Knowledge -->
        <div class="bg-white rounded-xl shadow-sm border border-brand-100 p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Knowledge Terbaru</h2>
            <Link :href="route('knowledge.index')" class="text-brand-600 hover:text-brand-700 text-sm font-medium">Lihat Semua</Link>
          </div>
          <div v-if="recentKnowledge?.length" class="space-y-3">
            <div v-for="knowledge in recentKnowledge" :key="knowledge.id" class="flex items-center justify-between p-3 rounded-lg border border-gray-100 hover:bg-gray-50">
              <div class="flex-1">
                <Link :href="route('knowledge.show', knowledge.id)" class="font-medium text-gray-900 hover:text-brand-600">{{ knowledge.title }}</Link>
                <p class="text-sm text-gray-600 mt-1">{{ knowledge.user?.name }} • {{ formatDate(knowledge.created_at) }}</p>
              </div>
              <div class="flex items-center gap-2">
                <span v-if="knowledge.is_verified" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                  Terverifikasi
                </span>
                <span v-else class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                  Menunggu
                </span>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-8 text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p>Belum ada knowledge</p>
          </div>
        </div>

        <!-- Recent Users -->
        <div class="bg-white rounded-xl shadow-sm border border-brand-100 p-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">User Terbaru</h2>
            <Link :href="route('users.index')" class="text-brand-600 hover:text-brand-700 text-sm font-medium">Lihat Semua</Link>
          </div>
          <div v-if="recentUsers?.length" class="space-y-3">
            <div v-for="user in recentUsers" :key="user.id" class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:bg-gray-50">
              <div class="h-10 w-10 rounded-full bg-gradient-to-br from-brand-500 to-brand-700 text-white flex items-center justify-center text-sm font-semibold">
                {{ (user.name || 'U').slice(0,1) }}
              </div>
              <div class="flex-1">
                <p class="font-medium text-gray-900">{{ user.name }}</p>
                <p class="text-sm text-gray-600">{{ user.skpd?.name || 'No SKPD' }} • {{ formatDate(user.created_at) }}</p>
              </div>
              <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                {{ (user.roles || []).join(', ') || 'User' }}
              </span>
            </div>
          </div>
          <div v-else class="text-center py-8 text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
            </svg>
            <p>Belum ada user</p>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { route } from '@/core/helpers/route'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
  stats: { type: Object, default: () => ({}) },
  recentKnowledge: { type: Array, default: () => [] },
  recentUsers: { type: Array, default: () => [] }
})

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script>