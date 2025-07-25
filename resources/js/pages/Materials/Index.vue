<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

import debounce from 'lodash/debounce';
import { BookOpen, Eye, Pencil, Trash2, Video } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const isLoading = computed(() => isCreating.value || isUpdating.value || isDeleting.value);
const isCreating = ref(false);
const isUpdating = ref(false);
const isDeleting = ref(false);

const props = defineProps<{
    materials: {
        data: Array<{
            id: number;
            title: string;
            type: string;
            file_path: string;
            video_path?: string | null;
            video_link?: string | null;
            year_level: { name: string };
            section?: { name: string };
            subject: { name: string };
            user: { name: string };
            comments?: Array<{
                id: number;
                comment: string;
                user: { name: string };
            }>;
        }>;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: { search?: string };
}>();

const search = ref(props.filters.search || '');

watch(
    search,
    debounce((value) => {
        router.get('/materials', { search: value }, { preserveState: true, replace: true });
    }, 300),
);
const showDeleteModal = ref(false);
const deleteId = ref<number | null>(null);

function createMaterial() {
    router.get('/materials/create');
}

function editMaterial(id: number) {
    router.get(`/materials/${id}/edit`);
}

function confirmDelete(id: number) {
    deleteId.value = id;
    showDeleteModal.value = true;
}

function destroyMaterial() {
    if (deleteId.value !== null) {
        isDeleting.value = true;

        router.delete(`/materials/${deleteId.value}`, {
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
}

function cancelDelete() {
    showDeleteModal.value = false;
    deleteId.value = null;
}

type SortKey = 'title' | 'type' | 'year_level' | 'section' | 'subject';
const sortKey = ref<SortKey>('title');
const sortAsc = ref(true);

const toggleSort = (key: SortKey) => {
    if (sortKey.value === key) {
        sortAsc.value = !sortAsc.value;
    } else {
        sortKey.value = key;
        sortAsc.value = true;
    }
};

const sortedMaterials = computed(() => {
    return [...props.materials.data].sort((a, b) => {
        let aVal: any;
        let bVal: any;

        switch (sortKey.value) {
            case 'year_level':
                aVal = a.year_level?.name || '';
                bVal = b.year_level?.name || '';
                break;
            case 'section':
                aVal = a.section?.name || '';
                bVal = b.section?.name || '';
                break;
            case 'subject':
                aVal = a.subject?.name || '';
                bVal = b.subject?.name || '';
                break;
            default:
                aVal = a[sortKey.value] || '';
                bVal = b[sortKey.value] || '';
        }

        aVal = aVal.toString().toLowerCase();
        bVal = bVal.toString().toLowerCase();

        if (aVal < bVal) return sortAsc.value ? -1 : 1;
        if (aVal > bVal) return sortAsc.value ? 1 : -1;
        return 0;
    });
});
</script>

<template>
    <Head title="My Materials" />
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
            <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
                <h1 class="flex items-center gap-2 text-2xl font-bold text-pink-400">
                    <BookOpen class="h-6 w-6 text-pink-300" /> My Uploaded Materials
                </h1>

                <div class="flex items-center gap-2">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search by title..."
                        class="rounded border border-pink-300 px-3 py-2 text-sm text-pink-600 shadow-sm focus:border-yellow-400 focus:outline-none"
                    />
                    <button
                        @click="createMaterial"
                        class="inline-flex items-center gap-2 rounded bg-pink-400 px-4 py-2 text-white transition hover:bg-pink-600"
                    >
                        <BookOpen class="h-4 w-4" />
                        <span>Upload Material</span>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-pink-200 shadow">
                <table class="min-w-full table-auto text-left text-sm text-pink-400">
                    <thead class="bg-pink-100 text-xs font-semibold text-pink-400 uppercase">
                        <tr>
                            <th @click="toggleSort('title')" class="cursor-pointer p-3">
                                Title <span v-if="sortKey === 'title'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th @click="toggleSort('type')" class="cursor-pointer p-3">
                                Type <span v-if="sortKey === 'type'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th @click="toggleSort('year_level')" class="cursor-pointer p-3">
                                Year Level <span v-if="sortKey === 'year_level'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th @click="toggleSort('section')" class="cursor-pointer p-3">
                                Section <span v-if="sortKey === 'section'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th @click="toggleSort('subject')" class="cursor-pointer p-3">
                                Subject <span v-if="sortKey === 'subject'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th class="p-3">File</th>
                            <th class="p-3 text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-pink-100 bg-white">
                        <template v-for="material in sortedMaterials" :key="material.id">
                            <tr class="hover:bg-pink-50">
                                <td class="p-3 align-top text-[#01006c]">{{ material.title }}</td>
                                <td class="p-3 align-top text-[#01006c] capitalize">{{ material.type.replace('_', ' ') }}</td>
                                <td class="p-3 align-top text-[#01006c]">{{ material.year_level.name }}</td>
                                <td class="p-3 align-top text-[#01006c]">{{ material.section?.name || '—' }}</td>
                                <td class="p-3 align-top text-[#01006c]">{{ material.subject.name }}</td>
                                <td class="flex items-center gap-3 p-3 align-top">
                                    <a :href="`/storage/${material.file_path}`" target="_blank" class="text-green-600 hover:text-green-800">
                                        <Eye class="h-4 w-4" />
                                    </a>
                                    <template v-if="material.video_path || material.video_link">
                                        <a
                                            :href="material.video_path ? `/storage/${material.video_path}` : (material.video_link ?? '')"
                                            target="_blank"
                                            class="text-blue-600 hover:text-blue-800"
                                            title="View Video"
                                        >
                                            <Video class="h-4 w-4" />
                                        </a>
                                    </template>
                                </td>
                                <td class="p-3 text-center align-top">
                                    <div class="flex justify-center gap-3">
                                        <button class="text-blue-600 hover:text-blue-800" @click="editMaterial(material.id)">
                                            <Pencil class="h-4 w-4" />
                                        </button>
                                        <button class="text-red-600 hover:text-red-800" @click="confirmDelete(material.id)">
                                            <Trash2 class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="material.comments?.length" class="bg-gray-50 text-sm">
                                <td colspan="7" class="p-4">
                                    <p class="mb-2 font-medium text-[#ff69b4]">Comments from Principal:</p>
                                    <ul class="ml-4 list-disc space-y-1 text-gray-600">
                                        <li v-for="comment in material.comments" :key="comment.id">
                                            "{{ comment.comment }}" — <i>{{ comment.user.name }}</i>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex flex-wrap justify-center gap-2">
                <template v-for="(link, i) in materials.links" :key="i">
                    <span
                        v-if="!link.url"
                        class="inline-flex cursor-not-allowed items-center justify-center rounded bg-gray-200 px-4 py-2 text-sm text-gray-500"
                        v-html="link.label"
                    />
                    <Link
                        v-else
                        :href="link.url"
                        class="inline-flex items-center justify-center rounded px-4 py-2 text-sm font-medium transition-all"
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
            <div class="w-full max-w-md rounded border border-[#01006c] bg-white p-6 shadow-lg">
                <h2 class="mb-4 text-lg font-semibold text-[#01006c]">Confirm Deletion</h2>
                <p class="mb-6 text-gray-700">Are you sure you want to delete this material?</p>
                <div class="flex justify-end space-x-4">
                    <button @click="cancelDelete" class="rounded border border-gray-400 bg-white px-4 py-2 text-sm text-gray-800 hover:bg-gray-100">
                        Cancel
                    </button>
                    <button @click="destroyMaterial" class="rounded bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700">Confirm</button>
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
