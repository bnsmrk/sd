<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

import debounce from 'lodash/debounce';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    modules: {
        data: {
            id: number;
            name: string;
            year_level: { id: number; name: string };
            subject: { id: number; name: string };
        }[];
        links: { url: string | null; label: string; active: boolean }[];
    };
    filters: { search?: string };
}>();

const search = ref(props.filters.search || '');
watch(
    search,
    debounce((value) => {
        router.get('/modules', { search: value }, { preserveState: true, replace: true });
    }, 300),
);
const showDeleteModal = ref(false);
const deleteId = ref<number | null>(null);

function confirmDelete(id: number) {
    deleteId.value = id;
    showDeleteModal.value = true;
}

function deleteModule() {
    if (deleteId.value !== null) {
        router.delete(`/modules/${deleteId.value}`);
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
    <Head title="Modules" />
    <AppLayout>
        <div class="space-y-4 p-4">
            <div class="mb-4 flex flex-wrap justify-between gap-2 sm:items-center sm:justify-end">
                <div class="flex w-full justify-end gap-2 sm:w-auto">
                    <input v-model="search" type="text" placeholder="Search modules..." class="rounded border px-3 py-2 text-sm shadow-sm" />
                    <Link href="/modules/create" class="inline-flex items-center gap-2 rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                        <Plus class="h-4 w-4" /> Create Module
                    </Link>
                </div>
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
                        <tr v-for="m in props.modules.data" :key="m.id" class="border-b bg-white hover:bg-gray-50">
                            <td class="px-6 py-4">{{ m.name }}</td>
                            <td class="px-6 py-4">{{ m.year_level.name }}</td>
                            <td class="px-6 py-4">{{ m.subject.name }}</td>
                            <td class="space-x-2 px-6 py-4 text-center">
                                <Link :href="`/modules/${m.id}/edit`" class="inline-flex items-center gap-1 text-blue-600 hover:underline">
                                    <Pencil class="h-4 w-4" /> Edit
                                </Link>
                                <button @click="confirmDelete(m.id)" class="inline-flex items-center gap-1 text-red-600 hover:underline">
                                    <Trash2 class="h-4 w-4" /> Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-6 flex justify-center gap-2">
                <template v-for="(link, i) in props.modules.links" :key="i">
                    <span v-if="!link.url" class="px-3 py-1 text-sm text-gray-400" v-html="link.label" />
                    <Link
                        v-else
                        :href="link.url"
                        class="rounded px-3 py-1 text-sm"
                        :class="{
                            'bg-blue-600 text-white': link.active,
                            'text-gray-700 hover:underline': !link.active,
                        }"
                    >
                        <span v-html="link.label" />
                    </Link>
                </template>
            </div>
        </div>

        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-white/30 backdrop-blur-sm">
            <div class="w-full max-w-md rounded bg-white p-6 shadow-lg dark:bg-gray-800">
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Confirm Deletion</h2>
                <p class="mb-6 text-gray-600 dark:text-gray-300">Are you sure you want to delete this module?</p>
                <div class="flex justify-end space-x-4">
                    <button
                        @click="cancelDelete"
                        class="rounded bg-gray-300 px-4 py-2 text-sm text-gray-800 hover:bg-gray-400 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500"
                    >
                        Cancel
                    </button>
                    <button @click="deleteModule" class="rounded bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700">Confirm</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
