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

            <div class="overflow-auto rounded border">
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
            </div>
        </div>
    </AppLayout>
</template>
