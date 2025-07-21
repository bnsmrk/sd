<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

import { BookOpen, Pencil, PlusCircle, Trash2, XCircle } from 'lucide-vue-next';

import { computed } from 'vue';

const isLoading = computed(() => isCreating.value || isUpdating.value || isDeleting.value);
const isCreating = ref(false);
const isUpdating = ref(false);
const isDeleting = ref(false);

const props = defineProps<{
    tests: Array<{
        id: number;
        title: string;
        type: string;
        scheduled_at: string;
        due_date: string;
        year_level: { name: string };
    }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Proficiency Test', href: '/proficiency-test' }];

const showDeleteModal = ref(false);
const deleteId = ref<number | null>(null);

function confirmDelete(id: number) {
    deleteId.value = id;
    showDeleteModal.value = true;
}
function destroyTest() {
    if (deleteId.value !== null) {
        isDeleting.value = true;

        router.delete(`/proficiency-test/${deleteId.value}`, {
            onFinish: () => {
                setTimeout(() => {
                    isDeleting.value = false;
                }, 800);
            },
        });

        showDeleteModal.value = false;
        deleteId.value = null;
    }
}

function cancelDelete() {
    showDeleteModal.value = false;
    deleteId.value = null;
}

const sortBy = ref<'title' | 'type' | 'scheduled_at' | 'due_date' | 'year_level'>('title');
const sortAsc = ref(true);

function toggleSort(key: typeof sortBy.value) {
    if (sortBy.value === key) {
        sortAsc.value = !sortAsc.value;
    } else {
        sortBy.value = key;
        sortAsc.value = true;
    }
}

const sortedTests = computed(() => {
    return [...props.tests].sort((a, b) => {
        let aVal: any = a[sortBy.value];
        let bVal: any = b[sortBy.value];

        if (sortBy.value === 'year_level') {
            aVal = a.year_level.name.toLowerCase();
            bVal = b.year_level.name.toLowerCase();
        }

        if (typeof aVal === 'string') aVal = aVal.toLowerCase();
        if (typeof bVal === 'string') bVal = bVal.toLowerCase();

        if (aVal < bVal) return sortAsc.value ? -1 : 1;
        if (aVal > bVal) return sortAsc.value ? 1 : -1;
        return 0;
    });
});
</script>

<template>
    <Head title="Proficiency Test" />

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
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-pink-500">Proficiency Tests</h1>
                <Link
                    href="/proficiency-test/create"
                    class="inline-flex items-center gap-2 rounded bg-pink-500 px-4 py-2 text-white transition hover:bg-pink-600"
                >
                    <PlusCircle class="h-5 w-5" />
                    Create Test
                </Link>
            </div>

            <div class="overflow-x-auto rounded-lg border border-pink-200 shadow">
                <table class="min-w-full table-auto text-left text-sm text-pink-900">
                    <thead class="bg-pink-100 text-xs font-semibold text-pink-700 uppercase">
                        <tr>
                            <th class="cursor-pointer px-4 py-2 text-left font-semibold" @click="toggleSort('title')">
                                Title <span v-if="sortBy === 'title'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th class="cursor-pointer px-4 py-2 text-left font-semibold" @click="toggleSort('type')">
                                Type <span v-if="sortBy === 'type'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th class="cursor-pointer px-4 py-2 text-left font-semibold" @click="toggleSort('scheduled_at')">
                                Scheduled At <span v-if="sortBy === 'scheduled_at'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th class="cursor-pointer px-4 py-2 text-left font-semibold" @click="toggleSort('due_date')">
                                Due Date <span v-if="sortBy === 'due_date'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th class="cursor-pointer px-4 py-2 text-left font-semibold" @click="toggleSort('year_level')">
                                Year Level <span v-if="sortBy === 'year_level'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th class="px-4 py-2 text-left font-semibold">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-pink-100 bg-white">
                        <tr v-for="test in sortedTests" :key="test.id" class="hover:bg-pink-50">
                            <td class="flex items-center gap-2 px-4 py-2">
                                <BookOpen class="h-4 w-4 text-pink-500" />
                                {{ test.title }}
                            </td>
                            <td class="px-4 py-2 capitalize">{{ test.type }}</td>
                            <td class="px-4 py-2">{{ new Date(test.scheduled_at).toLocaleString() }}</td>
                            <td class="px-4 py-2">{{ new Date(test.due_date).toLocaleString() }}</td>
                            <td class="px-4 py-2">{{ test.year_level.name }}</td>
                            <td class="space-x-2 px-4 py-2">
                                <Link
                                    :href="`/proficiency-test/${test.id}/edit`"
                                    class="inline-flex items-center gap-1 rounded bg-pink-100 px-3 py-1 text-sm font-medium text-pink-700 hover:bg-pink-200"
                                >
                                    <Pencil class="h-4 w-4" /> Edit
                                </Link>
                                <Link
                                    :href="`/proficiency-test/${test.id}/questions/create`"
                                    class="inline-flex items-center gap-1 rounded bg-pink-100 px-3 py-1 text-sm font-medium text-pink-700 hover:bg-pink-200"
                                >
                                    <PlusCircle class="h-4 w-4" /> Add Questions
                                </Link>
                                <button
                                    @click="confirmDelete(test.id)"
                                    class="inline-flex items-center gap-1 rounded bg-red-100 px-3 py-1 text-sm font-medium text-red-600 hover:bg-red-200"
                                >
                                    <Trash2 class="h-4 w-4" /> Delete
                                </button>
                            </td>
                        </tr>
                        <tr v-if="props.tests.length === 0">
                            <td colspan="6" class="px-4 py-4 text-center text-gray-500">No proficiency tests found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-white/30 backdrop-blur-sm">
            <div class="w-full max-w-md rounded bg-white p-6 shadow-lg">
                <h2 class="mb-4 text-lg font-semibold text-gray-900">Confirm Deletion</h2>
                <p class="mb-6 text-gray-600">Are you sure you want to delete this test?</p>
                <div class="flex justify-end space-x-4">
                    <button
                        @click="cancelDelete"
                        class="inline-flex items-center gap-2 rounded bg-gray-300 px-4 py-2 text-sm text-gray-800 hover:bg-gray-400"
                    >
                        <XCircle class="h-4 w-4 text-gray-700" />
                        Cancel
                    </button>

                    <button
                        @click="destroyTest"
                        class="inline-flex items-center gap-2 rounded bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700"
                    >
                        <Trash2 class="h-4 w-4 text-white" />
                        Confirm
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
