<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Check, Pencil, Plus, Save, Trash2, X } from 'lucide-vue-next';
import { computed, reactive, ref, watch } from 'vue';
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
    router.post('/subjects', createForm, {
        onFinish: () => {
            createForm.processing = false;
            showCreateModal.value = false;
        },
        onError: (errors) => {
            createForm.errors = errors;
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
    router.put(
        `/subjects/${editForm.id}`,
        {
            name: editForm.name,
            year_level_id: editForm.year_level_id,
        },
        {
            onFinish: () => {
                editForm.processing = false;
                showEditModal.value = false;
            },
            onError: (errors) => {
                editForm.errors = errors;
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
        router.delete(`/subjects/${deleteId.value}`, {
            onFinish: () => {
                showDeleteModal.value = false;
            },
        });
    }
};

const search = ref(props.filters.search || '');

watch(search, (value) => {
    router.get('/subjects', { search: value }, { preserveState: true, replace: true });
});
</script>
<template>
    <Head title="Subjects" />
    <AppLayout>
        <div class="p-4">
            <!-- Header -->
            <div class="mb-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-bold text-[#01006c]">📘 Subjects</h2>
                <div class="flex items-center gap-2">
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search subject..."
                        class="w-full rounded border border-[#01006c] p-2 text-sm shadow-sm focus:border-[#ffc60b] focus:outline-none md:w-64"
                    />
                    <button
                        @click="openCreateModal"
                        class="inline-flex items-center gap-2 rounded bg-[#01006c] px-4 py-2 text-white transition hover:bg-[#0d1282]"
                    >
                        <Plus class="h-4 w-4" />
                        Add Subject
                    </button>
                </div>
            </div>
            <!-- Table -->
            <div class="overflow-x-auto rounded-lg border border-[#01006c] shadow">
                <table class="min-w-full table-auto text-sm text-[#01006c]">
                    <thead class="bg-[#01006c] text-xs font-semibold text-white uppercase">
                        <tr>
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-left">Year Level</th>
                            <th class="px-4 py-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#01006c] bg-white">
                        <tr v-for="subject in props.subjects.data" :key="subject.id" class="transition hover:bg-gray-50">
                            <td class="px-4 py-2">{{ subject.name }}</td>
                            <td class="px-4 py-2">{{ subject.year_level?.name ?? '—' }}</td>
                            <td class="space-x-2 px-4 py-2 text-center">
                                <button
                                    @click="openEditModal(subject)"
                                    class="inline-flex items-center gap-1 text-[#01006c] transition hover:text-[#ff69b4]"
                                >
                                    <Pencil class="h-4 w-4" />
                                    Edit
                                </button>
                                <button
                                    @click="confirmDelete(subject.id)"
                                    class="inline-flex items-center gap-1 text-red-600 transition hover:text-red-800"
                                >
                                    <Trash2 class="h-4 w-4" />
                                    Delete
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
                        'border-blue-600 bg-blue-600 text-white': link.active,
                        'border-gray-300 text-gray-700 hover:bg-gray-100': !link.active,
                    }"
                >
                    <span v-html="link.label" />
                </Link>
            </template>
        </div>
        <!-- 🌸 Add Subject Modal -->
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
        <!-- 🌸 Edit Subject Modal -->
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
        <!-- 🌸 Delete Subject Modal -->
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
