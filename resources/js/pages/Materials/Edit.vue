<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

interface Module {
    id: number;
    name: string;
    year_level: { id: number; name: string };
    subject: { id: number; name: string };
}

interface Subject {
    id: number;
    name: string;
    year_level_id: number;
}

interface YearLevel {
    id: number;
    name: string;
}

interface Section {
    id: number;
    name: string;
    year_level_id: number;
}

const props = defineProps<{
    material: {
        id: number;
        title: string;
        type: 'material' | 'lesson_plan';
        description?: string | null;
        year_level_id: number;
        subject_id: number;
        section_id?: number | null;
        module_id?: number | null;
        file_path?: string | null;
    };
    modules: Module[];
    subjects: Subject[];
    yearLevels: YearLevel[];
    sections: Section[];
    subjectSectionMap: { section_id: number; subject_id: number }[];
}>();

const selectedType = ref(props.material.type);
const selectedModuleId = ref<number | null>(props.material.module_id ?? null);
const selectedYearLevelId = ref<number | null>(props.material.year_level_id ?? null);
const selectedSubjectId = ref<number | null>(props.material.subject_id ?? null);
const selectedSectionId = ref<number | null>(props.material.section_id ?? null);

const selectedModule = computed(() => props.modules.find((m) => m.id === selectedModuleId.value));
const filteredSections = computed(() => props.sections.filter((s) => s.year_level_id === selectedYearLevelId.value));
const filteredSubjects = computed(() => {
    if (!selectedSectionId.value) return [];
    const validSubjectIds = props.subjectSectionMap.filter((entry) => entry.section_id === selectedSectionId.value).map((entry) => entry.subject_id);
    return props.subjects.filter((subject) => validSubjectIds.includes(subject.id));
});

const form = useForm({
    title: props.material.title,
    type: props.material.type,
    description: props.material.description ?? '',
    file: null as File | null,
});

watch(selectedType, (newType) => {
    form.type = newType;
    selectedModuleId.value = null;
    selectedYearLevelId.value = null;
    selectedSubjectId.value = null;
    selectedSectionId.value = null;
});

function submitForm() {
    if (!form.title) {
        alert('Title is required.');
        return;
    }

    const data = new FormData();
    data.append('_method', 'put');
    data.append('title', form.title);
    data.append('type', form.type);
    data.append('description', form.description);

    if (form.file) {
        data.append('file', form.file as Blob);
    }

    if (form.type === 'material' && selectedModuleId.value) {
        const module = props.modules.find((m) => m.id === selectedModuleId.value);
        if (module) {
            data.append('module_id', selectedModuleId.value.toString());
            data.append('year_level_id', module.year_level.id.toString());
            data.append('subject_id', module.subject.id.toString());
        }
        if ((module as any).section_id) {
            data.append('section_id', (module as any).section_id.toString());
        }
    } else if (form.type === 'lesson_plan') {
        if (selectedSubjectId.value) data.append('subject_id', selectedSubjectId.value.toString());
        if (selectedSectionId.value) data.append('section_id', selectedSectionId.value.toString());
        if (selectedYearLevelId.value) data.append('year_level_id', selectedYearLevelId.value.toString());
    }

    router.post(`/materials/${props.material.id}`, data, {
        forceFormData: true,
    });
}
</script>

<template>
    <Head title="Edit Material" />
    <AppLayout>
        <div class="mx-auto max-w-xl space-y-6 p-6">
            <h2 class="text-xl font-bold">Edit {{ selectedType === 'material' ? 'Material' : 'Lesson Plan' }}</h2>

            <div>
                <label class="block font-medium">Type</label>
                <select v-model="selectedType" class="w-full rounded border p-2">
                    <option value="material">Material</option>
                    <option value="lesson_plan">Lesson Plan</option>
                </select>
            </div>

            <div v-if="selectedType === 'material'">
                <label class="block font-medium">Module</label>
                <select v-model="selectedModuleId" class="w-full rounded border p-2">
                    <option :value="null" disabled>Select Module</option>
                    <option v-for="m in props.modules" :key="m.id" :value="m.id">{{ m.name }}</option>
                </select>
                <div v-if="selectedModule" class="mt-2 text-sm text-gray-600">
                    <p>Year Level: {{ selectedModule.year_level.name }}</p>
                    <p>Subject: {{ selectedModule.subject.name }}</p>
                </div>
            </div>

            <div v-else>
                <label class="block font-medium">Year Level</label>
                <select v-model="selectedYearLevelId" class="w-full rounded border p-2">
                    <option :value="null" disabled>Select Year Level</option>
                    <option v-for="yl in props.yearLevels" :key="yl.id" :value="yl.id">{{ yl.name }}</option>
                </select>

                <label class="mt-4 block font-medium">Section</label>
                <select v-model="selectedSectionId" class="w-full rounded border p-2">
                    <option :value="null" disabled>Select Section</option>
                    <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>

                <label class="mt-4 block font-medium">Subject</label>
                <select v-model="selectedSubjectId" class="w-full rounded border p-2">
                    <option :value="null" disabled>Select Subject</option>
                    <option v-for="s in filteredSubjects" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>
            </div>

            <div>
                <label class="block font-medium">Title</label>
                <input v-model="form.title" type="text" class="w-full rounded border p-2" />
            </div>

            <div>
                <label class="block font-medium">Description</label>
                <textarea v-model="form.description" rows="3" class="w-full rounded border p-2" />
            </div>

            <div>
                <label class="block font-medium">Upload File</label>
                <input
                    type="file"
                    accept=".pdf,.doc,.docx,.ppt,.pptx"
                    @change="(e) => (form.file = (e.target as HTMLInputElement)?.files?.[0] ?? null)"
                    class="w-full"
                />
                <div v-if="props.material.file_path" class="mt-2 text-sm text-gray-700">
                    <p>Current File:</p>
                    <a :href="`/storage/${props.material.file_path}`" target="_blank" class="text-blue-600 underline"> View Uploaded File </a>
                </div>
            </div>

            <button @click="submitForm" class="w-full rounded bg-blue-600 py-2 text-white">Update</button>
        </div>
    </AppLayout>
</template>
