<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps<{
    assignments: Array<{
        id: number;
        teacher: { id: number; name: string };
        year_level: { name: string };
        subject: { name: string };
    }>;
}>();

// Modal state
const showDeleteModal = ref(false);
const deleteId = ref<number | null>(null);

function confirmDelete(id: number) {
    deleteId.value = id;
    showDeleteModal.value = true;
}

function deleteAssignment() {
    if (deleteId.value !== null) {
        router.delete(`/teacher-assignments/${deleteId.value}`);
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
    <Head title="Teacher Assignments" />
    <AppLayout>
        <div class="p-6">
            <h1 class="mb-4 text-xl font-bold">Teacher Assignments</h1>

            <Link href="/teacher-assignments/create" class="inline-block rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                Assign Teacher
            </Link>

            <table class="mt-6 w-full table-auto border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border px-4 py-2 text-left">Teacher</th>
                        <th class="border px-4 py-2 text-left">Year Level</th>
                        <th class="border px-4 py-2 text-left">Subject</th>
                        <th class="border px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="a in assignments" :key="a.id" class="hover:bg-gray-50">
                        <td class="border px-4 py-2">{{ a.teacher.name }}</td>
                        <td class="border px-4 py-2">{{ a.year_level.name }}</td>
                        <td class="border px-4 py-2">{{ a.subject.name }}</td>
                        <td class="space-x-2 border px-4 py-2">
                            <Link :href="`/teacher-assignments/${a.id}`" class="text-green-600 hover:underline"> View </Link>
                            <Link :href="`/teacher-assignments/${a.id}/edit`" class="text-blue-600 hover:underline"> Edit </Link>
                            <button @click="confirmDelete(a.id)" class="text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Delete Confirmation Modal with Blurred Background -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-white/30 backdrop-blur-sm">
            <div class="w-full max-w-md rounded bg-white p-6 shadow-lg dark:bg-gray-800">
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Confirm Deletion</h2>
                <p class="mb-6 text-gray-600 dark:text-gray-300">Are you sure you want to delete this assignment?</p>
                <div class="flex justify-end space-x-4">
                    <button
                        @click="cancelDelete"
                        class="rounded bg-gray-300 px-4 py-2 text-sm text-gray-800 hover:bg-gray-400 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500"
                    >
                        Cancel
                    </button>
                    <button @click="deleteAssignment" class="rounded bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700">Confirm</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
