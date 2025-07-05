<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    activities: {
        id: number;
        title: string;
        type: string;
        scheduled_at: string;
        due_date: string;
        year_level: string;
        section: string;
        subject: string;
    }[];
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Activities', href: '/activities' }];

const showDeleteModal = ref(false);
const deleteId = ref<number | null>(null);

function confirmDelete(id: number) {
    deleteId.value = id;
    showDeleteModal.value = true;
}

function deleteActivity() {
    if (deleteId.value !== null) {
        router.delete(`/activities/${deleteId.value}`);
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
    <Head title="Activities" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold">Activity List</h1>
                <Link href="/activities/create" class="rounded bg-blue-600 px-4 py-2 text-white">Create Activity</Link>
            </div>

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="min-w-full text-left text-sm text-gray-500 rtl:text-right dark:text-gray-400">
                    <thead class="bg-gray-50 text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Title</th>
                            <th class="px-6 py-3">Type</th>
                            <th class="px-6 py-3">scheduled_at</th>
                            <th class="px-6 py-3">due date</th>
                            <th class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="a in props.activities"
                            :key="a.id"
                            class="border-b border-gray-200 bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600"
                        >
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ a.title }}</td>
                            <td class="px-6 py-4 capitalize">{{ a.type }}</td>
                            <td class="px-6 py-4">{{ a.scheduled_at }}</td>
                            <td class="px-6 py-4">{{ a.due_date }}</td>


                            <td class="flex items-center space-x-3 px-6 py-4">
                                <Link :href="`/activities/${a.id}/edit`" class="font-medium text-blue-600 hover:underline dark:text-blue-500">
                                    Edit
                                </Link>

                                <button @click="confirmDelete(a.id)" class="font-medium text-red-600 hover:underline dark:text-red-500">
                                    Delete
                                </button>

                                <!-- Conditional Link -->
                                <template v-if="a.type === 'essay'">
                                    <Link
                                        :href="`/activities/${a.id}/essay-submissions`"
                                        class="font-medium text-purple-600 hover:underline dark:text-purple-400"
                                    >
                                        View Essay
                                    </Link>
                                </template>
                                <template v-else>
                                    <Link
                                        :href="`/activities/${a.id}/questions/create`"
                                        class="font-medium text-indigo-600 hover:underline dark:text-indigo-500"
                                    >
                                        Add Question
                                    </Link>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-white/30 backdrop-blur-sm">
            <div class="w-full max-w-md rounded bg-white p-6 shadow-lg dark:bg-gray-800">
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Confirm Deletion</h2>
                <p class="mb-6 text-gray-600 dark:text-gray-300">Are you sure you want to delete this activity?</p>
                <div class="flex justify-end space-x-4">
                    <button
                        @click="cancelDelete"
                        class="rounded bg-gray-300 px-4 py-2 text-sm text-gray-800 hover:bg-gray-400 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500"
                    >
                        Cancel
                    </button>
                    <button @click="deleteActivity" class="rounded bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700">Confirm</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
