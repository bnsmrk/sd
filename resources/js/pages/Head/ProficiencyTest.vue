<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps<{
    tests: Array<{
        id: number;
        title: string;
        type: string;
        scheduled_at: string;
        year_level: { name: string };
    }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Proficiency Test',
        href: '/proficiency-test',
    },
];

function deleteTest(id: number) {
    if (confirm('Are you sure you want to delete this test?')) {
        router.delete(`/proficiency-test/${id}`);
    }
}
</script>

<template>
    <Head title="Proficiency Test" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-[#01006c]">Proficiency Tests</h1>
                <Link href="/proficiency-test/create" class="rounded bg-[#01006c] px-4 py-2 text-white hover:bg-[#0d1282]"> + Create Test </Link>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-lg border border-gray-300">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-[#01006c] text-white">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold">Title</th>
                            <th class="px-4 py-2 text-left font-semibold">Type</th>
                            <th class="px-4 py-2 text-left font-semibold">Scheduled At</th>
                            <th class="px-4 py-2 text-left font-semibold">Year Level</th>

                            <th class="px-4 py-2 text-left font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="test in props.tests" :key="test.id" class="border-b">
                            <td class="px-4 py-2">{{ test.title }}</td>
                            <td class="px-4 py-2 capitalize">{{ test.type }}</td>
                            <td class="px-4 py-2">{{ new Date(test.scheduled_at).toLocaleString() }}</td>
                            <td class="px-4 py-2">{{ test.year_level.name }}</td>

                            <td class="space-x-2 px-4 py-2">
                                <Link :href="`/proficiency-test/${test.id}/edit`" class="text-blue-600 hover:underline"> Edit </Link>
                                <Link :href="`/proficiency-test/${test.id}/questions/create`" class="text-indigo-600 hover:underline">Add Questions</Link>
                                <button @click="deleteTest(test.id)" class="text-red-600 hover:underline">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="props.tests.length === 0">
                            <td colspan="7" class="px-4 py-4 text-center text-gray-500">No proficiency tests found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
