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
    assignments: {
        data: {
            id: number;
            teacher: { id: number; name: string };
            year_level: { name: string };
            subject: { name: string };
        }[];
        links: {
            url: string | null;
            label: string;
            active: boolean;
        }[];
    };
    filters: { search?: string };
}>();

const search = ref(props.filters.search || '');
watch(
    search,
    debounce((value) => {
        router.get('/teacher-assignments', { search: value }, { preserveState: true, replace: true });
    }, 300),
);

const showDeleteModal = ref(false);
const deleteId = ref<number | null>(null);

function confirmDelete(id: number) {
    deleteId.value = id;
    showDeleteModal.value = true;
}
function deleteAssignment() {
    if (deleteId.value !== null) {
        isDeleting.value = true;

        router.delete(`/teacher-assignments/${deleteId.value}`, {
            onSuccess: () => {
                showDeleteModal.value = false;
                deleteId.value = null;
            },
            onFinish: () => {
                setTimeout(() => {
                    isDeleting.value = false;
                }, 1000);
            },
        });
    }
}

function cancelDelete() {
    showDeleteModal.value = false;
    deleteId.value = null;
}

type SortableAssignmentKey = 'teacher' | 'year_level';

const sortKey = ref<SortableAssignmentKey>('teacher');
const sortAsc = ref(true);

const toggleSort = (key: SortableAssignmentKey) => {
    if (sortKey.value === key) {
        sortAsc.value = !sortAsc.value;
    } else {
        sortKey.value = key;
        sortAsc.value = true;
    }
};

const sortedAssignments = computed(() => {
    return [...props.assignments.data].sort((a, b) => {
        let aVal = '';
        let bVal = '';

        if (sortKey.value === 'teacher') {
            aVal = a.teacher.name.toLowerCase();
            bVal = b.teacher.name.toLowerCase();
        } else if (sortKey.value === 'year_level') {
            aVal = a.year_level.name.toLowerCase();
            bVal = b.year_level.name.toLowerCase();
        }

        if (aVal < bVal) return sortAsc.value ? -1 : 1;
        if (aVal > bVal) return sortAsc.value ? 1 : -1;
        return 0;
    });
});
</script>

<template>
    <Head title="Teacher Assignments" />
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

        <div class="p-6">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <h1 class="text-xl font-bold text-pink-400">Teacher Assignments</h1>
                <div class="flex items-center gap-2">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search teacher name..."
                        class="rounded border border-pink-300 px-3 py-2 text-sm text-pink-400 shadow-sm focus:border-yellow-400 focus:outline-none"
                    />
                    <Link
                        href="/teacher-assignments/create"
                        class="inline-flex items-center gap-2 rounded bg-pink-400 px-4 py-2 text-white transition hover:bg-pink-500"
                    >
                        <Plus class="h-4 w-4" /> Assign Teacher
                    </Link>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-pink-200 shadow">
                <table class="min-w-full table-auto text-left text-sm text-pink-400">
                    <thead class="bg-pink-100 text-xs font-semibold text-pink-400 uppercase">
                        <tr>
                            <th @click="toggleSort('teacher')" class="cursor-pointer px-4 py-3 text-left">
                                Teacher
                                <span v-if="sortKey === 'teacher'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th @click="toggleSort('year_level')" class="cursor-pointer px-4 py-3 text-left">
                                Year Level
                                <span v-if="sortKey === 'year_level'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th class="px-4 py-3 text-left">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-pink-100 bg-white">
                        <tr v-for="a in sortedAssignments" :key="a.id" class="hover:bg-pink-50">
                            <td class="px-4 py-2 text-[#01006c]">{{ a.teacher.name }}</td>
                            <td class="px-4 py-2 text-[#01006c]">{{ a.year_level.name }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <Link :href="`/teacher-assignments/${a.id}/edit`" class="text-blue-600 hover:text-blue-800">
                                        <Pencil class="h-5 w-5" />
                                    </Link>
                                    <button @click="confirmDelete(a.id)" class="text-red-600 hover:text-red-800">
                                        <Trash2 class="h-5 w-5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-center gap-2">
                <template v-for="(link, i) in props.assignments.links" :key="i">
                    <span v-if="!link.url" class="px-3 py-1 text-sm text-gray-400" v-html="link.label" />
                    <Link
                        v-else
                        :href="link.url"
                        class="inline-flex items-center justify-center rounded-md border px-3 py-1 text-sm transition"
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
            <div class="w-full max-w-md rounded-2xl border-2 border-[#ff69b4] bg-white p-6 shadow-xl">
                <h2 class="mb-4 text-lg font-bold text-[#ff69b4]">🗑️ Confirm Deletion</h2>
                <p class="mb-6 text-sm text-gray-600">Are you sure you want to delete this assignment?</p>
                <div class="flex justify-end space-x-4">
                    <button
                        @click="cancelDelete"
                        class="inline-flex items-center gap-1 rounded-md bg-[#ffc60b] px-4 py-2 text-sm font-semibold text-[#01006c] hover:brightness-110"
                    >
                        <X class="h-4 w-4" /> Cancel
                    </button>
                    <button
                        @click="deleteAssignment"
                        class="inline-flex items-center gap-1 rounded-md bg-[#ff69b4] px-4 py-2 text-sm font-semibold text-white hover:bg-[#e858a1]"
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
