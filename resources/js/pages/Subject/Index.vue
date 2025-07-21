<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Check, Pencil, Plus, Save, Trash2, X } from 'lucide-vue-next';
import { computed, reactive, ref, watch } from 'vue';

const isLoading = computed(() => isCreating.value || isUpdating.value || isDeleting.value);
const isCreating = ref(false);
const isUpdating = ref(false);
const isDeleting = ref(false);

const props = defineProps<{
    subjects: {
        data: Array<{
            id: number;
            name: string;
            year_level?: { id: number; name: string } | null;
            section?: { id: number; name: string } | null;
        }>;
        links: Array<{
            url: string | null;
            label: string;
            active: boolean;
        }>;
    };
    yearLevels: Array<{ id: number; name: string }>;
    filters: {
        search: string;
    };
}>();

const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const deleteId = ref<number | null>(null);

const createForm = reactive<{
    year_level_id: number | '';
    shared_subjects: string[];
    major_subjects: string[];
    errors: Record<string, string>;
    processing: boolean;
}>({
    year_level_id: '',
    shared_subjects: [''],
    major_subjects: [''],
    errors: {},
    processing: false,
});

const editForm = reactive<{
    id: number | null;
    name: string;
    year_level_id: number | '';
    errors: Record<string, string>;
    processing: boolean;
}>({
    id: null,
    name: '',
    year_level_id: '',
    errors: {},
    processing: false,
});

const isSHS = computed(() => {
    const yl = props.yearLevels.find((yl) => yl.id === createForm.year_level_id);
    return yl?.name === 'Grade 11' || yl?.name === 'Grade 12';
});

const addTo = (arr: string[]) => arr.push('');
const removeFrom = (arr: string[], index: number) => arr.splice(index, 1);

const openCreateModal = () => {
    showCreateModal.value = true;
    createForm.year_level_id = '';
    createForm.shared_subjects = [''];
    createForm.major_subjects = [''];
    createForm.errors = {};
};

const submitCreate = () => {
    createForm.processing = true;
    isCreating.value = true;
    createForm.errors = {};

    router.post('/subjects', createForm, {
        preserveScroll: true,
        onSuccess: () => {
            showCreateModal.value = false;
        },
        onError: (errors) => {
            // console.error('Validation errors:', errors);
            createForm.errors = errors;
        },

        onFinish: () => {
            createForm.processing = false;
            setTimeout(() => {
                isCreating.value = false;
            }, 500);
        },
    });
};

const openEditModal = (subject: (typeof props.subjects.data)[0]) => {
    editForm.id = subject.id;
    editForm.name = subject.name;
    editForm.year_level_id = subject.year_level?.id ?? '';
    editForm.errors = {};
    showEditModal.value = true;
};

const submitEdit = () => {
    if (!editForm.id) return;
    editForm.processing = true;
    isUpdating.value = true;

    router.put(
        `/subjects/${editForm.id}`,
        {
            name: editForm.name,
            year_level_id: editForm.year_level_id,
        },
        {
            preserveScroll: true,
            onError: (errors) => {
                // console.error('Validation errors:', errors);
                editForm.errors = errors;
            },
            onFinish: () => {
                editForm.processing = false;

                if (!Object.keys(editForm.errors).length) {
                    showEditModal.value = false;
                }

                setTimeout(() => {
                    isUpdating.value = false;
                }, 500);
            },
        },
    );
};

const confirmDelete = (id: number) => {
    deleteId.value = id;
    showDeleteModal.value = true;
};

const destroyItem = () => {
    if (deleteId.value !== null) {
        isDeleting.value = true;
        router.delete(`/subjects/${deleteId.value}`, {
            onFinish: () => {
                showDeleteModal.value = false;
                setTimeout(() => {
                    isDeleting.value = false;
                }, 800);
            },
        });
    }
};

const search = ref(props.filters.search || '');

watch(search, (value) => {
    router.get('/subjects', { search: value }, { preserveState: true, replace: true });
});

type SortableSubjectKey = 'name' | 'year_level';

const sortKey = ref<SortableSubjectKey>('name');
const sortAsc = ref(true);

const toggleSort = (key: SortableSubjectKey) => {
    if (sortKey.value === key) {
        sortAsc.value = !sortAsc.value;
    } else {
        sortKey.value = key;
        sortAsc.value = true;
    }
};

const sortedSubjects = computed(() => {
    return [...props.subjects.data].sort((a, b) => {
        let aValue: string = '';
        let bValue: string = '';

        if (sortKey.value === 'year_level') {
            aValue = a.year_level?.name?.toLowerCase() || '';
            bValue = b.year_level?.name?.toLowerCase() || '';
        } else {
            aValue = (a as any)[sortKey.value]?.toLowerCase?.() || '';
            bValue = (b as any)[sortKey.value]?.toLowerCase?.() || '';
        }

        if (aValue < bValue) return sortAsc.value ? -1 : 1;
        if (aValue > bValue) return sortAsc.value ? 1 : -1;
        return 0;
    });
});
</script>
<template>
    <Head title="Subjects" />
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
        <div class="p-4">
            <div class="mb-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-bold text-pink-400">Subjects</h2>
                <div class="flex items-center gap-2">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search subject..."
                        class="rounded border border-pink-300 px-3 py-2 text-sm text-pink-400 shadow-sm focus:border-yellow-400 focus:outline-none"
                    />
                    <button
                        @click="openCreateModal"
                        class="inline-flex items-center gap-2 rounded bg-pink-400 px-4 py-2 text-white transition hover:bg-pink-500"
                    >
                        <Plus class="h-4 w-4" />
                        Add Subject
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto rounded-lg border border-pink-200 shadow">
                <table class="min-w-full table-auto text-left text-sm text-pink-400">
                    <thead class="bg-pink-100 text-xs font-semibold text-pink-400 uppercase">
                        <tr>
                            <th @click="toggleSort('name')" class="cursor-pointer px-4 py-2 text-left">
                                Name <span v-if="sortKey === 'name'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th @click="toggleSort('year_level')" class="cursor-pointer px-4 py-2 text-left">
                                Year Level <span v-if="sortKey === 'year_level'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th class="px-4 py-2 text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-pink-100 bg-white">
                        <tr v-for="subject in sortedSubjects" :key="subject.id" class="hover:bg-pink-50">
                            <td class="px-4 py-2 text-[#01006c]">{{ subject.name }}</td>
                            <td class="px-4 py-2 text-[#01006c]">{{ subject.year_level?.name ?? '—' }}</td>
                            <td class="space-x-2 px-4 py-2 text-center">
                                <button @click="openEditModal(subject)" class="text-blue-600 hover:text-blue-800">
                                    <Pencil class="h-4 w-4" />
                                </button>
                                <button @click="confirmDelete(subject.id)" class="text-red-600 hover:text-red-800">
                                    <Trash2 class="h-4 w-4" />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6 flex justify-center gap-2">
            <template v-for="(link, i) in props.subjects.links" :key="i">
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
        <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
            <div class="w-full max-w-xl rounded-2xl border-2 border-[#ff69b4] bg-white p-6 shadow-xl">
                <h2 class="mb-4 text-xl font-bold text-[#ff69b4]">➕ Add Subject</h2>
                <div class="mb-4">
                    <label class="block font-semibold text-[#01006c]">Year Level</label>
                    <select
                        v-model.number="createForm.year_level_id"
                        class="w-full rounded-lg border-2 border-[#01006c] px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                    >
                        <option value="">Select Year Level</option>
                        <option v-for="yl in props.yearLevels" :key="yl.id" :value="yl.id">{{ yl.name }}</option>
                    </select>
                    <div class="text-sm text-red-600">{{ createForm.errors.year_level_id }}</div>
                </div>
                <div class="mb-4">
                    <label class="block font-semibold text-[#01006c]">Shared Subjects</label>
                    <div v-for="(subject, index) in createForm.shared_subjects" :key="index" class="mb-2 flex gap-2">
                        <input
                            v-model="createForm.shared_subjects[index]"
                            class="w-full rounded-lg border-2 border-[#01006c] px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                        />
                        <div class="text-sm text-red-600">
                            <div v-if="createForm.errors['shared_subjects']">{{ createForm.errors['shared_subjects'] }}</div>
                            <div v-for="(subject, index) in createForm.shared_subjects" :key="index">
                                <span v-if="createForm.errors[`shared_subjects.${index}`]">
                                    {{ createForm.errors[`shared_subjects.${index}`] }}
                                </span>
                            </div>
                        </div>

                        <div class="text-sm text-red-600" v-if="isSHS">
                            <div v-if="createForm.errors['major_subjects']">{{ createForm.errors['major_subjects'] }}</div>
                            <div v-for="(subject, index) in createForm.major_subjects" :key="index">
                                <span v-if="createForm.errors[`major_subjects.${index}`]">
                                    {{ createForm.errors[`major_subjects.${index}`] }}
                                </span>
                            </div>
                        </div>

                        <button
                            @click.prevent="removeFrom(createForm.shared_subjects, index)"
                            v-if="createForm.shared_subjects.length > 1"
                            class="text-red-600 hover:text-red-800"
                        >
                            <X class="h-4 w-4" />
                        </button>
                    </div>
                    <button @click.prevent="addTo(createForm.shared_subjects)" class="text-sm text-blue-600 hover:underline">+ Add</button>
                </div>
                <div class="mb-4" v-if="isSHS">
                    <label class="block font-semibold text-[#01006c]">Major Subjects</label>
                    <div v-for="(subject, index) in createForm.major_subjects" :key="index" class="mb-2 flex gap-2">
                        <input
                            v-model="createForm.major_subjects[index]"
                            class="w-full rounded-lg border-2 border-[#01006c] px-3 py-2 focus:border-[#ffc60b]"
                        />
                        <div class="text-sm text-red-600">
                            <div v-if="createForm.errors['shared_subjects']">{{ createForm.errors['shared_subjects'] }}</div>
                            <div v-for="(subject, index) in createForm.shared_subjects" :key="index">
                                <span v-if="createForm.errors[`shared_subjects.${index}`]">
                                    {{ createForm.errors[`shared_subjects.${index}`] }}
                                </span>
                            </div>
                        </div>

                        <div class="text-sm text-red-600" v-if="isSHS">
                            <div v-if="createForm.errors['major_subjects']">{{ createForm.errors['major_subjects'] }}</div>
                            <div v-for="(subject, index) in createForm.major_subjects" :key="index">
                                <span v-if="createForm.errors[`major_subjects.${index}`]">
                                    {{ createForm.errors[`major_subjects.${index}`] }}
                                </span>
                            </div>
                        </div>

                        <button
                            @click.prevent="removeFrom(createForm.major_subjects, index)"
                            v-if="createForm.major_subjects.length > 1"
                            class="text-red-600 hover:text-red-800"
                        >
                            <X class="h-4 w-4" />
                        </button>
                    </div>
                    <button @click.prevent="addTo(createForm.major_subjects)" class="text-sm text-blue-600 hover:underline">+ Add</button>
                </div>
                <div class="mt-4 flex justify-end space-x-2">
                    <button
                        @click="showCreateModal = false"
                        class="inline-flex items-center gap-1 rounded-md bg-[#ffc60b] px-4 py-2 text-sm font-semibold text-[#01006c] hover:brightness-110"
                    >
                        <X class="h-4 w-4" />
                        Cancel
                    </button>
                    <button
                        @click="submitCreate"
                        class="inline-flex items-center gap-1 rounded-md bg-[#ff69b4] px-4 py-2 text-sm font-semibold text-white hover:bg-[#e858a1]"
                        :disabled="createForm.processing"
                    >
                        <Save class="h-4 w-4" />
                        {{ createForm.processing ? 'Saving...' : 'Save' }}
                    </button>
                </div>
            </div>
        </div>
        <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-2xl border-2 border-[#ff69b4] bg-white p-6 shadow-xl">
                <h2 class="mb-4 text-xl font-bold text-[#ff69b4]">✏️ Edit Subject</h2>
                <div class="mb-4">
                    <label class="block font-semibold text-[#01006c]">Subject Name</label>
                    <input
                        v-model="editForm.name"
                        class="w-full rounded-lg border-2 border-[#01006c] px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                    />
                    <div class="text-sm text-red-600">{{ editForm.errors.name }}</div>
                </div>
                <div class="mb-4">
                    <label class="block font-semibold text-[#01006c]">Year Level</label>
                    <select
                        v-model.number="editForm.year_level_id"
                        class="w-full rounded-lg border-2 border-[#01006c] px-3 py-2 focus:border-[#ffc60b]"
                    >
                        <option value="">Select Year Level</option>
                        <option v-for="yl in props.yearLevels" :key="yl.id" :value="yl.id">{{ yl.name }}</option>
                    </select>
                    <div class="text-sm text-red-600">{{ editForm.errors.year_level_id }}</div>
                </div>
                <div class="flex justify-end space-x-2">
                    <button
                        @click="showEditModal = false"
                        class="inline-flex items-center gap-1 rounded-md bg-[#ffc60b] px-4 py-2 text-sm font-semibold text-[#01006c] hover:brightness-110"
                    >
                        <X class="h-4 w-4" />
                        Cancel
                    </button>
                    <button
                        @click="submitEdit"
                        class="inline-flex items-center gap-1 rounded-md bg-[#ff69b4] px-4 py-2 text-sm font-semibold text-white hover:bg-[#e858a1]"
                        :disabled="editForm.processing"
                    >
                        <Check class="h-4 w-4" />
                        {{ editForm.processing ? 'Updating...' : 'Update' }}
                    </button>
                </div>
            </div>
        </div>
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
            <div class="w-full max-w-sm rounded-2xl border-2 border-[#ff69b4] bg-white p-6 shadow-xl">
                <h2 class="mb-4 text-xl font-bold text-[#ff69b4]">🗑️ Confirm Deletion</h2>
                <p class="mb-6 text-[#01006c]">Are you sure you want to delete this subject?</p>
                <div class="flex justify-end space-x-4">
                    <button
                        @click="showDeleteModal = false"
                        class="inline-flex items-center gap-1 rounded-md bg-[#ffc60b] px-4 py-2 text-sm font-semibold text-[#01006c] hover:brightness-110"
                    >
                        <X class="h-4 w-4" />
                        Cancel
                    </button>
                    <button
                        @click="destroyItem"
                        class="inline-flex items-center gap-1 rounded-md bg-[#ff69b4] px-4 py-2 text-sm font-semibold text-white hover:bg-[#e858a1]"
                    >
                        <Trash2 class="h-4 w-4" />
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
