<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps<{
    subjects: Array<{
        id: number;
        name: string;
        year_level: { name: string };
        section: { name: string };
    }>;
}>();

const destroyItem = (id: number) => {
    if (confirm('Are you sure?')) {
        router.delete(`/subjects/${id}`);
    }
};
</script>

<template>
    <Head title="Subjects" />
    <AppLayout>
        <div class="p-4">
            <div class="mb-4 flex justify-between">
                <h2 class="text-xl font-bold">Subjects</h2>
                <Link href="/subjects/create" class="rounded bg-blue-600 px-4 py-2 text-white">Add Subject</Link>
            </div>
            <!-- <table class="w-full table-auto border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2">Name</th>
                        <th class="border px-4 py-2">Year Level</th>
                        <th class="border px-4 py-2">Section</th>
                        <th class="border px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="subject in subjects" :key="subject.id">
                        <td class="border px-4 py-2">{{ subject.name }}</td>
                        <td class="border px-4 py-2">{{ subject.year_level.name }}</td>
                        <td class="border px-4 py-2">{{ subject.section.name }}</td>
                        <td class="border px-4 py-2">
                            <Link :href="`/subjects/${subject.id}/edit`" class="mr-2 text-blue-600">Edit</Link>
                            <button @click="destroyItem(subject.id)" class="text-red-600">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table> -->
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="min-w-full text-left text-sm text-gray-500 rtl:text-right dark:text-gray-400">
                    <thead class="bg-gray-50 text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Name</th>
                            <th scope="col" class="px-6 py-3">Year Level</th>
                            <th scope="col" class="px-6 py-3">Section</th>
                            <th scope="col" class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="subject in subjects"
                            :key="subject.id"
                            class="border-b border-gray-200 bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600"
                        >
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ subject.name }}</td>
                            <td class="px-6 py-4">{{ subject.year_level.name }}</td>
                            <td class="px-6 py-4">{{ subject.section.name }}</td>
                            <td class="flex items-center justify-center space-x-3 px-6 py-4">
                                <Link :href="`/subjects/${subject.id}/edit`" class="font-medium text-blue-600 hover:underline dark:text-blue-500"
                                    >Edit</Link
                                >
                                <button @click="destroyItem(subject.id)" class="font-medium text-red-600 hover:underline dark:text-red-500">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
