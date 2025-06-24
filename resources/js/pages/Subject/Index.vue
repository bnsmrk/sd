<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue'; // ✅ required

defineProps<{
    subjects: Array<{
        id: number;
        name: string;
        year_level?: { name: string } | null;
        section?: { name: string } | null;
    }>;
}>();

const showDeleteModal = ref(false);
const deleteId = ref<number | null>(null);

const confirmDelete = (id: number) => {
    deleteId.value = id;
    showDeleteModal.value = true;
};

const destroyItem = () => {
    if (deleteId.value !== null) {
        router.delete(`/subjects/${deleteId.value}`);
        showDeleteModal.value = false;
        deleteId.value = null;
    }
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    deleteId.value = null;
};
</script>

<template>
    <Head title="Subjects" />
    <AppLayout>
        <div class="p-4">
            <div class="mb-4 flex justify-between">
                <h2 class="text-xl font-bold">Subjects</h2>
                <Link href="/subjects/create" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Add Subject</Link>
            </div>

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="min-w-full text-left text-sm text-gray-700">
                    <thead class="bg-gray-100 text-xs text-gray-600 uppercase">
                        <tr>
                            <th scope="col" class="px-6 py-3">Name</th>
                            <th scope="col" class="px-6 py-3">Year Level</th>
                            <!-- <th scope="col" class="px-6 py-3">Section</th> -->
                            <th scope="col" class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="subject in subjects" :key="subject.id" class="border-b bg-white hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ subject.name }}</td>
                            <td class="px-6 py-4">{{ subject.year_level?.name ?? '—' }}</td>
                            <!-- <td class="px-6 py-4">{{ subject.section?.name ?? '—' }}</td> -->
                            <td class="space-x-3 px-6 py-4 text-center">
                                <Link :href="`/subjects/${subject.id}/edit`" class="text-blue-600 hover:underline">Edit</Link>
                                <button @click="confirmDelete(subject.id)" class="text-red-600 hover:underline">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="subjects.length === 0">
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No subjects found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Blurred Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-white/30 backdrop-blur-sm">
            <div class="w-full max-w-md rounded bg-white p-6 shadow-lg dark:bg-gray-800">
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Confirm Deletion</h2>
                <p class="mb-6 text-gray-600 dark:text-gray-300">Are you sure you want to delete this subject?</p>
                <div class="flex justify-end space-x-4">
                    <button
                        @click="cancelDelete"
                        class="rounded bg-gray-300 px-4 py-2 text-sm text-gray-800 hover:bg-gray-400 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500"
                    >
                        Cancel
                    </button>
                    <button @click="destroyItem" class="rounded bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700">Confirm</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
