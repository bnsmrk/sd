<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { Check, Pencil, Save, Trash2, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const isLoading = computed(() => isCreating.value || isUpdating.value || isDeleting.value);
const isCreating = ref(false);
const isUpdating = ref(false);
const isDeleting = ref(false);
// const showFlash = ref(false);

const props = defineProps<{
    yearLevels: {
        data: Array<{ id: number; name: string }>;
        links: Array<{ url: string | null; label: string; active: boolean }>;
    };
    filters: { search?: string };
}>();

const breadcrumbs = [{ title: 'Year Levels', href: '/year-levels' }];

const search = ref(props.filters.search || '');

watch(
    search,
    debounce((value) => {
        router.get('/year-levels', { search: value }, { preserveState: true, replace: true });
    }, 300),
);

const showAddModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);

const createForm = useForm({ name: '' });
const editForm = useForm({ id: null as number | null, name: '' });
const deleteId = ref<number | null>(null);

const openAddModal = () => {
    createForm.reset();
    showAddModal.value = true;
};

const openEditModal = (level: { id: number; name: string }) => {
    editForm.id = level.id;
    editForm.name = level.name;
    showEditModal.value = true;
};

const openDeleteModal = (id: number) => {
    deleteId.value = id;
    showDeleteModal.value = true;
};

const createYearLevel = () => {
    isCreating.value = true;
    createForm.post('/year-levels', {
        onSuccess: () => {
            showAddModal.value = false;
            createForm.reset();
        },
        onFinish: () => {
            setTimeout(() => {
                isCreating.value = false;
            }, 2000);
        },
    });
};

const updateYearLevel = () => {
    if (editForm.id) {
        isUpdating.value = true;
        editForm.put(`/year-levels/${editForm.id}`, {
            onSuccess: () => {
                showEditModal.value = false;
            },
            onFinish: () => {
                setTimeout(() => {
                    isUpdating.value = false;
                }, 2000);
            },
        });
    }
};

const deleteYearLevel = () => {
    if (deleteId.value !== null) {
        isDeleting.value = true;
        router.delete(`/year-levels/${deleteId.value}`, {
            onSuccess: () => {
                deleteId.value = null;
                showDeleteModal.value = false;
            },
            onFinish: () => {
                setTimeout(() => {
                    isDeleting.value = false;
                }, 2000);
            },
        });
    }
};
</script>

<template>
    <Head title="Year Levels" />
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

        <div class="p-6">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <h1 class="text-xl font-bold text-[#01006c]">👥 Users</h1>
                <div class="flex flex-wrap items-center gap-2">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search..."
                        class="rounded border border-[#01006c] px-3 py-2 text-sm shadow-sm focus:border-[#ffc60b] focus:outline-none"
                    />
                    <button @click="openAddModal" class="inline-flex items-center gap-2 rounded bg-[#01006c] px-4 py-2 text-white hover:bg-[#0d1282]">
                        <UserPlus class="h-4 w-4" /> + Add Year Level
                    </button>
                </div>
            </div>

            <div class="overflow-hidden rounded-lg border border-[#01006c] shadow">
                <table class="min-w-full divide-y divide-[#01006c]">
                    <thead class="bg-[#01006c] text-xs font-semibold text-white">
                        <tr>
                            <th class="px-6 py-3 text-center">ID</th>
                            <th class="px-6 py-3 text-left">Name</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#01006c] bg-white text-sm">
                        <tr v-for="level in props.yearLevels.data" :key="level.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-center font-medium text-[#01006c]">{{ level.id }}</td>
                            <td class="px-6 py-4 text-left text-[#01006c]">{{ level.name }}</td>
                            <td class="space-x-2 px-6 py-4 text-center">
                                <button
                                    @click="openEditModal(level)"
                                    class="inline-flex items-center gap-1 text-[#01006c] transition hover:text-[#ff69b4]"
                                >
                                    <Pencil class="h-4 w-4" /> Edit
                                </button>
                                <button
                                    @click="openDeleteModal(level.id)"
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
                <template v-for="(link, i) in props.yearLevels.links" :key="i">
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

        <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-2xl border-2 border-[#ff69b4] bg-white p-6 shadow-xl">
                <h3 class="mb-4 text-xl font-bold text-[#ff69b4]">➕ Add Year Level</h3>
                <form @submit.prevent="createYearLevel" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-[#01006c]">Name</label>
                        <input
                            v-model="createForm.name"
                            type="text"
                            placeholder="e.g. Grade 7"
                            class="w-full rounded-lg border-2 border-[#01006c] px-3 py-2 text-[#01006c] focus:border-[#ffc60b] focus:outline-none"
                        />
                        <div v-if="createForm.errors.name" class="text-sm text-red-600">
                            {{ createForm.errors.name }}
                        </div>
                    </div>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button
                            type="button"
                            @click="showAddModal = false"
                            class="inline-flex items-center gap-1 rounded-md bg-[#ffc60b] px-4 py-2 text-sm font-semibold text-[#01006c] hover:brightness-110"
                        >
                            <X class="h-4 w-4" /> Cancel
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center gap-1 rounded-md bg-[#ff69b4] px-4 py-2 text-sm font-semibold text-white hover:bg-[#e858a1]"
                        >
                            <Save class="h-4 w-4" /> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-2xl border-2 border-[#ff69b4] bg-white p-6 shadow-xl">
                <h3 class="mb-4 text-xl font-bold text-[#ff69b4]">✏️ Edit Year Level</h3>
                <form @submit.prevent="updateYearLevel" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-[#01006c]">Name</label>
                        <input
                            v-model="editForm.name"
                            type="text"
                            class="w-full rounded-lg border-2 border-[#01006c] px-3 py-2 text-[#01006c] focus:border-[#ffc60b] focus:outline-none"
                        />
                        <div v-if="editForm.errors.name" class="text-sm text-red-600">
                            {{ editForm.errors.name }}
                        </div>
                    </div>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button
                            type="button"
                            @click="showEditModal = false"
                            class="inline-flex items-center gap-1 rounded-md bg-[#ffc60b] px-4 py-2 text-sm font-semibold text-[#01006c] hover:brightness-110"
                        >
                            <X class="h-4 w-4" /> Cancel
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center gap-1 rounded-md bg-[#ff69b4] px-4 py-2 text-sm font-semibold text-white hover:bg-[#e858a1]"
                        >
                            <Check class="h-4 w-4" /> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-2xl border-2 border-[#ff69b4] bg-white p-6 shadow-xl">
                <h3 class="mb-4 text-xl font-bold text-[#ff69b4]">⚠️ Confirm Deletion</h3>
                <p class="mb-6 text-sm text-gray-600">Are you sure you want to delete this year level?</p>
                <div class="flex justify-end space-x-2 pt-2">
                    <button
                        @click="showDeleteModal = false"
                        class="inline-flex items-center gap-1 rounded-md bg-[#ffc60b] px-4 py-2 text-sm font-semibold text-[#01006c] hover:brightness-110"
                    >
                        <X class="h-4 w-4" /> Cancel
                    </button>
                    <button
                        @click="deleteYearLevel"
                        class="inline-flex items-center gap-1 rounded-md bg-[#ff69b4] px-4 py-2 text-sm font-semibold text-white hover:bg-[#e858a1]"
                    >
                        <Trash2 class="h-4 w-4" /> Delete
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
