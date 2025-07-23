<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { Pencil, Plus, Trash2, X } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    assignments: {
        id: number;
        user: { id: number; name: string };
        year_level: { id: number; name: string };
    }[];
    filters: { search?: string };
}>();

const search = ref(props.filters.search || '');
const showDeleteModal = ref(false);
const deleteId = ref<number | null>(null);
const isDeleting = ref(false);

watch(
    search,
    debounce((value) => {
        router.get('/head-assignments', { search: value }, { preserveState: true, replace: true });
    }, 300),
);

const openDeleteModal = (id: number) => {
    deleteId.value = id;
    showDeleteModal.value = true;
};

const deleteAssignment = () => {
    if (deleteId.value !== null) {
        isDeleting.value = true;
        router.delete(`/head-assignments/year-level/${deleteId.value}`, {
            onSuccess: () => {
                deleteId.value = null;
                showDeleteModal.value = false;
            },
            onFinish: () => {
                setTimeout(() => {
                    isDeleting.value = false;
                }, 1000);
            },
        });
    }
};
</script>

<template>
    <AppLayout>
        <Head title="Head Assignments" />

        <div class="p-6">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <h1 class="text-xl font-bold text-pink-400">Head Assignments</h1>
                <div class="flex flex-wrap items-center gap-2">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search head..."
                        class="rounded border border-pink-300 px-3 py-2 text-sm text-pink-400 shadow-sm focus:border-pink-500 focus:outline-none"
                    />
                    <Link
                        href="/head-assignments/create"
                        class="inline-flex items-center gap-2 rounded bg-pink-400 px-4 py-2 text-white hover:bg-pink-500"
                    >
                        <Plus class="h-4 w-4" /> Assign Head
                    </Link>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-pink-200 shadow-md">
                <table class="min-w-full divide-y divide-pink-100">
                    <thead class="bg-pink-100 text-xs font-semibold text-pink-400">
                        <tr>
                            <th class="px-6 py-3 text-left">Head</th>
                            <th class="px-6 py-3 text-left">Year Level</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-pink-100 bg-white text-sm">
                        <tr v-for="a in props.assignments" :key="a.id" class="transition hover:bg-pink-50">
                            <td class="px-6 py-4 text-[#01006c]">{{ a.user.name }}</td>
                            <td class="px-6 py-4 text-[#01006c]">{{ a.year_level.name }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <Link :href="`/head-assignments/${a.year_level.id}/edit`" class="text-blue-500 hover:text-blue-600">
                                        <Pencil class="h-4 w-4" />
                                    </Link>
                                    <button @click="openDeleteModal(a.year_level.id)" class="text-red-500 hover:text-red-600">
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-2xl border-2 border-pink-300 bg-white p-6 shadow-xl">
                <h3 class="mb-4 text-xl font-bold text-pink-600">Confirm Deletion</h3>
                <p class="mb-6 text-sm text-gray-600">Are you sure you want to delete this head assignment?</p>
                <div class="flex justify-end space-x-2">
                    <button
                        @click="showDeleteModal = false"
                        class="inline-flex items-center gap-1 rounded bg-yellow-400 px-4 py-2 text-sm font-semibold text-[#01006c] hover:brightness-110"
                    >
                        <X class="h-4 w-4" /> Cancel
                    </button>
                    <button
                        @click="deleteAssignment"
                        class="inline-flex items-center gap-1 rounded bg-pink-500 px-4 py-2 text-sm font-semibold text-white hover:bg-pink-600"
                    >
                        <Trash2 class="h-4 w-4" /> Delete
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
