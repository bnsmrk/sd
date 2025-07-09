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
        <div class="space-y-6 p-6">
            <!-- Header Actions -->
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <h1 class="flex items-center gap-2 text-2xl font-bold text-[#01006c]">
                    <Layers class="h-6 w-6 text-[#01006c]" />
                    Module List
                </h1>
                <div class="flex flex-wrap items-center gap-2">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search modules..."
                        class="rounded border border-gray-300 px-3 py-2 text-sm focus:border-[#ffc60b] focus:ring-2 focus:ring-[#ffc60b] focus:outline-none"
                    />
                    <Link
                        href="/modules/create"
                        class="inline-flex items-center gap-2 rounded bg-[#01006c] px-4 py-2 text-sm font-semibold text-white shadow hover:bg-[#0d1282]"
                    >
                        <Plus class="h-4 w-4" />
                        Create Module
                    </Link>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow">
                <table class="min-w-full text-sm text-gray-700">
                    <thead class="bg-[#01006c] text-white">
                        <tr>
                            <th class="px-6 py-3 text-left">Name</th>
                            <th class="px-6 py-3 text-left">Year</th>
                            <th class="px-6 py-3 text-left">Subject</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="m in props.modules.data"
                            :key="m.id"
                            class="border-b transition hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700"
                        >
                            <td class="px-6 py-4">{{ m.name }}</td>
                            <td class="px-6 py-4">{{ m.year_level.name }}</td>
                            <td class="px-6 py-4">{{ m.subject.name }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-4">
                                    <Link
                                        :href="`/modules/${m.id}/edit`"
                                        class="inline-flex items-center gap-1 font-medium text-[#01006c] hover:text-[#ffc60b]"
                                    >
                                        <Pencil class="h-4 w-4" /> Edit
                                    </Link>
                                    <button
                                        @click="confirmDelete(m.id)"
                                        class="inline-flex items-center gap-1 font-medium text-red-600 hover:underline"
                                    >
                                        <Trash2 class="h-4 w-4" /> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex flex-wrap justify-center gap-2">
                <template v-for="(link, i) in props.modules.links" :key="i">
                    <span
                        v-if="!link.url"
                        class="inline-flex items-center justify-center rounded bg-gray-200 px-3 py-1 text-sm text-gray-500"
                        v-html="link.label"
                    />
                    <Link
                        v-else
                        :href="link.url"
                        class="inline-flex items-center justify-center rounded px-3 py-1 text-sm font-medium transition"
                        :class="{
                            'bg-[#01006c] text-white hover:bg-[#0d1282]': link.active,
                            'border border-gray-300 bg-white text-gray-700 hover:bg-gray-100': !link.active,
                        }"
                    >
                        <span v-html="link.label" />
                    </Link>
                </template>
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
            <div class="w-full max-w-md rounded bg-white p-6 shadow-lg dark:bg-gray-800">
                <h2 class="mb-4 text-lg font-semibold text-[#01006c]">Confirm Deletion</h2>
                <p class="mb-6 text-sm text-gray-600 dark:text-gray-300">Are you sure you want to delete this module?</p>
                <div class="flex justify-end space-x-3">
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
