<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';

defineProps<{
    materials: Array<{
        id: number;
        title: string;
        type: string;
        file_path: string;
        year_level: { name: string };
        section?: { name: string }; // ✅ section is now optional
        subject: { name: string };
        user: { name: string };
    }>;
}>();

function createMaterial() {
    router.get('/materials/create');
}

function editMaterial(id: number) {
    router.get(`/materials/${id}/edit`);
}

function deleteMaterial(id: number) {
    if (confirm('Are you sure you want to delete this material?')) {
        router.delete(`/materials/${id}`);
    }
}
</script>

<template>
    <Head title="My Materials" />
    <AppLayout>
        <div class="space-y-4 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">My Uploaded Materials</h1>
                <button @click="createMaterial" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">+ Upload Material</button>
            </div>

            <table class="w-full table-auto border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2">Title</th>
                        <th>Type</th>
                        <th>Year Level</th>
                        <th>Section</th>
                        <!-- ✅ New -->
                        <th>Subject</th>
                        <th>File</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="material in materials" :key="material.id" class="border-t">
                        <td class="p-2">{{ material.title }}</td>
                        <td class="capitalize">{{ material.type.replace('_', ' ') }}</td>
                        <td>{{ material.year_level.name }}</td>
                        <td>{{ material.section?.name || '—' }}</td>
                        <!-- ✅ New -->
                        <td>{{ material.subject.name }}</td>
                        <td>
                            <a :href="`/storage/${material.file_path}`" target="_blank" class="text-blue-600 underline">View</a>
                        </td>
                        <td>
                            <button class="mr-2 text-blue-600" @click="editMaterial(material.id)">Edit</button>
                            <button class="text-red-600" @click="deleteMaterial(material.id)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
