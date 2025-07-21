<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

import debounce from 'lodash/debounce';
import { Layers, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';

import { computed } from 'vue';

const isLoading = computed(() => isCreating.value || isUpdating.value || isDeleting.value);
const isCreating = ref(false);
const isUpdating = ref(false);
const isDeleting = ref(false);

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
        isDeleting.value = true;
        router.delete(`/modules/${deleteId.value}`, {
            onSuccess: () => {
                showDeleteModal.value = false;
                deleteId.value = null;
            },
            onFinish: () => {
                setTimeout(() => {
                    isDeleting.value = false;
                }, 2000);
            },
        });
    }
}

function cancelDelete() {
    showDeleteModal.value = false;
    deleteId.value = null;
}

type SortKey = 'name' | 'year' | 'subject';
const sortKey = ref<SortKey>('name');
const sortAsc = ref(true);

const toggleSort = (key: SortKey) => {
    if (sortKey.value === key) {
        sortAsc.value = !sortAsc.value;
    } else {
        sortKey.value = key;
        sortAsc.value = true;
    }
};

const sortedModules = computed(() => {
    return [...props.modules.data].sort((a, b) => {
        let aVal = '';
        let bVal = '';

        if (sortKey.value === 'name') {
            aVal = a.name.toLowerCase();
            bVal = b.name.toLowerCase();
        } else if (sortKey.value === 'year') {
            aVal = a.year_level.name.toLowerCase();
            bVal = b.year_level.name.toLowerCase();
        } else if (sortKey.value === 'subject') {
            aVal = a.subject.name.toLowerCase();
            bVal = b.subject.name.toLowerCase();
        }

        if (aVal < bVal) return sortAsc.value ? -1 : 1;
        if (aVal > bVal) return sortAsc.value ? 1 : -1;
        return 0;
    });
});
</script>

<template>
    <Head title="Modules" />
    <AppLayout>
        <div v-if="isLoading" class="fixed inset-0 z-50 flex items-center justify-center bg-white/30 backdrop-blur-sm">
            <div class="flex flex-col items-center gap-4">
                <div class="relative h-16 w-16">
                    <div class="animate-spin-slow-cw absolute inset-0 rounded-full border-4 border-blue-600 border-t-transparent"></div>

                    <div class="animate-spin-slow-ccw absolute inset-2 rounded-full border-4 border-yellow-400 border-t-transparent"></div>

                    <div class="animate-spin-fast-cw absolute inset-4 rounded-full border-4 border-pink-500 border-t-transparent"></div>
                </div>

                <div class="text-center">
                    <span class="block animate-pulse text-base font-semibold text-[#01006c]">Processing Request...</span>
                    <span class="text-xs text-[#01006c]/70">This may take a moment</span>
                </div>
            </div>
        </div>
        <div class="space-y-6 p-6">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <h1 class="flex items-center gap-2 text-2xl font-bold text-pink-400">
                    <Layers class="h-6 w-6 text-pink-400" />
                    Module List
                </h1>
                <div class="flex flex-wrap items-center gap-2">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search modules..."
                        class="rounded border border-pink-300 px-3 py-2 text-sm text-pink-800 shadow-sm focus:border-yellow-400 focus:outline-none"
                    />
                    <Link
                        href="/modules/create"
                        class="inline-flex items-center gap-2 rounded bg-pink-400 px-4 py-2 text-white transition hover:bg-pink-400"
                    >
                        <Plus class="h-4 w-4" />
                        Create Module
                    </Link>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-pink-200 shadow">
                <table class="min-w-full table-auto text-left text-sm text-pink-500">
                    <thead class="bg-pink-100 text-xs font-semibold text-pink-500 uppercase">
                        <tr>
                            <th @click="toggleSort('name')" class="cursor-pointer px-6 py-3 text-left">
                                Name <span v-if="sortKey === 'name'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th @click="toggleSort('year')" class="cursor-pointer px-6 py-3 text-left">
                                Year <span v-if="sortKey === 'year'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th @click="toggleSort('subject')" class="cursor-pointer px-6 py-3 text-left">
                                Subject <span v-if="sortKey === 'subject'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-pink-100 bg-white">
                        <tr v-for="m in sortedModules" :key="m.id" class="hover:bg-pink-50">
                            <td class="px-6 py-4 text-[#01006c]">{{ m.name }}</td>
                            <td class="px-6 py-4 text-[#01006c]">{{ m.year_level.name }}</td>
                            <td class="px-6 py-4 text-[#01006c]">{{ m.subject.name }}</td>
                            <td class="px-6 py-4 text-center text-[#01006c]">
                                <div class="flex justify-center gap-4">
                                    <Link :href="`/modules/${m.id}/edit`" class="text-blue-500 hover:text-blue-800">
                                        <Pencil class="h-4 w-4" />
                                    </Link>
                                    <button @click="confirmDelete(m.id)" class="text-red-600 hover:text-red-800">
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

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
                            'border-pink-500 bg-pink-500 text-white': link.active,
                            'border-pink-200 text-pink-800 hover:bg-pink-100': !link.active,
                        }"
                    >
                        <span v-html="link.label" />
                    </Link>
                </template>
            </div>
        </div>

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

<style scoped>
@keyframes spin-cw {
    to {
        transform: rotate(360deg);
    }
}

@keyframes spin-ccw {
    to {
        transform: rotate(-360deg);
    }
}

.animate-spin-slow-cw {
    animation: spin-cw 2s linear infinite;
}

.animate-spin-slow-ccw {
    animation: spin-ccw 3s linear infinite;
}

.animate-spin-fast-cw {
    animation: spin-cw 1s linear infinite;
}
</style>
