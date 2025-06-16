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

            <table class="w-full table-auto border">
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
            </table>
        </div>
    </AppLayout>
</template>
