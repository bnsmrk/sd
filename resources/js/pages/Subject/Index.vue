<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';

// Props
const props = defineProps<{
    subjects: Array<{
        id: number;
        name: string;
        year_level?: { id: number; name: string } | null;
        section?: { id: number; name: string } | null;
    }>;
    yearLevels: Array<{ id: number; name: string }>;
}>();

// === Modal State ===
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const deleteId = ref<number | null>(null);

// === Create Form ===
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

// === Edit Form ===
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

// === Computed: Is Senior High ===
const isSHS = computed(() => {
    const yl = props.yearLevels.find((yl) => yl.id === createForm.year_level_id);
    return yl?.name === 'Grade 11' || yl?.name === 'Grade 12';
});

// === Utilities ===
const addTo = (arr: string[]) => arr.push('');
const removeFrom = (arr: string[], index: number) => arr.splice(index, 1);

// === Actions ===
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

const openEditModal = (subject: (typeof props.subjects)[0]) => {
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
</script>

<template>
    <Head title="Subjects" />
    <AppLayout>
        <div class="p-4">
            <div class="mb-4 flex justify-between">
                <h2 class="text-xl font-bold">Subjects</h2>
                <button @click="openCreateModal" class="rounded bg-blue-600 px-4 py-2 text-white">Add Subject</button>
            </div>

            <!-- Subject Table -->
            <table class="min-w-full table-auto text-sm text-gray-700 shadow">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">Year Level</th>
                        <th class="px-4 py-2 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="subject in subjects" :key="subject.id" class="bg-white hover:bg-gray-50">
                        <td class="px-4 py-2">{{ subject.name }}</td>
                        <td class="px-4 py-2">{{ subject.year_level?.name ?? '—' }}</td>
                        <td class="space-x-2 px-4 py-2 text-center">
                            <button @click="openEditModal(subject)" class="text-blue-600 hover:underline">Edit</button>
                            <button @click="confirmDelete(subject.id)" class="text-red-600 hover:underline">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Create Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur">
            <div class="w-full max-w-xl rounded bg-white p-6 shadow">
                <h2 class="mb-4 text-lg font-bold">Add Subject</h2>

                <!-- Year Level -->
                <div class="mb-4">
                    <label class="block font-semibold">Year Level</label>
                    <select v-model.number="createForm.year_level_id" class="w-full rounded border p-2">
                        <option value="">Select Year Level</option>
                        <option v-for="yl in props.yearLevels" :key="yl.id" :value="yl.id">{{ yl.name }}</option>
                    </select>
                    <div class="text-sm text-red-600">{{ createForm.errors.year_level_id }}</div>
                </div>

                <!-- Shared Subjects -->
                <div class="mb-4">
                    <label class="block font-semibold">Shared Subjects</label>
                    <div v-for="(subject, index) in createForm.shared_subjects" :key="index" class="mb-2 flex gap-2">
                        <input v-model="createForm.shared_subjects[index]" class="w-full rounded border p-2" />
                        <button @click.prevent="removeFrom(createForm.shared_subjects, index)" v-if="createForm.shared_subjects.length > 1">✕</button>
                    </div>
                    <button @click.prevent="addTo(createForm.shared_subjects)" class="text-blue-600 hover:underline">+ Add</button>
                </div>

                <!-- Major Subjects -->
                <div class="mb-4" v-if="isSHS">
                    <label class="block font-semibold">Major Subjects</label>
                    <div v-for="(subject, index) in createForm.major_subjects" :key="index" class="mb-2 flex gap-2">
                        <input v-model="createForm.major_subjects[index]" class="w-full rounded border p-2" />
                        <button @click.prevent="removeFrom(createForm.major_subjects, index)" v-if="createForm.major_subjects.length > 1">✕</button>
                    </div>
                    <button @click.prevent="addTo(createForm.major_subjects)" class="text-blue-600 hover:underline">+ Add</button>
                </div>

                <div class="mt-4 flex justify-end space-x-2">
                    <button @click="showCreateModal = false" class="rounded bg-gray-300 px-4 py-2">Cancel</button>
                    <button @click="submitCreate" class="rounded bg-blue-600 px-4 py-2 text-white" :disabled="createForm.processing">
                        {{ createForm.processing ? 'Saving...' : 'Save' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur">
            <div class="w-full max-w-md rounded bg-white p-6 shadow">
                <h2 class="mb-4 text-lg font-bold">Edit Subject</h2>
                <div class="mb-4">
                    <label class="block font-semibold">Subject Name</label>
                    <input v-model="editForm.name" class="w-full rounded border p-2" />
                    <div class="text-sm text-red-600">{{ editForm.errors.name }}</div>
                </div>
                <div class="mb-4">
                    <label class="block font-semibold">Year Level</label>
                    <select v-model.number="editForm.year_level_id" class="w-full rounded border p-2">
                        <option value="">Select Year Level</option>
                        <option v-for="yl in props.yearLevels" :key="yl.id" :value="yl.id">{{ yl.name }}</option>
                    </select>
                    <div class="text-sm text-red-600">{{ editForm.errors.year_level_id }}</div>
                </div>
                <div class="flex justify-end space-x-2">
                    <button @click="showEditModal = false" class="rounded bg-gray-300 px-4 py-2">Cancel</button>
                    <button @click="submitEdit" class="rounded bg-green-600 px-4 py-2 text-white" :disabled="editForm.processing">
                        {{ editForm.processing ? 'Updating...' : 'Update' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur">
            <div class="w-full max-w-sm rounded bg-white p-6 shadow">
                <h2 class="mb-4 text-lg font-bold">Confirm Deletion</h2>
                <p class="mb-6 text-gray-600">Are you sure you want to delete this subject?</p>
                <div class="flex justify-end space-x-4">
                    <button @click="showDeleteModal = false" class="rounded bg-gray-300 px-4 py-2">Cancel</button>
                    <button @click="destroyItem" class="rounded bg-red-600 px-4 py-2 text-white">Confirm</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
