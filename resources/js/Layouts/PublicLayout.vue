<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
        <!-- Header -->
        <PublicHeader />

        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-30 pointer-events-none">
            <svg class="absolute top-0 left-0 w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs>
                    <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                        <path d="M 10 0 L 0 0 0 10" fill="none" stroke="#e5e7eb" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100" height="100" fill="url(#grid)"/>
            </svg>
        </div>

        <!-- Main Content -->
        <main class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <slot />
        </main>

        <!-- Footer -->
        <PublicFooter />
    </div>
</template>

<script setup>
import PublicHeader from '@/Components/Public/PublicHeader.vue'
import PublicFooter from '@/Components/Public/PublicFooter.vue'

// Props untuk konfigurasi layout jika diperlukan
defineProps({
    pageTitle: {
        type: String,
        default: 'SIMPAN - Pengetahuan Publik'
    }
})
</script>