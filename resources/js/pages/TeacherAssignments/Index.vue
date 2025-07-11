<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { Eye, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';

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
        router.delete(`/teacher-assignments/${deleteId.value}`);
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
    <Head title="Teacher Assignments" />
    <AppLayout>
        <div class="p-6">
            <!-- Header -->
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <h1 class="text-xl font-bold text-[#01006c]">👩‍🏫 Teacher Assignments</h1>
                <div class="flex items-center gap-2">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search teacher name..."
                        class="rounded border border-[#01006c] px-3 py-2 text-sm shadow-sm focus:border-[#ffc60b] focus:outline-none"
                    />
                    <Link
                        href="/teacher-assignments/create"
                        class="inline-flex items-center gap-2 rounded bg-[#01006c] px-4 py-2 text-white hover:bg-[#0d1282]"
                    >
                        <Plus class="h-4 w-4" /> Assign Teacher
                    </Link>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-hidden rounded border border-[#01006c] shadow">
                <table class="min-w-full divide-y divide-[#01006c]">
                    <thead class="bg-[#01006c] text-xs font-semibold text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">Teacher</th>
                            <th class="px-4 py-3 text-left">Year Level</th>
                            <th class="px-4 py-3 text-left">Subject</th>
                            <th class="px-4 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#01006c] bg-white text-sm">
                        <tr v-for="a in props.assignments.data" :key="a.id" class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-[#01006c]">{{ a.teacher.name }}</td>
                            <td class="px-4 py-2 text-[#01006c]">{{ a.year_level.name }}</td>
                            <td class="px-4 py-2 text-[#01006c]">{{ a.subject.name }}</td>
                            <td class="space-x-2 px-4 py-2">
                                <Link
                                    :href="`/teacher-assignments/${a.id}`"
                                    class="inline-flex items-center gap-1 text-green-600 hover:text-[#ff69b4]"
                                >
                                    <Eye class="h-4 w-4" /> View
                                </Link>
                                <Link
                                    :href="`/teacher-assignments/${a.id}/edit`"
                                    class="inline-flex items-center gap-1 text-[#01006c] hover:text-[#ff69b4]"
                                >
                                    <Pencil class="h-4 w-4" /> Edit
                                </Link>
                                <button @click="confirmDelete(a.id)" class="inline-flex items-center gap-1 text-red-600 hover:text-red-800">
                                    <Trash2 class="h-4 w-4" /> Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex justify-center gap-2">
                <template v-for="(link, i) in props.assignments.links" :key="i">
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

        <!-- Delete Modal -->
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
