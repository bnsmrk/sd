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
        <div class="mx-auto max-w-2xl space-y-6 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">Teacher Assignment Details</h1>
                <Link href="/teacher-assignments" class="rounded bg-gray-200 px-4 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-300">
                    Back
                </Link>
            </div>

            <div class="rounded border bg-white p-6 shadow-sm">
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-600">Teacher</label>
                    <p class="text-lg text-gray-800">{{ props.assignment.teacher.name }}</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-600">Year Level</label>
                    <p class="text-lg text-gray-800">{{ props.assignment.year_level.name }}</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-600">Section</label>
                    <p class="text-lg text-gray-800">
                        {{ props.assignment.section?.name ?? '— (Shared Subject)' }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-600">Subject</label>
                    <p class="text-lg text-gray-800">{{ props.assignment.subject.name }}</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
