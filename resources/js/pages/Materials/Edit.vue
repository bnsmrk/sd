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
    year_level: { id: number; name: string };
}

interface YearLevel {
    id: number;
    name: string;
}

const props = defineProps<{
    material: {
        id: number;
        title: string;
        type: 'material' | 'lesson_plan';
        year_level_id: number;
        subject_id: number;
        module_id?: number | null;
        file_path?: string | null;
    };
    modules: Module[];
    subjects: Subject[];
    yearLevels: YearLevel[];
}>();

const selectedType = ref<'material' | 'lesson_plan'>(props.material.type);
const selectedModuleId = ref<number | null>(props.material.module_id ?? null);
const selectedYearLevelId = ref<number | null>(props.material.year_level_id);
const selectedSubjectId = ref<number | null>(props.material.subject_id);

const selectedModule = computed(() => props.modules.find((m) => m.id === selectedModuleId.value));
const selectedSubject = computed(() => props.subjects.find((s) => s.id === selectedSubjectId.value));
const filteredSubjects = computed(() => props.subjects.filter((s) => s.year_level_id === selectedYearLevelId.value));

const form = useForm({
    title: props.material.title,
    file: null as File | null,
});

watch(selectedType, () => {
    selectedModuleId.value = null;
    selectedYearLevelId.value = null;
    selectedSubjectId.value = null;
});

function submitForm() {
    const data = new FormData();
    data.append('_method', 'put');
    data.append('title', form.title);
    data.append('type', selectedType.value);

    if (form.file) {
        data.append('file', form.file);
    }

    if (selectedType.value === 'material' && selectedModule.value) {
        data.append('module_id', selectedModule.value.id.toString());
        data.append('year_level_id', selectedModule.value.year_level.id.toString());
        data.append('subject_id', selectedModule.value.subject.id.toString());
    }

    if (selectedType.value === 'lesson_plan' && selectedSubject.value) {
        data.append('year_level_id', selectedYearLevelId.value!.toString());
        data.append('subject_id', selectedSubjectId.value!.toString());
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

            <!-- Type -->
            <div>
                <label class="block font-medium">Type</label>
                <select v-model="selectedType" class="w-full rounded border p-2">
                    <option value="material">Material</option>
                    <option value="lesson_plan">Lesson Plan</option>
                </select>
            </div>

            <!-- Module -->
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

            <!-- Year Level + Subject -->
            <div v-else>
                <label class="block font-medium">Year Level</label>
                <select v-model="selectedYearLevelId" class="w-full rounded border p-2">
                    <option :value="null" disabled>Select Year Level</option>
                    <option v-for="yl in props.yearLevels" :key="yl.id" :value="yl.id">{{ yl.name }}</option>
                </select>

                <label class="mt-4 block font-medium">Subject</label>
                <select v-model="selectedSubjectId" class="w-full rounded border p-2">
                    <option :value="null" disabled>Select Subject</option>
                    <option v-for="s in filteredSubjects" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>

                <div v-if="selectedSubject" class="mt-2 text-sm text-gray-600">
                    <p>Selected Subject: {{ selectedSubject.name }}</p>
                    <p>Year Level: {{ selectedSubject.year_level.name }}</p>
                </div>
            </div>

            <!-- Title -->
            <div>
                <label class="block font-medium">Title</label>
                <input v-model="form.title" type="text" class="w-full rounded border p-2" />
            </div>

            <!-- File -->
            <div>
                <label class="block font-medium">Upload File (optional)</label>
                <input
                    type="file"
                    class="w-full"
                    accept=".pdf,.doc,.docx,.ppt,.pptx"
                    @change="(e) => (form.file = (e.target as HTMLInputElement)?.files?.[0] ?? null)"
                />
                <div v-if="props.material.file_path" class="mt-2 text-sm text-gray-700">
                    <p>Current File:</p>
                    <a :href="`/storage/${props.material.file_path}`" target="_blank" rel="noopener" class="text-blue-600 underline"
                        >View Uploaded File</a
                    >
                </div>
            </div>

            <!-- Submit -->
            <button @click="submitForm" class="w-full rounded bg-blue-600 py-2 text-white">Update</button>
        </div>
    </AppLayout>
</template>
