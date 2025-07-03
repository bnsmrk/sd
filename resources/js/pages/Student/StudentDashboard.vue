<script setup lang="ts">
import AppLayoutStudent from '@/layouts/AppLayoutStudent.vue';

import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/student-dashboard',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayoutStudent :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <!-- Grid Container -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                <h1>Student Dashboard</h1>
            </div>
        </div>
    </AppLayoutStudent>
</template>
