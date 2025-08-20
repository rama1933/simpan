<template>
    <PublicLayout>
        <!-- Page Title -->
        <PublicPageHeader
            title="Temukan Pengetahuan"
            description="Jelajahi koleksi pengetahuan yang telah terverifikasi dari Pemerintah Kabupaten Hulu Sungai Selatan"
        />

        <!-- Filter Section -->
        <div class="bg-white p-6 rounded-xl shadow-md ring-1 ring-gray-100 mb-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Filter Data</h3>
                        <p class="text-sm text-gray-500 mt-1">Saring data berdasarkan kriteria yang diinginkan</p>
                    </div>
                    <div class="flex space-x-3">
                        <button
                            @click="toggleAIAssistant"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                            AI Assistant
                        </button>
                        <button
                            @click="search"
                            class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"/>
                            </svg>
                            Filter Data
                        </button>
                        <button
                            @click="resetFilters"
                            :disabled="!hasActiveFilters"
                            :class="[
                                'inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border transition-all duration-200',
                                hasActiveFilters
                                    ? 'text-gray-600 bg-gray-50 border-gray-300 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 shadow-sm'
                                    : 'text-gray-400 bg-gray-100 border-gray-200 cursor-not-allowed'
                            ]"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Hapus Filter
                        </button>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Single Row Layout -->
                    <div class="grid grid-cols-1 lg:grid-cols-6 gap-4 items-start">
                        <!-- Google-like Search -->
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian Cerdas</label>
                            <GoogleLikeSearch
                                :initial-value="searchForm.search"
                                @search="handleSearch"
                                @select="handleSelectKnowledge"
                            />
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                            <VueSelect
                                v-model="searchForm.category_id"
                                :options="categoryOptions"
                                placeholder="Pilih Kategori..."
                            />
                        </div>

                        <!-- SKPD -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">SKPD</label>
                            <VueSelect
                                v-model="searchForm.skpd_id"
                                :options="skpdOptions"
                                placeholder="Pilih SKPD..."
                            />
                        </div>

                        <!-- Tags -->
                        <div class="lg:col-span-2 relative">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                            <div class="relative">
                                <input
                                    v-model="tagsQuery"
                                    @input="debouncedSearchTags"
                                    @focus="showTagOptions = true"
                                    type="text"
                                    placeholder="Cari tag..."
                                    class="w-full h-[42px] px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 hover:border-gray-400 transition-colors duration-200"
                                />

                                <!-- Tag Options Dropdown -->
                                <div v-if="showTagOptions && tagOptions.length > 0" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                    <div
                                        v-for="tag in tagOptions"
                                        :key="tag.id"
                                        @click="toggleTag(tag)"
                                        class="px-3 py-2 hover:bg-gray-100 cursor-pointer flex items-center justify-between"
                                    >
                                        <span>{{ tag.name }}</span>
                                        <span v-if="selectedTagIds.includes(tag.id)" class="text-blue-500">✓</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tag Options and Selected Tags (Below main row) -->
                    <div v-if="tagOptions.length > 0 || selectedTagIds.length > 0" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div v-if="tagOptions.length > 0">
                            <div class="text-xs text-gray-500 mb-2">Pilih tag:</div>
                            <div class="flex flex-wrap gap-2 max-h-32 overflow-y-auto">
                                <button
                                    v-for="tag in tagOptions"
                                    :key="tag.id"
                                    type="button"
                                    @click="toggleTag(tag)"
                                    :class="selectedTagIds.includes(tag.id) ? 'bg-blue-600 text-white border-blue-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 border-gray-200'"
                                    class="px-2.5 py-1.5 text-xs rounded-md border transition-colors duration-200"
                                >
                                    {{ tag.name }}
                                    <span v-if="selectedTagIds.includes(tag.id)" class="ml-1">✓</span>
                                </button>
                            </div>
                        </div>
                        <div v-if="selectedTagIds.length">
                            <div class="text-xs text-gray-500 mb-2">Tag terpilih ({{ selectedTagIds.length }}):</div>
                            <div class="flex flex-wrap gap-2">
                                <span v-for="id in selectedTagIds" :key="`sel-${id}`" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs bg-indigo-100 text-indigo-800">
                                    {{ tagNameById(id) }}
                                    <button class="ml-1.5 text-indigo-600 hover:text-indigo-800 font-medium" @click="removeTag(id)">×</button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active filters display -->
                <div v-if="hasActiveFilters" class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500">Filter aktif:</span>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-if="searchForm.search"
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                            >
                                Search: {{ searchForm.search }}
                                <button
                                    @click="searchForm.search = ''"
                                    class="ml-1.5 text-blue-700 hover:text-blue-900"
                                >
                                    ×
                                </button>
                            </span>
                            <span
                                v-if="searchForm.category_id"
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
                            >
                                Kategori: {{ getCategoryName(searchForm.category_id) }}
                                <button
                                    @click="searchForm.category_id = ''"
                                    class="ml-1.5 text-green-600 hover:text-green-800"
                                >
                                    ×
                                </button>
                            </span>
                            <span
                                v-if="searchForm.skpd_id"
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800"
                            >
                                SKPD: {{ getSKPDName(searchForm.skpd_id) }}
                                <button
                                    @click="searchForm.skpd_id = ''"
                                    class="ml-1.5 text-purple-600 hover:text-purple-800"
                                >
                                    ×
                                </button>
                            </span>
                            <span
                                v-if="selectedTagIds.length > 0"
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
                            >
                                Tags: {{ selectedTagIds.length }} dipilih
                                <button
                                    @click="selectedTagIds = []; selectedTags = []"
                                    class="ml-1.5 text-indigo-600 hover:text-indigo-800"
                                >
                                    ×
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Knowledge Grid -->
            <div v-if="knowledge && knowledge.data && knowledge.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="item in knowledge.data"
                    :key="item.id"
                    class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow cursor-pointer"
                    @click="viewKnowledge(item.id)"
                >
                    <div class="p-6">
                        <!-- Category Badge -->
                        <div class="flex items-center justify-between mb-3">
                            <!-- <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ item.category?.name || 'Umum' }}
                            </span> -->
                            <!-- <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Terverifikasi
                            </span> -->
                        </div>

                        <!-- Title -->
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                            {{ item.title }}
                        </h3>

                        <!-- Description -->
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                            {{ item.description || 'Tidak ada deskripsi' }}
                        </p>

                        <!-- Meta Info -->
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <div class="flex items-center space-x-4">
                                <!-- <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ item.author?.name || 'Unknown' }}
                                </span> -->
                                <!-- <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    {{ item.skpd?.nama_skpd || 'SKPD' }}
                                </span> -->
                            </div>
                            <div>
                                {{ formatDate(item.created_at) }}
                            </div>
                        </div>

                        <!-- Tags -->
                        <div v-if="item.tags_relation && item.tags_relation.length > 0" class="mt-3 flex flex-wrap gap-1">
                            <span
                                v-for="tag in item.tags_relation.slice(0, 3)"
                                :key="tag.id"
                                class="inline-flex items-center px-2 py-1 rounded text-xs bg-blue-100 text-blue-800"
                            >
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                {{ tag.name }}
                            </span>
                            <span v-if="item.tags_relation.length > 3" class="text-xs text-gray-500">
                                +{{ item.tags_relation.length - 3 }} lainnya
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
                <div class="w-24 h-24 mx-auto mb-4 text-gray-300">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada pengetahuan ditemukan</h3>
                <p class="text-gray-600">Coba ubah filter pencarian atau kata kunci Anda.</p>
            </div>

            <!-- Pagination -->
            <div v-if="knowledge && knowledge.data && knowledge.data.length > 0 && knowledge.links && knowledge.links.length > 0" class="mt-8">
                <nav class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <Link
                            v-if="knowledge.prev_page_url"
                            :href="knowledge.prev_page_url"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Sebelumnya
                        </Link>
                        <Link
                            v-if="knowledge.next_page_url"
                            :href="knowledge.next_page_url"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                        >
                            Selanjutnya
                        </Link>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Menampilkan
                                <span class="font-medium">{{ knowledge.from }}</span>
                                sampai
                                <span class="font-medium">{{ knowledge.to }}</span>
                                dari
                                <span class="font-medium">{{ knowledge.total }}</span>
                                hasil
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                <template v-for="link in knowledge.links" :key="link.label">
                                    <Link
                                        v-if="link.url"
                                        :href="link.url"
                                        :class="[
                                            'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                            link.active
                                                ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                                                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                                        ]"
                                        :preserve-scroll="true"
                                        v-html="link.label"
                                    >
                                    </Link>
                                    <span
                                        v-else
                                        :class="[
                                            'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                            'bg-gray-100 border-gray-300 text-gray-400 cursor-not-allowed'
                                        ]"
                                        v-html="link.label"
                                    >
                                    </span>
                                </template>
                            </nav>
                        </div>
                    </div>
                </nav>
            </div>

        <!-- AI Assistant Modal -->
        <div v-if="showAIModal" class="fixed inset-0 z-50 overflow-y-auto" @click="closeAIModal">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full" @click.stop>
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                                AI Assistant
                            </h3>
                            <button @click="closeAIModal" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Chat Messages -->
                        <div class="h-96 overflow-y-auto border border-gray-200 rounded-lg p-4 mb-4 bg-gray-50">
                            <div v-if="chatMessages.length === 0" class="text-center text-gray-500 mt-20">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <p>Mulai percakapan dengan AI Assistant tentang sistem manajemen pengetahuan</p>
                            </div>

                            <div v-for="(message, index) in chatMessages" :key="index" class="mb-4">
                                <div :class="message.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                                    <div :class="[
                                        'max-w-xs lg:max-w-md px-4 py-2 rounded-lg',
                                        message.role === 'user'
                                            ? 'bg-purple-500 text-white'
                                            : 'bg-gray-100 text-gray-800'
                                    ]">
                                        <p class="text-sm whitespace-pre-wrap">{{ message.content }}</p>
                                    </div>
                                </div>
                            </div>

                            <div v-if="aiLoading" class="flex justify-start mb-4">
                                <div class="bg-white border border-gray-200 px-4 py-2 rounded-lg">
                                    <div class="flex items-center space-x-2">
                                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-purple-600"></div>
                                        <span class="text-sm text-gray-500">AI sedang mengetik...</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Message Input -->
                        <div class="flex space-x-2">
                            <input
                                v-model="currentMessage"
                                @keyup.enter="sendMessage"
                                type="text"
                                placeholder="Tanyakan tentang sistem manajemen pengetahuan..."
                                class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                :disabled="aiLoading"
                            />
                            <button
                                @click="sendMessage"
                                :disabled="aiLoading || !currentMessage.trim()"
                                class="px-6 py-2 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<script setup>
import { ref, reactive, computed, nextTick, onMounted, onUnmounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import VueSelect from '@/Components/VueSelect.vue'
import GoogleLikeSearch from '@/components/GoogleLikeSearch.vue'
import axios from 'axios'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import PublicPageHeader from '@/Components/Public/PublicPageHeader.vue'
import { route } from '@/core/helpers/route'



// Props
const props = defineProps({
    knowledge: Object,
    filters: Object,
    categories: Array,
    skpds: Array
})

// Search form
const searchForm = reactive({
    search: props.filters?.search || '',
    category_id: props.filters?.category_id || '',
    skpd_id: props.filters?.skpd_id || ''
})

// AI Assistant state
const showAIModal = ref(false)
const chatMessages = ref([])
const currentMessage = ref('')
const aiLoading = ref(false)

// Advanced filters state
const showAdvancedFilters = ref(false)

// Options for VueSelect components
const categoryOptions = computed(() => {
    const options = [{ value: '', label: 'Semua Kategori' }]
    if (props.categories) {
        options.push(...props.categories.map(category => ({
            value: category.id,
            label: category.name
        })))
    }
    return options
})

const skpdOptions = computed(() => {
    const options = [{ value: '', label: 'Semua SKPD' }]
    if (props.skpds) {
        options.push(...props.skpds.map(skpd => ({
            value: skpd.id,
            label: skpd.nama_skpd
        })))
    }
    return options
})

// Tags state
const tagsQuery = ref('')
const selectedTagIds = ref(Array.isArray(props.filters?.tags) ? props.filters.tags : [])
const tagOptions = ref([])
const selectedTags = ref([]) // Cache untuk menyimpan data tag yang dipilih
const showTagOptions = ref(false)

// Methods
const search = () => {
    const filterParams = {
        ...searchForm,
        tags: selectedTagIds.value.length > 0 ? selectedTagIds.value : undefined
    }

    // Remove empty values
    Object.keys(filterParams).forEach(key => {
        if (filterParams[key] === '' || filterParams[key] === null || filterParams[key] === undefined) {
            delete filterParams[key]
        }
    })

    router.get('/knowledge/public', filterParams, {
        preserveState: true,
        preserveScroll: true
    })
}

const resetFilters = () => {
    searchForm.search = ''
    searchForm.category_id = ''
    searchForm.skpd_id = ''
    selectedTagIds.value = []
    selectedTags.value = []
    tagsQuery.value = ''
    tagOptions.value = []
    search()
}

// Tags methods
const tagNameById = (id) => {
    // Cari di tagOptions terlebih dahulu
    const tag = tagOptions.value.find(t => t.id === id)
    if (tag) return tag.name

    // Jika tidak ditemukan, cari di selectedTags cache
    const selectedTag = selectedTags.value.find(t => t.id === id)
    return selectedTag?.name || `Tag ${id}`
}

const removeTag = (id) => {
    selectedTagIds.value = selectedTagIds.value.filter(t => t !== id)
    // Hapus juga dari cache selectedTags
    selectedTags.value = selectedTags.value.filter(t => t.id !== id)
    search()
}

const toggleTag = (tag) => {
    if (selectedTagIds.value.includes(tag.id)) {
        removeTag(tag.id)
    } else {
        selectedTagIds.value.push(tag.id)
        // Simpan data tag ke cache jika belum ada
        if (!selectedTags.value.find(t => t.id === tag.id)) {
            selectedTags.value.push(tag)
        }
        search()
    }
    // Tutup dropdown setelah memilih tag
    showTagOptions.value = false
    tagsQuery.value = ''
}

const searchTags = async () => {
    if (tagsQuery.value.length < 2) {
        tagOptions.value = []
        return
    }

    try {
        const response = await axios.get('/api/knowledge/tags', {
            params: { q: tagsQuery.value }
        })
        tagOptions.value = response.data.data || []
    } catch (error) {
        console.error('Error searching tags:', error)
        tagOptions.value = []
    }
}

// Debounced search function
let searchTimeout = null
const debouncedSearchTags = () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        searchTags()
    }, 300)
}

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
    const tagContainer = event.target.closest('.relative')
    if (!tagContainer || !tagContainer.querySelector('input[placeholder="Cari tag..."]')) {
        showTagOptions.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})

const viewKnowledge = (id) => {
    router.visit(route('knowledge.public.show', id))
}

const formatDate = (dateString) => {
    const date = new Date(dateString)
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

// Computed properties for filter state
const hasActiveFilters = computed(() => {
    return searchForm.search ||
           searchForm.category_id ||
           searchForm.skpd_id ||
           selectedTagIds.value.length > 0
})

const getCategoryName = (id) => {
    const category = props.categories?.find(c => c.id == id)
    return category?.name || 'Unknown'
}

const getSKPDName = (id) => {
    const skpd = props.skpds?.find(s => s.id == id)
    return skpd?.nama_skpd || 'Unknown'
}

// Google-like search handlers
const handleSearch = (query) => {
    searchForm.search = query
    search()
}

const handleSelectKnowledge = (knowledge) => {
    router.visit(`/knowledge/public/${knowledge.id}`)
}

// AI Assistant functions
const toggleAIAssistant = () => {
    showAIModal.value = true
    if (chatMessages.value.length === 0) {
        chatMessages.value.push({
            role: 'assistant',
            content: 'Halo! Saya adalah AI Assistant yang akan membantu Anda menjelajahi dan memahami seluruh pengetahuan yang tersedia di sistem manajemen pengetahuan ini.\n\nSaya dapat membantu Anda dengan:\n- Mencari informasi tentang dokumen pengetahuan yang tersedia\n- Menjelaskan konsep atau topik dari berbagai dokumen\n- Memberikan ringkasan dari pengetahuan yang ada\n- Menjawab pertanyaan umum tentang sistem\n\nAda yang ingin Anda ketahui tentang pengetahuan di sistem ini?'
        })
    }
}

const closeAIModal = () => {
    showAIModal.value = false
}

const sendMessage = async () => {
    if (!currentMessage.value.trim() || aiLoading.value) return

    const message = currentMessage.value.trim()
    currentMessage.value = ''

    // Add user message
    chatMessages.value.push({
        role: 'user',
        content: message
    })

    aiLoading.value = true

    try {
        // Call Gemini AI API
        const response = await fetch('/api/ai/gemini/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            },
            body: JSON.stringify({
                message: message,
                context: {
                    knowledge_title: 'Sistem Manajemen Pengetahuan',
                    knowledge_content: 'Seluruh pengetahuan yang tersedia di aplikasi',
                    knowledge_description: 'AI Assistant untuk membantu menjawab pertanyaan tentang seluruh pengetahuan yang ada di sistem manajemen pengetahuan ini, termasuk dokumen, konsep, dan informasi yang tersimpan'
                }
            })
        })

        if (!response.ok) {
            throw new Error('Failed to get AI response')
        }

        const data = await response.json()

        // Add AI response
        chatMessages.value.push({
            role: 'assistant',
            content: data.response
        })
    } catch (error) {
        console.error('AI Error:', error)
        chatMessages.value.push({
            role: 'assistant',
            content: 'Maaf, terjadi kesalahan saat menghubungi AI Assistant. Silakan coba lagi.'
        })
    } finally {
        aiLoading.value = false
        await nextTick()
        // Scroll to bottom of chat
        const chatContainer = document.querySelector('.h-96.overflow-y-auto')
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight
        }
    }
}
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
