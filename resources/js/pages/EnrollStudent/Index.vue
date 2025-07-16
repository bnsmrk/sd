<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { Pencil, Plus, Trash2, X } from 'lucide-vue-next';
import { ref, watch } from 'vue';

import { computed } from 'vue';

const isLoading = computed(() => isCreating.value || isUpdating.value || isDeleting.value);
const isCreating = ref(false);
const isUpdating = ref(false);
const isDeleting = ref(false);

const props = defineProps<{
    enrollments: {
        data: Array<{
            id: number;
            user: { name: string };
            year_level: { name: string };
            section: { name: string } | null;
        }>;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
    };
    filters: {
        search?: string;
    };
}>();

const breadcrumbs = [{ title: 'Enrollments', href: '/enroll' }];
const showDeleteModal = ref(false);
const deleteId = ref<number | null>(null);

const search = ref(props.filters.search || '');
watch(
    search,
    debounce((value) => {
        router.get('/enroll', { search: value }, { preserveState: true, replace: true });
    }, 300),
);

const confirmDelete = (id: number) => {
    deleteId.value = id;
    showDeleteModal.value = true;
};
const destroyItem = () => {
    if (deleteId.value !== null) {
        isDeleting.value = true;

        router.delete(`/enroll/${deleteId.value}`, {
            onSuccess: () => {
                showDeleteModal.value = false;
                deleteId.value = null;
            },
            onFinish: () => {
                setTimeout(() => {
                    isDeleting.value = false;
                }, 800);
            },
        });
    }
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    deleteId.value = null;
};

type SortableEnrollKey = 'user' | 'year_level' | 'section';

const sortKey = ref<SortableEnrollKey>('user');
const sortAsc = ref(true);

const toggleSort = (key: SortableEnrollKey) => {
    if (sortKey.value === key) {
        sortAsc.value = !sortAsc.value;
    } else {
        sortKey.value = key;
        sortAsc.value = true;
    }
};

const sortedEnrollments = computed(() => {
    return [...props.enrollments.data].sort((a, b) => {
        let aValue = '';
        let bValue = '';

        if (sortKey.value === 'user') {
            aValue = a.user.name.toLowerCase();
            bValue = b.user.name.toLowerCase();
        } else if (sortKey.value === 'year_level') {
            aValue = a.year_level.name.toLowerCase();
            bValue = b.year_level.name.toLowerCase();
        } else if (sortKey.value === 'section') {
            aValue = a.section?.name?.toLowerCase?.() || '';
            bValue = b.section?.name?.toLowerCase?.() || '';
        }

        if (aValue < bValue) return sortAsc.value ? -1 : 1;
        if (aValue > bValue) return sortAsc.value ? 1 : -1;
        return 0;
    });
});
</script>

<template>
    <Head title="Enrollments" />
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
        <div class="p-4">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-[#01006c]">📋 Enrollments</h2>
                <div class="flex items-center gap-2">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search student..."
                        class="rounded border border-[#01006c] px-3 py-2 text-sm shadow-sm focus:border-[#ffc60b] focus:outline-none"
                    />
                    <Link
                        href="/enroll/create"
                        class="inline-flex items-center gap-2 rounded bg-[#01006c] px-4 py-2 text-white transition hover:bg-[#0d1282]"
                    >
                        <Plus class="h-4 w-4" /> Add Enrollment
                    </Link>
                </div>
            </div>

            <div class="relative overflow-x-auto rounded-lg border border-[#01006c] shadow">
                <table class="min-w-full text-left text-sm text-[#01006c]">
                    <thead class="bg-[#01006c] text-xs text-white uppercase">
                        <tr>
                            <th @click="toggleSort('user')" class="cursor-pointer px-6 py-3">
                                Student
                                <span v-if="sortKey === 'user'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th @click="toggleSort('year_level')" class="cursor-pointer px-6 py-3">
                                Year Level
                                <span v-if="sortKey === 'year_level'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th @click="toggleSort('section')" class="cursor-pointer px-6 py-3">
                                Section
                                <span v-if="sortKey === 'section'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#01006c] bg-white">
                        <tr v-for="enroll in sortedEnrollments" :key="enroll.id" class="transition hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium">{{ enroll.user.name }}</td>
                            <td class="px-6 py-4">{{ enroll.year_level.name }}</td>
                            <td class="px-6 py-4">{{ enroll.section?.name ?? '—' }}</td>
                            <td class="space-x-3 px-6 py-4 text-center">
                                <Link
                                    :href="`/enroll/${enroll.id}/edit`"
                                    class="inline-flex items-center gap-1 text-[#01006c] transition hover:text-[#ff69b4]"
                                >
                                    <Pencil class="h-4 w-4" /> Edit
                                </Link>
                                <button
                                    @click="confirmDelete(enroll.id)"
                                    class="inline-flex items-center gap-1 text-red-600 transition hover:text-red-800"
                                >
                                    <Trash2 class="h-4 w-4" /> Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-center gap-2">
                <template v-for="(link, i) in enrollments.links" :key="i">
                    <span v-if="!link.url" class="px-3 py-1 text-sm text-gray-400" v-html="link.label" />
                    <Link
                        v-else
                        :href="link.url"
                        class="inline-flex items-center justify-center rounded-md border px-3 py-1 text-sm transition"
                        :class="{
                            'border-blue-600 bg-blue-600 text-white': link.active,
                            'border-gray-300 text-gray-700 hover:bg-gray-100': !link.active,
                        }"
                    >
                        <span v-html="link.label" />
                    </Link>
                </template>
            </div>
        </div>

        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-white/30 backdrop-blur-sm">
            <div class="w-full max-w-md rounded bg-white p-6 shadow-lg dark:bg-gray-800">
                <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">🗑️ Confirm Deletion</h2>
                <p class="mb-6 text-gray-600 dark:text-gray-300">Are you sure you want to delete this enrollment?</p>
                <div class="flex justify-end space-x-4">
                    <button
                        @click="cancelDelete"
                        class="inline-flex items-center gap-1 rounded bg-gray-300 px-4 py-2 text-sm text-gray-800 hover:bg-gray-400 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500"
                    >
                        <X class="h-4 w-4" /> Cancel
                    </button>
                    <button
                        @click="destroyItem"
                        class="inline-flex items-center gap-1 rounded bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700"
                    >
                        <Trash2 class="h-4 w-4" /> Confirm
                    </button>
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
