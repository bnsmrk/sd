<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { Book, GraduationCap, Layers, ListChecks, Users } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
const isLoading = ref(false);

interface YearLevel {
    id: number;
    name: string;
}
interface Section {
    id: number;
    name: string;
    year_level_id: number;
}
interface Subject {
    id: number;
    name: string;
    section_id: number;
}
interface Module {
    id: number;
    name: string;
}
interface ResultEntry {
    student: string;
    score: number;
    total: number;
    average: number;
}
interface GroupedResult {
    activity_title: string;
    entries: ResultEntry[];
}

const props = defineProps<{
    yearLevels: YearLevel[];
    sections: Section[];
    subjects: Subject[];
    modules: Module[];
    filters: {
        year_level_id: number | null;
        section_id: number | null;
        subject_id: number | null;
        module_id: number | null;
        type: string;
    };
    resultsByActivity: GroupedResult[];
}>();

const filtersApplied = ref(false);
const selectedYearLevel = ref<number | null>(props.filters.year_level_id);
const selectedSection = ref<number | null>(props.filters.section_id);
const selectedSubject = ref<number | null>(props.filters.subject_id);
const selectedModule = ref<number | null>(props.filters.module_id);
const selectedType = ref<string>(props.filters.type || 'quiz');

const filteredSections = computed(() => props.sections.filter((s) => s.year_level_id === selectedYearLevel.value));
const filteredSubjects = computed(() => props.subjects.filter((s) => s.section_id === selectedSection.value));

watch(selectedSubject, () => {
    selectedModule.value = null;
    if (selectedSubject.value && selectedYearLevel.value && selectedSection.value) {
        router.get(
            '/students-proficiency',
            {
                year_level_id: selectedYearLevel.value,
                section_id: selectedSection.value,
                subject_id: selectedSubject.value,
                type: selectedType.value,
            },
            {
                preserveScroll: true,
                preserveState: true,
                only: ['modules'],
            },
        );
    }
});

watch(selectedYearLevel, () => {
    selectedSection.value = null;
    selectedSubject.value = null;
    selectedModule.value = null;
});

watch(selectedSection, () => {
    selectedSubject.value = null;
    selectedModule.value = null;
});

watch(
    () => props.modules,
    (newModules) => {
        if (newModules.length > 0) {
            selectedModule.value = newModules[0].id;
        }
    },
);

const applyFilters = () => {
    isLoading.value = true;
    filtersApplied.value = false;

    router.get(
        '/students-proficiency',
        {
            year_level_id: selectedYearLevel.value,
            section_id: selectedSection.value,
            subject_id: selectedSubject.value,
            module_id: selectedModule.value,
            type: selectedType.value,
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                filtersApplied.value = true;
            },
            onFinish: () => {
                setTimeout(() => {
                    isLoading.value = false;
                }, 2000);
            },
        },
    );
};

const pdfUrl = computed(() => {
    return `/students-proficiency/pdf?year_level_id=${selectedYearLevel.value ?? ''}&section_id=${selectedSection.value ?? ''}&subject_id=${selectedSubject.value ?? ''}&module_id=${selectedModule.value ?? ''}&type=${selectedType.value}`;
});

const canGeneratePdf = computed(() => {
    return filtersApplied.value && !!selectedSubject.value && !!selectedModule.value && props.resultsByActivity.length > 0;
});
</script>

<template>
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
            <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
                <h1 class="flex items-center gap-2 text-2xl font-bold text-[#01006c]">📊 Proficiency Report</h1>
            </div>

            <div class="grid grid-cols-1 items-end gap-4 sm:grid-cols-2 md:grid-cols-4 xl:grid-cols-6 2xl:grid-cols-7">
                <div>
                    <label class="flex items-center gap-1 text-sm font-semibold text-[#01006c]"> <GraduationCap class="h-4 w-4" /> Year Level </label>
                    <select
                        v-model="selectedYearLevel"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 text-sm focus:border-[#ffc60b] focus:ring-[#ffc60b]"
                    >
                        <option :value="null">Select Year Level</option>
                        <option v-for="y in props.yearLevels" :key="y.id" :value="y.id">{{ y.name }}</option>
                    </select>
                </div>

                <div>
                    <label class="flex items-center gap-1 text-sm font-semibold text-[#01006c]"> <Users class="h-4 w-4" /> Section </label>
                    <select
                        v-model="selectedSection"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 text-sm focus:border-[#ffc60b] focus:ring-[#ffc60b]"
                    >
                        <option :value="null">Select Section</option>
                        <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                </div>

                <div>
                    <label class="flex items-center gap-1 text-sm font-semibold text-[#01006c]"> <Book class="h-4 w-4" /> Subject </label>
                    <select
                        v-model="selectedSubject"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 text-sm focus:border-[#ffc60b] focus:ring-[#ffc60b]"
                    >
                        <option :value="null">Select Subject</option>
                        <option v-for="sub in filteredSubjects" :key="sub.id" :value="sub.id">{{ sub.name }}</option>
                    </select>
                </div>

                <div>
                    <label class="flex items-center gap-1 text-sm font-semibold text-[#01006c]"> <Layers class="h-4 w-4" /> Module </label>
                    <select
                        v-model="selectedModule"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 text-sm focus:border-[#ffc60b] focus:ring-[#ffc60b]"
                    >
                        <option :value="null">Select Module</option>
                        <option v-for="m in props.modules" :key="m.id" :value="m.id">{{ m.name }}</option>
                    </select>
                </div>

                <div>
                    <label class="flex items-center gap-1 text-sm font-semibold text-[#01006c]"> <ListChecks class="h-4 w-4" /> Test Type </label>
                    <select
                        v-model="selectedType"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 text-sm focus:border-[#ffc60b] focus:ring-[#ffc60b]"
                    >
                        <option value="quiz">Quiz</option>
                        <option value="exam">Exam</option>
                    </select>
                </div>

                <div>
                    <button @click="applyFilters" class="w-full rounded bg-[#01006c] px-4 py-2 text-sm font-medium text-white hover:bg-[#0d1282]">
                        Generate
                    </button>
                </div>
            </div>

            <div v-if="canGeneratePdf" class="mt-4 flex justify-end gap-2">
                <a
                    :href="pdfUrl"
                    target="_blank"
                    class="inline-flex items-center gap-2 rounded bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700"
                >
                    🧾 Generate PDF
                </a>
                <!--
                <a
                    :href="pdfUrl.replace('/pdf', '/upload')"
                    target="_blank"
                    class="inline-flex items-center gap-2 rounded bg-green-600 px-4 py-2 text-sm text-white hover:bg-green-700"
                >
                    ☁️ Upload to Google Sheets
                </a>
                -->
            </div>

            <div v-if="props.resultsByActivity.length > 0" class="mt-8 space-y-10">
                <div v-for="group in props.resultsByActivity" :key="group.activity_title">
                    <h2 class="text-xl font-semibold text-[#01006c]">{{ group.activity_title }}</h2>
                    <div class="overflow-x-auto rounded-lg border border-[#01006c] bg-white">
                        <table class="min-w-full table-auto text-left text-sm">
                            <thead class="bg-[#01006c] text-white">
                                <tr>
                                    <th class="p-3">Student</th>
                                    <th class="p-3">Score</th>
                                    <th class="p-3">Total</th>
                                    <th class="p-3">Average (%)</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-gray-700">
                                <tr v-for="entry in group.entries" :key="entry.student" class="border-t hover:bg-gray-50">
                                    <td class="p-3 align-top text-[#01006c]">{{ entry.student }}</td>
                                    <td class="p-3 align-top">{{ entry.score }}</td>
                                    <td class="p-3 align-top">{{ entry.total }}</td>
                                    <td class="p-3 align-top">{{ entry.average }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div v-else class="mt-10 text-center text-gray-500 italic">No data to show.</div>
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
