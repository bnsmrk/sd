<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Plus, Pencil, Trash2, Save, Check, X } from 'lucide-vue-next';
defineProps<{
    enrollments: Array<{
        id: number;
        user: { name: string };
        year_level: { name: string };
        section: { name: string } | null;
    }>;
}>();

const breadcrumbs = [{ title: 'Enrollments', href: '/enroll' }];
const showDeleteModal = ref(false);
const deleteId = ref<number | null>(null);

const confirmDelete = (id: number) => {
    deleteId.value = id;
    showDeleteModal.value = true;
};

const destroyItem = () => {
    if (deleteId.value !== null) {
        router.delete(`/enroll/${deleteId.value}`);
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
    <Head title="Enrollments" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <!-- Header -->
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800">📋 Enrollments</h2>
                <Link href="/enroll/create" class="inline-flex items-center gap-2 rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    <Plus class="w-4 h-4" /> Add Enrollment
                </Link>
            </div>

            <!-- Table -->
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="min-w-full text-left text-sm text-gray-700">
                    <thead class="bg-gray-50 text-xs text-gray-600 uppercase">
                        <tr>
                            <th class="px-6 py-3">Student</th>
                            <th class="px-6 py-3">Year Level</th>
                            <th class="px-6 py-3">Section</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="enroll in enrollments" :key="enroll.id" class="border-b bg-white hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium">{{ enroll.user.name }}</td>
                            <td class="px-6 py-4">{{ enroll.year_level.name }}</td>
                            <td class="px-6 py-4">{{ enroll.section?.name ?? '—' }}</td>
                            <td class="px-6 py-4 text-center space-x-3">
                                <Link
                                    :href="`/enroll/${enroll.id}/edit`"
                                    class="inline-flex items-center gap-1 text-blue-600 hover:underline"
                                >
                                    <Pencil class="w-4 h-4" /> Edit
                                </Link>
                                <button
                                    @click="confirmDelete(enroll.id)"
                                    class="inline-flex items-center gap-1 text-red-600 hover:underline"
                                >
                                    <Trash2 class="w-4 h-4" /> Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-white/30 backdrop-blur-sm">
            <div class="w-full max-w-md rounded bg-white p-6 shadow-lg dark:bg-gray-800">
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">🗑️ Confirm Deletion</h2>
                <p class="mb-6 text-gray-600 dark:text-gray-300">Are you sure you want to delete this enrollment?</p>
                <div class="flex justify-end space-x-4">
                    <button
                        @click="cancelDelete"
                        class="inline-flex items-center gap-1 rounded bg-gray-300 px-4 py-2 text-sm text-gray-800 hover:bg-gray-400 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500"
                    >
                        <X class="w-4 h-4" /> Cancel
                    </button>
                    <button
                        @click="destroyItem"
                        class="inline-flex items-center gap-1 rounded bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700"
                    >
                        <Trash2 class="w-4 h-4" /> Confirm
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
