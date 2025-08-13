<template>
    <nav class="relative z-10 bg-white/80 backdrop-blur-md border-b border-gray-200/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-2">
                    <Link href="/" class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                                SIMPAN
                            </span>
                            <span class="text-xs text-gray-600 -mt-1">
                                Kab. Hulu Sungai Selatan
                            </span>
                        </div>
                    </Link>
                </div>
                <div class="flex items-center space-x-4">
                    <Link
                        href="/"
                        class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                    >
                        Beranda
                    </Link>
                    <Link
                        href="/knowledge/public"
                        class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                    >
                        Temukan Pengetahuan
                    </Link>
                    <Link
                        href="/login"
                        class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl"
                    >
                        Masuk Sistem
                    </Link>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
</script>