<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, FileUp } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const isLoading = computed(() => isCreating.value || isUpdating.value || isDeleting.value);
const isCreating = ref(false);
const isUpdating = ref(false);
const isDeleting = ref(false);

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
const filteredSections = computed(() => {
    const ylId = selectedType.value === 'material' ? selectedModule.value?.year_level.id : selectedYearLevelId.value;

    return props.sections.filter((s) => s.year_level_id === ylId);
});
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
    video: null as File | null,
    video_link: '',
    module_id: null as number | null,
});
watch(selectedModuleId, (newId) => {
    form.module_id = newId;
});

watch(selectedType, (newType) => {
    form.type = newType;
    selectedModuleId.value = null;
    selectedYearLevelId.value = null;
    selectedSubjectId.value = null;
    selectedSectionId.value = null;
});

watch(selectedModuleId, (newModuleId) => {
    if (selectedType.value === 'material') {
        const module = props.modules.find((m) => m.id === newModuleId);
        const sectionMatch = props.sections.find((s) => s.year_level_id === module?.year_level.id);
        selectedSectionId.value = sectionMatch?.id ?? null;
    }
});

function submitForm() {
    isCreating.value = true;

    const data = new FormData();
    data.append('title', form.title);
    data.append('type', form.type);
    data.append('description', form.description);
    data.append('file', form.file as Blob);

    if (form.video) {
        data.append('video', form.video as Blob);
    }

    if (form.video_link) {
        data.append('video_link', form.video_link);
    }

    if (form.type === 'material' && selectedModuleId.value) {
        if (selectedSectionId.value) {
            data.append('section_id', selectedSectionId.value.toString());
        }
    } else if (form.type === 'lesson_plan' && selectedSubjectId.value && selectedSectionId.value) {
        data.append('subject_id', selectedSubjectId.value.toString());
        data.append('section_id', selectedSectionId.value.toString());
    }

    form.post('/materials', {
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            selectedModuleId.value = null;
            selectedSectionId.value = null;
            selectedSubjectId.value = null;
        },
        onFinish: () => {
            setTimeout(() => {
                isCreating.value = false;
            }, 800);
        },
    });
}
</script>

<template>
    <Head title="Upload Material" />
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

        <div class="mx-auto w-full max-w-screen-xl space-y-6 px-6 py-8">
            <div class="flex items-center justify-between">
                <h2 class="flex items-center gap-2 text-2xl font-bold text-[#01006c]">
                    <FileUp class="h-6 w-6 text-[#01006c]" />
                    Upload {{ selectedType === 'material' ? 'Material' : 'Lesson Plan' }}
                </h2>
                <Link
                    href="/materials"
                    class="inline-flex items-center gap-2 rounded border border-[#01006c] bg-white px-4 py-2 text-sm font-semibold text-[#01006c] shadow hover:bg-[#ffc60b]"
                >
                    <ArrowLeft class="h-4 w-4" /> Back to Material
                </Link>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-[#01006c] text-[#ff69b4]">Type</label>
                    <select
                        v-model="selectedType"
                        class="w-full rounded border border-[#01006c] bg-white p-2 text-sm focus:border-[#ffc60b] focus:outline-none"
                    >
                        <option value="material">Material</option>
                        <option value="lesson_plan">Lesson Plan</option>
                    </select>
                </div>

                <div v-if="selectedType === 'material'" class="col-span-3 grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-[#01006c] text-[#ff69b4]">Module</label>
                        <select
                            v-model="selectedModuleId"
                            class="w-full rounded border border-[#01006c] bg-white p-2 text-sm focus:border-[#ffc60b] focus:outline-none"
                        >
                            <option :value="null" disabled>Select Module</option>
                            <option v-for="m in props.modules" :key="m.id" :value="m.id">{{ m.name }}</option>
                        </select>
                        <p v-if="form.errors.module_id" class="mt-1 text-sm text-red-600">{{ form.errors.module_id }}</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-[#01006c] text-[#ff69b4]">Year Level</label>
                        <select
                            disabled
                            class="w-full rounded border border-[#01006c] bg-gray-100 p-2 text-sm text-gray-700 focus:border-[#ffc60b] focus:outline-none"
                        >
                            <option v-if="selectedModule">{{ selectedModule.year_level.name }}</option>
                            <option v-else>Select Module First</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-[#01006c] text-[#ff69b4]">Section</label>
                        <select
                            disabled
                            v-model="selectedSectionId"
                            :disabled="!selectedModuleId"
                            class="w-full rounded border border-[#01006c] bg-white p-2 text-sm focus:border-[#ffc60b] focus:outline-none"
                        >
                            <option :value="null" disabled>Select Section</option>
                            <option v-for="section in filteredSections" :key="section.id" :value="section.id">
                                {{ section.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-[#01006c] text-[#ff69b4]">Subject</label>
                        <select
                            disabled
                            class="w-full rounded border border-[#01006c] bg-gray-100 p-2 text-sm text-gray-700 focus:border-[#ffc60b] focus:outline-none"
                        >
                            <option v-if="selectedModule">{{ selectedModule.subject.name }}</option>
                            <option v-else>Select Module First</option>
                        </select>
                    </div>
                </div>

                <div v-else class="col-span-3 grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-[#01006c] text-[#ff69b4]">Year Level</label>
                        <select
                            v-model="selectedYearLevelId"
                            class="w-full rounded border border-[#01006c] bg-white p-2 text-sm focus:border-[#ffc60b] focus:outline-none"
                        >
                            <option :value="null" disabled>Select Year Level</option>
                            <option v-for="yl in props.yearLevels" :key="yl.id" :value="yl.id">{{ yl.name }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-[#01006c] text-[#ff69b4]">Section</label>
                        <select
                            v-model="selectedSectionId"
                            class="w-full rounded border border-[#01006c] bg-white p-2 text-sm focus:border-[#ffc60b] focus:outline-none"
                        >
                            <option :value="null" disabled>Select Section</option>
                            <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-[#01006c] text-[#ff69b4]">Subject</label>
                        <select
                            v-model="selectedSubjectId"
                            class="w-full rounded border border-[#01006c] bg-white p-2 text-sm focus:border-[#ffc60b] focus:outline-none"
                        >
                            <option :value="null" disabled>Select Subject</option>
                            <option v-for="s in filteredSubjects" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-[#01006c] text-[#ff69b4]">Title</label>
                <input
                    v-model="form.title"
                    type="text"
                    class="w-full rounded border border-[#01006c] bg-white p-2 text-sm focus:border-[#ffc60b] focus:outline-none"
                />
                <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</p>
            </div>
            <div v-if="selectedType === 'material'">
                <label class="mb-1 block text-sm font-medium text-[#01006c] text-[#ff69b4]">Upload Video (Optional)</label>
                <input
                    type="file"
                    accept="video/*"
                    @change="(e) => (form.video = (e.target as HTMLInputElement)?.files?.[0] ?? null)"
                    class="w-full rounded border border-[#01006c] p-2 text-[#ff69b4] file:mr-4 file:border-0 file:bg-[#ffc60b]/20 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-[#01006c] hover:file:bg-[#ffc60b]/40 focus:border-[#ffc60b] focus:outline-none"
                />
            </div>

            <div v-if="selectedType === 'material'">
                <label class="mb-1 block text-sm font-medium text-[#01006c] text-[#ff69b4]">Video/Meeting Link (Optional)</label>
                <input
                    v-model="form.video_link"
                    type="url"
                    placeholder="https://youtube.com/... or https://zoom.us/..."
                    class="w-full rounded border border-[#01006c] bg-white p-2 text-sm focus:border-[#ffc60b] focus:outline-none"
                />
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-[#01006c] text-[#ff69b4]">Description</label>
                <textarea
                    v-model="form.description"
                    rows="3"
                    class="w-full rounded border border-[#01006c] bg-white p-2 text-sm focus:border-[#ffc60b] focus:outline-none"
                />
            </div>

            <div>
                <label class="mb-1 block flex items-center gap-1 text-sm font-medium text-[#01006c] text-[#ff69b4]">
                    <FileUp class="h-4 w-4 text-[#ff69b4]" />
                    Upload File
                </label>
                <input
                    type="file"
                    accept=".pdf,.doc,.docx,.ppt,.pptx"
                    @change="(e) => (form.file = (e.target as HTMLInputElement)?.files?.[0] ?? null)"
                    class="w-full rounded border border-[#01006c] p-2 text-[#ff69b4] file:mr-4 file:border-0 file:bg-[#ffc60b]/20 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-[#01006c] hover:file:bg-[#ffc60b]/40 focus:border-[#ffc60b] focus:outline-none"
                />
                <p v-if="form.errors.file" class="mt-1 text-sm text-red-600">{{ form.errors.file }}</p>
            </div>

            <button @click="submitForm" class="w-full rounded bg-[#01006c] py-2 text-white transition hover:bg-[#0d1282]">Submit</button>
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
