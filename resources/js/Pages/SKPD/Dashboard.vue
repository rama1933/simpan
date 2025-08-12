<template>
  <SKPDLayout :page-title="'Dashboard SKPD'" :user="$page.props.auth.user">
    <div class="space-y-6">
      <!-- Welcome Section -->
      <div class="bg-white rounded-xl shadow-sm border border-blue-100 p-6">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Selamat Datang, {{ $page.props.auth.user?.name }}</h1>
            <p class="text-gray-600 mt-1">{{ $page.props.auth.user?.skpd?.name || 'SKPD' }}</p>
          </div>
          <div class="h-16 w-16 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center text-xl font-bold">
            {{ ($page.props.auth.user?.name || 'U').slice(0,1) }}
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-blue-100 p-6">
          <div class="flex items-center">
            <div class="h-12 w-12 rounded-lg bg-blue-100 flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Total Knowledge</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats?.total_knowledge || 0 }}</p>
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
      <div class="bg-white rounded-xl shadow-sm border border-blue-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <Link :href="route('knowledge.create')" class="flex items-center gap-3 p-4 rounded-lg border border-blue-200 hover:bg-blue-50 transition-colors">
            <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
              </svg>
            </div>
            <div>
              <p class="font-medium text-gray-900">Tambah Knowledge</p>
              <p class="text-sm text-gray-600">Buat knowledge baru</p>
            </div>
          </Link>

          <Link :href="route('knowledge.index')" class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
            <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
            <div>
              <p class="font-medium text-gray-900">Lihat Knowledge</p>
              <p class="text-sm text-gray-600">Kelola knowledge SKPD</p>
            </div>
          </Link>

          <Link :href="route('ai.index')" class="flex items-center gap-3 p-4 rounded-lg border border-purple-200 hover:bg-purple-50 transition-colors">
            <div class="h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-purple-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
              </svg>
            </div>
            <div>
              <p class="font-medium text-gray-900">AI Assistant</p>
              <p class="text-sm text-gray-600">Bantuan AI untuk knowledge</p>
            </div>
          </Link>
        </div>
      </div>

      <!-- Recent Knowledge -->
      <div class="bg-white rounded-xl shadow-sm border border-blue-100 p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold text-gray-900">Knowledge Terbaru</h2>
          <Link :href="route('knowledge.index')" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Lihat Semua</Link>
        </div>
        <div v-if="recentKnowledge?.length" class="space-y-3">
          <div v-for="knowledge in recentKnowledge" :key="knowledge.id" class="flex items-center justify-between p-3 rounded-lg border border-gray-100 hover:bg-gray-50">
            <div class="flex-1">
              <Link :href="route('knowledge.show', knowledge.id)" class="font-medium text-gray-900 hover:text-blue-600">{{ knowledge.title }}</Link>
              <p class="text-sm text-gray-600 mt-1">{{ knowledge.category?.name }} • {{ formatDate(knowledge.created_at) }}</p>
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
          <p>Belum ada knowledge yang dibuat</p>
        </div>
      </div>
    </div>
  </SKPDLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { route } from '@/core/helpers/route'
import SKPDLayout from '@/Layouts/SKPDLayout.vue'

defineProps({
  stats: { type: Object, default: () => ({}) },
  recentKnowledge: { type: Array, default: () => [] }
})

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script>