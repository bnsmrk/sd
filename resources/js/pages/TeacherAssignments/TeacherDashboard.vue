<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/teacher-dashboard',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <!-- Grid Container -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                <!-- Total Lists Card -->
                <div
                    class="relative transform overflow-hidden rounded-xl border border-sidebar-border/70 transition-all duration-300 hover:scale-105 hover:shadow-lg dark:border-sidebar-border"
                >
                    <div class="rounded-xl bg-gradient-to-br from-blue-500/10 to-blue-600/10 p-5 shadow-xl">
                        <div class="flex items-center justify-between pb-3">
                            <h2 class="text-sm font-semibold text-blue-500">Total Lists</h2>
                            <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </div>
                        <div class="text-3xl font-semibold text-blue-500">12</div>
                        <p class="text-xs text-gray-600">Your task lists</p>
                    </div>
                </div>

                <!-- Total Tasks Card -->
                <div
                    class="relative transform overflow-hidden rounded-xl border border-sidebar-border/70 transition-all duration-300 hover:scale-105 hover:shadow-lg dark:border-sidebar-border"
                >
                    <div class="rounded-xl bg-gradient-to-br from-green-500/10 to-green-600/10 p-5 shadow-xl">
                        <div class="flex items-center justify-between pb-3">
                            <h2 class="text-sm font-semibold text-green-500">Total Tasks</h2>
                            <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="text-3xl font-semibold text-green-500">34</div>
                        <p class="text-xs text-gray-600">All your tasks</p>
                    </div>
                </div>

                <!-- Pending Tasks Card -->
                <div
                    class="relative transform overflow-hidden rounded-xl border border-sidebar-border/70 transition-all duration-300 hover:scale-105 hover:shadow-lg dark:border-sidebar-border"
                >
                    <div class="rounded-xl bg-gradient-to-br from-yellow-500/10 to-yellow-600/10 p-5 shadow-xl">
                        <div class="flex items-center justify-between pb-3">
                            <h2 class="text-sm font-semibold text-yellow-500">Pending Tasks</h2>
                            <svg class="h-5 w-5 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 8v4l3 3" />
                                <circle cx="12" cy="12" r="10" />
                            </svg>
                        </div>
                        <div class="text-3xl font-semibold text-yellow-500">8</div>
                        <p class="text-xs text-gray-600">Tasks to be completed</p>
                    </div>
                </div>

                <!-- Completed Tasks Card -->
                <div
                    class="relative transform overflow-hidden rounded-xl border border-sidebar-border/70 transition-all duration-300 hover:scale-105 hover:shadow-lg dark:border-sidebar-border"
                >
                    <div class="rounded-xl bg-gradient-to-br from-purple-500/10 to-purple-600/10 p-5 shadow-xl">
                        <div class="flex items-center justify-between pb-3">
                            <h2 class="text-sm font-semibold text-purple-500">Completed Tasks</h2>
                            <svg class="h-5 w-5 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="text-3xl font-semibold text-purple-500">26</div>
                        <p class="text-xs text-gray-600">Tasks completed</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
