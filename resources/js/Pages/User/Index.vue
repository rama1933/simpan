<template>
  <AdminLayout page-title="User Management" :user="user">
    <div class="mb-8 flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
          <p class="mt-2 text-gray-600">Kelola user dan role dalam sistem</p>
        </div>
        <a
          :href="route('users.create')"
          class="bg-brand-700 text-white px-4 py-2 rounded-md hover:bg-brand-800"
        >
          Tambah User
        </a>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Role</label>
            <select
              v-model="filters.role"
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-500"
            >
              <option value="">Semua Role</option>
              <option value="Admin">Admin</option>
              <option value="User SKPD">User SKPD</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input
              v-model="filters.search"
              type="text"
              placeholder="Cari nama atau email..."
              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-500"
            />
          </div>
          <div class="flex items-end">
            <button
              @click="applyFilters"
              class="w-full bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-700"
            >
              Filter
            </button>
          </div>
        </div>
      </div>

      <!-- Users Table -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                User
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Role
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="user in filteredUsers" :key="user.id">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10">
                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                      <span class="text-sm font-medium text-gray-700">
                        {{ user.name.charAt(0).toUpperCase() }}
                      </span>
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                    <div class="text-sm text-gray-500">{{ user.email }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  v-for="role in user.roles"
                  :key="role.id"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="role.name === 'Admin' ? 'bg-red-100 text-red-800' : 'bg-brand-100 text-brand-800'"
                >
                  {{ role.name }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                  Active
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <a
                  :href="route('users.edit', user.id)"
                  class="text-brand-700 hover:text-brand-900 mr-4"
                >
                  Edit
                </a>
                <button
                  @click="deleteUser(user.id)"
                  class="text-red-600 hover:text-red-900"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { route } from '@/core/helpers/route'
import AdminLayout from '@/Layouts/AdminLayout.vue'

interface User {
  id: number
  name: string
  email: string
  roles: Array<{ id: number; name: string }>
}

interface Props {
  user?: any
}

const props = defineProps<Props>()
const users = ref<User[]>([])
const filters = ref({
  role: '',
  search: ''
})

const filteredUsers = computed(() => {
  let filtered = users.value

  if (filters.value.role) {
    filtered = filtered.filter(user =>
      user.roles.some(role => role.name === filters.value.role)
    )
  }

  if (filters.value.search) {
    const search = filters.value.search.toLowerCase()
    filtered = filtered.filter(user =>
      user.name.toLowerCase().includes(search) ||
      user.email.toLowerCase().includes(search)
    )
  }

  return filtered
})

const fetchUsers = async () => {
  try {
    const response = await axios.get(route('users.list'))
    users.value = response.data.data || []
  } catch (error) {
    console.error('Error fetching users:', error)
  }
}

const applyFilters = () => {
  // Filters are applied automatically via computed property
}

const deleteUser = async (userId: number) => {
  if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
    try {
      await axios.delete(`/users/${userId}`)
      await fetchUsers()
    } catch (error) {
      alert('Error menghapus user')
    }
  }
}

onMounted(() => {
  fetchUsers()
})
</script>
