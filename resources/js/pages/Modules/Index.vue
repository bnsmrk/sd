<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps<{
    modules: {
        id: number;
        name: string;
        year_level: { id: number; name: string };
        section: { id: number; name: string };
        subject: { id: number; name: string };
    }[];
}>();

function deleteModule(id: number) {
    if (confirm('Are you sure you want to delete this module?')) {
        router.delete(`/modules/${id}`);
    }
}
</script>

<template>
    <Head title="Modules" />
    <AppLayout>
        <div class="space-y-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold">Modules</h1>
                <Link href="/modules/create" class="rounded bg-blue-600 px-4 py-2 text-white">Create Module</Link>
            </div>

            <!-- <div class="overflow-auto rounded border">
                <table class="min-w-full divide-y">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 text-left">Name</th>
                            <th class="p-2 text-left">Year</th>
                            <th class="p-2 text-left">Section</th>
                            <th class="p-2 text-left">Subject</th>
                            <th class="p-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="m in props.modules" :key="m.id" class="border-t">
                            <td class="p-2">{{ m.name }}</td>
                            <td class="p-2">{{ m.year_level.name }}</td>
                            <td class="p-2">{{ m.section.name }}</td>
                            <td class="p-2">{{ m.subject.name }}</td>
                            <td class="space-x-2 p-2 text-center">
                                <Link :href="`/modules/${m.id}/edit`" class="text-blue-600 hover:underline">Edit</Link>
                                <button @click="deleteModule(m.id)" class="text-red-600 hover:underline">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div> -->
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="min-w-full text-left text-sm text-gray-500 rtl:text-right dark:text-gray-400">
                    <thead class="bg-gray-50 text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Name</th>
                            <th scope="col" class="px-6 py-3">Year</th>
                            <th scope="col" class="px-6 py-3">Section</th>
                            <th scope="col" class="px-6 py-3">Subject</th>
                            <th scope="col" class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="m in props.modules"
                            :key="m.id"
                            class="border-b border-gray-200 bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600"
                        >
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ m.name }}</td>
                            <td class="px-6 py-4">{{ m.year_level.name }}</td>
                            <td class="px-6 py-4">{{ m.section.name }}</td>
                            <td class="px-6 py-4">{{ m.subject.name }}</td>
                            <td class="flex items-center justify-center space-x-3 px-6 py-4">
                                <Link :href="`/modules/${m.id}/edit`" class="font-medium text-blue-600 hover:underline dark:text-blue-500">Edit</Link>
                                <button @click="deleteModule(m.id)" class="font-medium text-red-600 hover:underline dark:text-red-500">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
