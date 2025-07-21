<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { FileText, Pencil, PlusCircle, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';

import { computed } from 'vue';

const isLoading = computed(() => isCreating.value || isUpdating.value || isDeleting.value);
const isCreating = ref(false);
const isUpdating = ref(false);
const isDeleting = ref(false);

const props = defineProps<{
    activities: {
        data: {
            id: number;
            title: string;
            type: string;
            scheduled_at: string;
            due_date: string;
            year_level: string;
            section: string;
            subject: string;
        }[];
        links: { url: string | null; label: string; active: boolean }[];
    };
    filters: { search?: string };
}>();

const search = ref(props.filters.search || '');

watch(
    search,
    debounce((value) => {
        router.get('/activities', { search: value }, { preserveState: true, replace: true });
    }, 300),
);

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Activities', href: '/activities' }];

const showDeleteModal = ref(false);
const deleteId = ref<number | null>(null);

function confirmDelete(id: number) {
    deleteId.value = id;
    showDeleteModal.value = true;
}
function deleteActivity() {
    if (deleteId.value !== null) {
        isDeleting.value = true;

        router.delete(`/activities/${deleteId.value}`, {
            onFinish: () => {
                setTimeout(() => {
                    isDeleting.value = false;
                }, 800);
                showDeleteModal.value = false;
                deleteId.value = null;
            },
        });
    }
}

function cancelDelete() {
    showDeleteModal.value = false;
    deleteId.value = null;
}

type SortKey = 'title' | 'type' | 'scheduled_at' | 'due_date';
const sortKey = ref<SortKey>('scheduled_at');
const sortAsc = ref(true);

const toggleSort = (key: SortKey) => {
    if (sortKey.value === key) {
        sortAsc.value = !sortAsc.value;
    } else {
        sortKey.value = key;
        sortAsc.value = true;
    }
};

const sortedActivities = computed(() => {
    return [...props.activities.data].sort((a, b) => {
        let aVal = a[sortKey.value]?.toString().toLowerCase() || '';
        let bVal = b[sortKey.value]?.toString().toLowerCase() || '';
        if (aVal < bVal) return sortAsc.value ? -1 : 1;
        if (aVal > bVal) return sortAsc.value ? 1 : -1;
        return 0;
    });
});
</script>
<template>
    <Head title="Activities" />
    <AppLayout :breadcrumbs="breadcrumbs">
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

        <div class="space-y-4 p-6">
            <div class="mb-4 flex flex-wrap justify-between gap-2 sm:items-center sm:justify-between">
                <h1 class="text-2xl font-bold text-pink-500">Activities</h1>
                <div class="flex w-full justify-end gap-2 sm:w-auto">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search activities..."
                        class="rounded border border-pink-300 px-3 py-2 text-sm text-pink-800 shadow-sm focus:border-yellow-400 focus:outline-none"
                    />
                    <Link
                        href="/activities/create"
                        class="inline-flex items-center gap-2 rounded bg-pink-500 px-4 py-2 text-white transition hover:bg-pink-600"
                    >
                        <PlusCircle class="h-4 w-4" />
                        Create Activity
                    </Link>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-pink-200 shadow">
                <table class="min-w-full table-auto text-left text-sm text-pink-500">
                    <thead class="bg-pink-100 text-xs font-semibold text-pink-500 uppercase">
                        <tr>
                            <th @click="toggleSort('title')" class="cursor-pointer px-6 py-3">
                                Title <span v-if="sortKey === 'title'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th @click="toggleSort('type')" class="cursor-pointer px-6 py-3">
                                Type <span v-if="sortKey === 'type'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th @click="toggleSort('scheduled_at')" class="cursor-pointer px-6 py-3">
                                Scheduled At <span v-if="sortKey === 'scheduled_at'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th @click="toggleSort('due_date')" class="cursor-pointer px-6 py-3">
                                Due Date <span v-if="sortKey === 'due_date'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-pink-100 bg-white">
                        <tr v-for="a in sortedActivities" :key="a.id" class="hover:bg-pink-50">
                            <td class="px-6 py-4 font-medium text-[#01006c] dark:text-white">{{ a.title }}</td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-block rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800 capitalize dark:bg-blue-800 dark:text-blue-100"
                                >
                                    {{ a.type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-[#01006c]">{{ a.scheduled_at }}</td>
                            <td class="px-6 py-4 text-[#01006c]">{{ a.due_date }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-3">
                                    <Link :href="`/activities/${a.id}/edit`" class="text-[#01006c] hover:text-[#ffc60b]">
                                        <Pencil class="inline h-4 w-4" />
                                    </Link>

                                    <button @click="confirmDelete(a.id)" class="text-red-600 hover:text-red-800">
                                        <Trash2 class="inline h-4 w-4" />
                                    </button>

                                    <template v-if="a.type === 'essay'">
                                        <Link :href="`/activities/${a.id}/essay-submissions`" class="text-purple-600 hover:text-purple-800">
                                            <FileText class="inline h-4 w-4" />
                                        </Link>
                                    </template>

                                    <template v-else>
                                        <Link :href="`/activities/${a.id}/questions/create`" class="text-pink-600 hover:text-pink-800">
                                            <PlusCircle class="inline h-4 w-4" />
                                        </Link>

                                        <Link :href="`/activities/${a.id}/essay-answers`" class="text-purple-600 hover:text-purple-800">
                                            <FileText class="inline h-4 w-4" />
                                        </Link>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-center gap-2">
                <template v-for="(link, i) in props.activities.links" :key="i">
                    <span v-if="!link.url" class="px-3 py-1 text-sm text-gray-400" v-html="link.label" />
                    <Link
                        v-else
                        :href="link.url"
                        class="rounded px-3 py-1 text-sm font-medium"
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
            <div class="w-full max-w-md rounded bg-white p-6 shadow-xl dark:bg-gray-800">
                <h2 class="mb-4 text-lg font-semibold text-[#01006c] dark:text-white">Confirm Deletion</h2>
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
