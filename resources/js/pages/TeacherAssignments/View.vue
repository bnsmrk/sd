<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps<{
    assignment: {
        id: number;
        teacher: { id: number; name: string };
        year_level: { id: number; name: string };
        section: { id: number; name: string } | null;
        subject: { id: number; name: string };
    };
}>();
</script>

<template>
    <Head title="View Assignment" />
    <AppLayout :breadcrumbs="[{ title: 'Assignments', href: '/teacher-assignments' }]">
        <div class="mx-auto mt-10 max-w-6xl space-y-8 px-6">
            <div class="flex items-center justify-between">
                <h1 class="text-lg font-bold text-gray-900 dark:text-white">Teacher Assignment Details</h1>
                <Link
                    href="/teacher-assignments"
                    class="rounded-lg bg-gray-800 px-2 py-2 text-sm font-medium text-white hover:bg-gray-900 focus:ring-2 focus:ring-gray-300 dark:hover:bg-gray-700 dark:focus:ring-gray-700"
                >
                    ← Back to List
                </Link>
            </div>

            <div class="grid grid-cols-2 gap-2 rounded-lg border border-gray-200 bg-white p-4 shadow dark:border-gray-700 dark:bg-gray-800">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-600 dark:text-gray-300">Teacher</label>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ props.assignment.teacher.name }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-600 dark:text-gray-300">Year Level</label>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ props.assignment.year_level.name }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-600 dark:text-gray-300">Section</label>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ props.assignment.section?.name ?? '— (Shared Subject)' }}
                    </p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-600 dark:text-gray-300">Subject</label>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ props.assignment.subject.name }}</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
