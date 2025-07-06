<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { FileUp } from 'lucide-vue-next';

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
    modules: Module[];
    subjects: Subject[];
    yearLevels: YearLevel[];
    sections: Section[];
    subjectSectionMap: { section_id: number; subject_id: number }[];
}>();

const selectedType = ref<'material' | 'lesson_plan'>('material');
const selectedModuleId = ref<number | null>(null);
const selectedYearLevelId = ref<number | null>(null);
const selectedSubjectId = ref<number | null>(null);
const selectedSectionId = ref<number | null>(null);

const selectedModule = computed(() => props.modules.find((m) => m.id === selectedModuleId.value));
const filteredSections = computed(() => props.sections.filter((s) => s.year_level_id === selectedYearLevelId.value));
const filteredSubjects = computed(() => {
    if (!selectedSectionId.value) return [];
    const validSubjectIds = props.subjectSectionMap.filter((entry) => entry.section_id === selectedSectionId.value).map((entry) => entry.subject_id);
    return props.subjects.filter((subject) => validSubjectIds.includes(subject.id));
});

const form = useForm({
    title: '',
    type: selectedType.value,
    description: '',
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
    if (!form.title || !form.file) {
        alert('Title and file are required.');
        return;
    }

    const data = new FormData();
    data.append('title', form.title);
    data.append('type', form.type);
    data.append('description', form.description);
    data.append('file', form.file as Blob);

    if (form.type === 'material' && selectedModuleId.value) {
        data.append('module_id', selectedModuleId.value.toString());
    } else if (form.type === 'lesson_plan' && selectedSubjectId.value && selectedSectionId.value) {
        data.append('subject_id', selectedSubjectId.value.toString());
        data.append('section_id', selectedSectionId.value.toString());
    }

    router.post('/materials', data);
}
</script>

<template>
    <Head title="Upload Material" />
    <AppLayout>
        <div class="mx-auto max-w-5xl space-y-6 p-6">
            <h2 class="text-2xl font-bold flex items-center gap-2">
                <FileUp class="w-6 h-6 text-blue-600" />
                Upload {{ selectedType === 'material' ? 'Material' : 'Lesson Plan' }}
            </h2>

            <!-- Horizontal Inputs Row -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Type -->
                <div>
                    <label class="block font-medium mb-1">Type</label>
                    <select v-model="selectedType" class="w-full rounded border p-2">
                        <option value="material">Material</option>
                        <option value="lesson_plan">Lesson Plan</option>
                    </select>
                </div>

                <!-- Module / Year Level + Section + Subject -->
                <div v-if="selectedType === 'material'" class="col-span-3 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Module -->
                    <div>
                        <label class="block font-medium mb-1">Module</label>
                        <select v-model="selectedModuleId" class="w-full rounded border p-2">
                            <option :value="null" disabled>Select Module</option>
                            <option v-for="m in props.modules" :key="m.id" :value="m.id">{{ m.name }}</option>
                        </select>
                    </div>

                    <!-- Year Level -->
                    <div>
                        <label class="block font-medium mb-1">Year Level</label>
                        <select disabled class="w-full rounded border p-2 bg-gray-100 text-gray-700" v-if="selectedModule">
                            <option>{{ selectedModule.year_level.name }}</option>
                        </select>
                        <select disabled class="w-full rounded border p-2 bg-gray-100 text-gray-400" v-else>
                            <option>Select Module First</option>
                        </select>
                    </div>

                    <!-- Subject -->
                    <div>
                        <label class="block font-medium mb-1">Subject</label>
                        <select disabled class="w-full rounded border p-2 bg-gray-100 text-gray-700" v-if="selectedModule">
                            <option>{{ selectedModule.subject.name }}</option>
                        </select>
                        <select disabled class="w-full rounded border p-2 bg-gray-100 text-gray-400" v-else>
                            <option>Select Module First</option>
                        </select>
                    </div>
                </div>

                <!-- Lesson Plan Inputs -->
                <div v-else class="col-span-3 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Year Level -->
                    <div>
                        <label class="block font-medium mb-1">Year Level</label>
                        <select v-model="selectedYearLevelId" class="w-full rounded border p-2">
                            <option :value="null" disabled>Select Year Level</option>
                            <option v-for="yl in props.yearLevels" :key="yl.id" :value="yl.id">{{ yl.name }}</option>
                        </select>
                    </div>

                    <!-- Section -->
                    <div>
                        <label class="block font-medium mb-1">Section</label>
                        <select v-model="selectedSectionId" class="w-full rounded border p-2">
                            <option :value="null" disabled>Select Section</option>
                            <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                    </div>

                    <!-- Subject -->
                    <div>
                        <label class="block font-medium mb-1">Subject</label>
                        <select v-model="selectedSubjectId" class="w-full rounded border p-2">
                            <option :value="null" disabled>Select Subject</option>
                            <option v-for="s in filteredSubjects" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Title -->
            <div>
                <label class="block font-medium">Title</label>
                <input v-model="form.title" type="text" class="w-full rounded border p-2" />
            </div>

            <!-- Description -->
            <div>
                <label class="block font-medium">Description</label>
                <textarea v-model="form.description" rows="3" class="w-full rounded border p-2" />
            </div>

            <!-- File Upload -->
            <div>
                <label class="block font-medium mb-1 flex items-center gap-1">
                    <FileUp class="w-4 h-4 text-blue-600" />
                    Upload File
                </label>
                <input
                    type="file"
                    accept=".pdf,.doc,.docx,.ppt,.pptx"
                    @change="(e) => (form.file = (e.target as HTMLInputElement)?.files?.[0] ?? null)"
                    class="w-full rounded border p-2 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                />
            </div>

            <!-- Submit -->
            <button @click="submitForm" class="w-full rounded bg-blue-600 py-2 text-white hover:bg-blue-700 transition">
                Submit
            </button>
        </div>
    </AppLayout>
</template>
