<template>
  <AdminLayout page-title="Tambah User" :user="user">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
      <h1 class="text-2xl font-semibold text-gray-900 mb-4">Tambah User</h1>
      <form @submit.prevent="submit">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
            <input v-model="form.name" type="text" class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-500" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input v-model="form.email" type="email" class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-500" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">SKPD</label>
            <select v-model.number="form.skpd_id" class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-500" required>
              <option :value="null" disabled>Pilih SKPD</option>
              <option v-for="s in skpds" :key="s.id" :value="s.id">{{ s.nama_skpd }}</option>
            </select>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
              <input v-model="form.password" type="password" class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-500" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
              <input v-model="form.password_confirmation" type="password" class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-500" required />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Roles</label>
            <div class="flex flex-wrap gap-2">
              <label v-for="r in roles" :key="r.id" class="inline-flex items-center gap-2 text-sm px-3 py-1 rounded-full border border-brand-100 bg-brand-50 text-brand-800">
                <input type="checkbox" :value="r.name" v-model="form.roles" class="rounded border-gray-300 text-brand-700 focus:ring-brand-500" />
                {{ r.name }}
              </label>
            </div>
          </div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
          <Link :href="route('users.index')" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Batal</Link>
          <button type="submit" class="px-4 py-2 rounded-md text-sm font-semibold text-white bg-brand-700 hover:bg-brand-800">Simpan</button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { reactive } from 'vue'
import { route } from '@/core/helpers/route'

const props = defineProps({ user: Object, roles: Array, skpds: Array })

const form = reactive({
  name: '',
  email: '',
  skpd_id: null,
  password: '',
  password_confirmation: '',
  roles: []
})

function submit() {
  router.post(route('users.store'), form, {
    onSuccess: () => {},
  })
}
</script>

