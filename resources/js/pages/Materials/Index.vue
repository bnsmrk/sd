<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps<{
    materials: Array<{
        id: number;
        title: string;
        type: string;
        file_path: string;
        year_level: { name: string };
        section?: { name: string };
        subject: { name: string };
        user: { name: string };
        comments?: Array<{
            id: number;
            comment: string;
            user: { name: string };
        }>;
    }>;
}>();

const showDeleteModal = ref(false);
const deleteId = ref<number | null>(null);

function createMaterial() {
    router.get('/materials/create');
}

function editMaterial(id: number) {
    router.get(`/materials/${id}/edit`);
}

function confirmDelete(id: number) {
    deleteId.value = id;
    showDeleteModal.value = true;
}

function destroyMaterial() {
    if (deleteId.value !== null) {
        router.delete(`/materials/${deleteId.value}`);
        showDeleteModal.value = false;
        deleteId.value = null;
    }
}

function cancelDelete() {
    showDeleteModal.value = false;
    deleteId.value = null;
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
                        <th>Subject</th>
                        <th>File</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="material in materials" :key="material.id">
                        <tr class="border-t">
                            <td class="p-2 align-top">{{ material.title }}</td>
                            <td class="align-top capitalize">{{ material.type.replace('_', ' ') }}</td>
                            <td class="align-top">{{ material.year_level.name }}</td>
                            <td class="align-top">{{ material.section?.name || '—' }}</td>
                            <td class="align-top">{{ material.subject.name }}</td>
                            <td class="align-top">
                                <a :href="`/storage/${material.file_path}`" target="_blank" class="text-blue-600 underline">View</a>
                            </td>
                            <td class="align-top">
                                <button class="mr-2 text-blue-600" @click="editMaterial(material.id)">Edit</button>
                                <button class="text-red-600" @click="confirmDelete(material.id)">Delete</button>
                            </td>
                        </tr>

                        <!-- ✅ Comments shown under the same material -->
                        <tr v-if="material.comments?.length" class="bg-gray-50 text-sm">
                            <td colspan="7" class="p-4">
                                <p class="mb-2 font-medium text-gray-700">Comments from Principal:</p>
                                <ul class="ml-4 list-disc space-y-1 text-gray-600">
                                    <li v-for="comment in material.comments" :key="comment.id">
                                        "{{ comment.comment }}" — <i>{{ comment.user.name }}</i>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-white/30 backdrop-blur-sm">
            <div class="w-full max-w-md rounded bg-white p-6 shadow-lg dark:bg-gray-800">
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Confirm Deletion</h2>
                <p class="mb-6 text-gray-600 dark:text-gray-300">Are you sure you want to delete this material?</p>
                <div class="flex justify-end space-x-4">
                    <button
                        @click="cancelDelete"
                        class="rounded bg-gray-300 px-4 py-2 text-sm text-gray-800 hover:bg-gray-400 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500"
                    >
                        Cancel
                    </button>
                    <button @click="destroyMaterial" class="rounded bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700">Confirm</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
