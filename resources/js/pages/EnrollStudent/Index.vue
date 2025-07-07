<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { Pencil, Plus, Trash2, X } from 'lucide-vue-next';
import { ref, watch } from 'vue';

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
        router.delete(`/enroll/${deleteId.value}`);
        showDeleteModal.value = false;
        deleteId.value = null;
    }
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    deleteId.value = null;
};
</script>

<template>
    <Head title="Enrollments" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800">📋 Enrollments</h2>
                <div class="flex items-center gap-2">
                    <input v-model="search" type="text" placeholder="Search student..." class="rounded border px-3 py-2 text-sm shadow-sm" />
                    <Link href="/enroll/create" class="inline-flex items-center gap-2 rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                        <Plus class="h-4 w-4" /> Add Enrollment
                    </Link>
                </div>
            </div>

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="min-w-full text-left text-sm text-gray-700">
                    <thead class="bg-gray-50 text-xs text-gray-600 uppercase">
                        <tr>
                            <th class="px-6 py-3">Student</th>
                            <th class="px-6 py-3">Year Level</th>
                            <th class="px-6 py-3">Section</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="enroll in enrollments.data" :key="enroll.id" class="border-b bg-white hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium">{{ enroll.user.name }}</td>
                            <td class="px-6 py-4">{{ enroll.year_level.name }}</td>
                            <td class="px-6 py-4">{{ enroll.section?.name ?? '—' }}</td>
                            <td class="space-x-3 px-6 py-4 text-center">
                                <Link :href="`/enroll/${enroll.id}/edit`" class="inline-flex items-center gap-1 text-blue-600 hover:underline">
                                    <Pencil class="h-4 w-4" /> Edit
                                </Link>
                                <button @click="confirmDelete(enroll.id)" class="inline-flex items-center gap-1 text-red-600 hover:underline">
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
