<template>
  <AdminLayout page-title="Edit User" :user="user">
    <div class="min-h-screen bg-gray-50">
      <div class="w-full mx-auto py-6 px-6">
        <div class="px-0">
          <!-- Flash Success -->
          <div v-if="$page.props.flash?.success" class="mb-4">
            <div class="rounded-md bg-green-50 p-4 border border-green-200">
              <p class="text-sm font-medium text-green-800">{{ $page.props.flash.success }}</p>
            </div>
          </div>

          <!-- Header -->
          <div class="mb-6 flex items-center justify-between">
            <Link href="/users" class="text-brand-700 hover:text-brand-800 mr-4">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
              </svg>
            </Link>
            <div class="flex-1">
              <h1 class="text-3xl font-bold text-gray-900">Edit User</h1>
              <p class="text-gray-600 mt-2">Perbarui data user dalam sistem manajemen pengetahuan</p>
            </div>
            <div class="hidden md:flex items-center gap-3">
              <span class="inline-flex items-center rounded-full bg-brand-50 px-3 py-1 text-sm font-medium text-brand-700 border border-brand-100">Form</span>
            </div>
          </div>

          <!-- Form Full Width -->
          <Form ref="formRef" :validation-schema="schema" :initial-values="initialValues" @submit="onSubmit" v-slot="{ meta, values }" class="space-y-6">
            <div class="grid grid-cols-1 gap-6">
              <!-- Konten Utama -->
              <div class="space-y-6">
                <div class="bg-white shadow rounded-lg p-6">
                  <div class="space-y-6">
                    <!-- Name -->
                    <div>
                      <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama <span class="text-red-500">*</span></label>
                      <Field name="name" v-slot="{ field, meta }">
                        <div class="relative">
                          <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                          </div>
                          <input v-bind="field" id="name" type="text" class="w-full pl-10 pr-24 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-500 border transition-colors" :class="meta.touched && !meta.valid ? 'border-red-500' : 'border-gray-300 hover:border-gray-400'" placeholder="Masukkan nama lengkap" maxlength="255" />
                        </div>
                      </Field>
                      <ErrorMessage name="name" class="mt-1 text-sm text-red-600" />
                    </div>

                    <!-- Email -->
                    <div>
                      <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                      <Field name="email" v-slot="{ field, meta }">
                        <div class="relative">
                          <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
                          </div>
                          <input v-bind="field" id="email" type="email" class="w-full pl-10 pr-24 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-500 border transition-colors" :class="meta.touched && !meta.valid ? 'border-red-500' : 'border-gray-300 hover:border-gray-400'" placeholder="user@example.com" maxlength="255" />
                        </div>
                      </Field>
                      <ErrorMessage name="email" class="mt-1 text-sm text-red-600" />
                    </div>

                    <!-- Password Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-gray-500">(opsional)</span></label>
                        <Field name="password" v-slot="{ field, meta }">
                          <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                              <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                            </div>
                            <input v-bind="field" id="password" type="password" class="w-full pl-10 pr-24 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-500 border transition-colors" :class="meta.touched && !meta.valid ? 'border-red-500' : 'border-gray-300 hover:border-gray-400'" placeholder="Kosongkan jika tidak ingin mengubah" />
                          </div>
                        </Field>
                        <ErrorMessage name="password" class="mt-1 text-sm text-red-600" />
                      </div>

                      <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                        <Field name="password_confirmation" v-slot="{ field, meta }">
                          <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                              <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                            </div>
                            <input v-bind="field" id="password_confirmation" type="password" class="w-full pl-10 pr-24 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-500 border transition-colors" :class="meta.touched && !meta.valid ? 'border-red-500' : 'border-gray-300 hover:border-gray-400'" placeholder="Ulangi password" />
                          </div>
                        </Field>
                        <ErrorMessage name="password_confirmation" class="mt-1 text-sm text-red-600" />
                      </div>
                    </div>

                    <!-- SKPD -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">SKPD <span class="text-red-500">*</span></label>
                      <Field name="skpd_id" v-slot="{ field }">
                        <VueSelect
                          v-model="field.value"
                          :options="skpdOptions"
                          placeholder="Pilih SKPD"
                          @change="(v) => field.onChange(v)"
                        />
                      </Field>
                      <ErrorMessage name="skpd_id" class="mt-1 text-sm text-red-600" />
                    </div>

                    <!-- Roles -->
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">Roles <span class="text-red-500">*</span></label>
                      <Field name="roles" v-slot="{ field, meta }">
                        <div class="flex flex-wrap gap-2">
                          <label v-for="role in roles" :key="role.id" class="inline-flex items-center gap-2 text-sm px-3 py-2 rounded-lg border border-brand-100 bg-brand-50 text-brand-800 hover:bg-brand-100 cursor-pointer transition-colors">
                            <input 
                              type="checkbox" 
                              :value="role.name" 
                              :checked="field.value && field.value.includes(role.name)"
                              @change="(e) => {
                                const target = e.target;
                                const checked = !!target?.checked;
                                const currentValue = Array.isArray(field.value) ? field.value : [];
                                if (checked) {
                                  if (!currentValue.includes(role.name)) {
                                    field.onChange([...currentValue, role.name]);
                                  }
                                } else {
                                  field.onChange(currentValue.filter((roleName) => roleName !== role.name));
                                }
                              }"
                              class="rounded border-gray-300 text-brand-700 focus:ring-brand-500" 
                            />
                            {{ role.name }}
                          </label>
                        </div>
                      </Field>
                      <ErrorMessage name="roles" class="mt-1 text-sm text-red-600" />
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Sticky Action Bar -->
            <div class="sticky bottom-0 inset-x-0 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/80 border-t border-gray-200 px-6 py-4 flex justify-end gap-3 shadow-lg">
              <Link href="/users" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500">Batal</Link>
              <button type="submit" :disabled="shouldDisableSubmit(values, meta.valid)" class="px-5 py-2.5 border border-transparent rounded-md shadow-sm text-sm font-semibold text-white bg-brand-700 hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 disabled:opacity-50 disabled:cursor-not-allowed">
                <span v-if="submitting">Menyimpan...</span>
                <span v-else>Simpan Perubahan</span>
              </button>
            </div>
          </Form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { Form, Field, ErrorMessage } from 'vee-validate'
import * as yup from 'yup'
import { ref, computed } from 'vue'
import VueSelect from '@/Components/VueSelect.vue'
import { route as r } from '@/core/helpers/route'
import { toast } from 'vue3-toastify'

const props = defineProps({ user: Object, editing: Object, roles: Array, skpds: Array })

// Options mapping for VueSelect
const skpdOptions = computed(() => (props.skpds || []).map((s) => ({ id: s.id, nama_skpd: s.nama_skpd })))

// Initial values from existing user data
const initialValues = {
  name: props.editing?.name || '',
  email: props.editing?.email || '',
  password: '',
  password_confirmation: '',
  skpd_id: props.editing?.skpd_id || null,
  roles: (props.editing?.roles || []).map((r) => r.name)
}

// Validation schema - password is optional for edit
const schema = yup.object({
  name: yup.string().required('Nama wajib diisi').max(255),
  email: yup.string().required('Email wajib diisi').email('Format email tidak valid').max(255),
  password: yup
    .string()
    .nullable()
    .test('password-length', 'Minimal 8 karakter', function (value) {
      if (!value) return true // Password is optional for edit
      return value.length >= 8
    }),
  password_confirmation: yup
    .string()
    .nullable()
    .test('password-match', 'Konfirmasi password tidak sama', function (value) {
      const password = this.parent.password
      if (!password && !value) return true // Both empty is OK
      return value === password
    }),
  skpd_id: yup.mixed().required('SKPD wajib dipilih'),
  roles: yup.array().of(yup.string()).ensure().min(1, 'Minimal pilih 1 role').default([])
})

const formRef = ref(null)
const submitting = ref(false)

const shouldDisableSubmit = (values, isValid) => {
  return submitting.value || !isValid
}

const onSubmit = async (values) => {
  try {
    submitting.value = true

    // Prepare data - remove empty passwords
    const submitData = {
      name: values.name,
      email: values.email,
      skpd_id: values.skpd_id,
      roles: values.roles || [],
      _method: 'PUT'
    }

    // Only include password if it's not empty
    if (values.password && values.password.trim() !== '') {
      submitData.password = values.password
      submitData.password_confirmation = values.password_confirmation
    }

    router.post(r('users.update', props.editing.id), submitData, {
      onSuccess: () => {
        toast.success('User berhasil diperbarui')
        router.visit(r('users.index'))
      },
      onError: () => {
        toast.error('Gagal memperbarui user')
      },
      onFinish: () => {
        submitting.value = false
      }
    })
  } catch (e) {
    submitting.value = false
    toast.error('Terjadi kesalahan')
  }
}
</script>

