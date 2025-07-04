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

const props = defineProps<{
    proficiencyTests: {
        id: number;
        title: string;
        type: string;
        scheduled_at: string;
        description: string | null;
    }[];
    takenTestIds: number[];
}>();

function isTaken(testId: number): boolean {
    return props.takenTestIds.includes(testId);
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayoutStudent :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                <h1>Student Dashboard</h1>
            </div>

            <div class="rounded-lg bg-white p-4 shadow">
                <h2 class="mb-2 text-lg font-semibold text-[#01006c]">Proficiency Tests</h2>

                <div v-if="proficiencyTests.length === 0" class="text-sm text-gray-500">No proficiency tests scheduled.</div>

                <div v-for="test in proficiencyTests" :key="test.id" class="mb-2 rounded border p-4 hover:bg-gray-50">
                    <div class="font-semibold text-[#01006c]">{{ test.title }}</div>
                    <div class="text-sm text-gray-600">Scheduled at: {{ test.scheduled_at }}</div>

                    <div class="mt-2">
                        <a
                            v-if="!isTaken(test.id)"
                            :href="`/student/proficiency-test/${test.id}`"
                            class="inline-block rounded bg-blue-600 px-4 py-1 text-sm text-white hover:bg-blue-700"
                        >
                            Take Test
                        </a>
                        <span v-else class="inline-block cursor-not-allowed rounded bg-gray-400 px-4 py-1 text-sm text-white"> ✓ Already Taken </span>
                    </div>
                </div>
            </div>
        </div>
    </AppLayoutStudent>
</template>
