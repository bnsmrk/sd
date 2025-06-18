<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps<{
    sections: Array<{
        id: number;
        name: string;
        year_level: { name: string };
    }>;
}>();

const breadcrumbs = [{ title: 'Sections', href: '/sections' }];

const destroyItem = (id: number) => {
    if (confirm('Are you sure you want to delete this section?')) {
        router.delete(`/sections/${id}`);
    }
};
</script>

<template>
    <Head title="Sections" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold">Sections</h2>
                <Link href="/sections/create" class="rounded bg-blue-600 px-4 py-2 text-white">Add Section</Link>
            </div>

            <!-- <table class="w-full table-auto border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2">ID</th>
                        <th class="border px-4 py-2">Section Name</th>
                        <th class="border px-4 py-2">Year Level</th>
                        <th class="border px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="section in sections" :key="section.id">
                        <td class="border px-4 py-2">{{ section.id }}</td>
                        <td class="border px-4 py-2">{{ section.name }}</td>
                        <td class="border px-4 py-2">{{ section.year_level.name }}</td>
                        <td class="border px-4 py-2">
                            <Link :href="`/sections/${section.id}/edit`" class="mr-4 text-blue-600 hover:underline">Edit</Link>
                            <button @click="destroyItem(section.id)" class="text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table> -->
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="min-w-full text-left text-sm text-gray-500 rtl:text-right dark:text-gray-400">
                    <thead class="bg-gray-50 text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">ID</th>
                            <th scope="col" class="px-6 py-3">Section Name</th>
                            <th scope="col" class="px-6 py-3">Year Level</th>
                            <th scope="col" class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="section in sections"
                            :key="section.id"
                            class="border-b border-gray-200 bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600"
                        >
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ section.id }}</td>
                            <td class="px-6 py-4">{{ section.name }}</td>
                            <td class="px-6 py-4">{{ section.year_level.name }}</td>
                            <td class="flex items-center justify-center space-x-3 px-6 py-4">
                                <Link :href="`/sections/${section.id}/edit`" class="font-medium text-blue-600 hover:underline dark:text-blue-500"
                                    >Edit</Link
                                >
                                <button @click="destroyItem(section.id)" class="font-medium text-red-600 hover:underline dark:text-red-500">
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
