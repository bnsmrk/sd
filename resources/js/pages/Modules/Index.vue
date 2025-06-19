<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps<{
    modules: {
        id: number;
        name: string;
        year_level: { id: number; name: string };
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

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="min-w-full text-left text-sm text-gray-700">
                    <thead class="bg-gray-100 text-xs uppercase">
                        <tr>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Year</th>
                            <th class="px-6 py-3">Subject</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="m in props.modules" :key="m.id" class="border-b bg-white hover:bg-gray-50">
                            <td class="px-6 py-4">{{ m.name }}</td>
                            <td class="px-6 py-4">{{ m.year_level.name }}</td>
                            <td class="px-6 py-4">{{ m.subject.name }}</td>
                            <td class="space-x-2 px-6 py-4 text-center">
                                <Link :href="`/modules/${m.id}/edit`" class="text-blue-600 hover:underline">Edit</Link>
                                <button @click="deleteModule(m.id)" class="text-red-600 hover:underline">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
