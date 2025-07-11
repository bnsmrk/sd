<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { BarChart2, BookOpen, ClipboardList, FileText, Layers, LayoutTemplate, Search } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

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
    year_level_id: number;
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
const filteredSubjects = computed(() => {
    if (!selectedYearLevel.value && !selectedSection.value) {
        return props.subjects;
    }

    return props.subjects.filter((s) => {
        const matchesYearLevel = !selectedYearLevel.value || s.year_level_id === selectedYearLevel.value;
        const matchesSection = !selectedSection.value || s.section_id === selectedSection.value || s.section_id === null;
        return matchesYearLevel && matchesSection;
    });
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

watch(selectedSubject, (subjectId) => {
    if (subjectId) {
        selectedModule.value = null;
        router.get(
            '/principal-students-proficiency',
            {
                year_level_id: selectedYearLevel.value,
                section_id: selectedSection.value,
                subject_id: subjectId,
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

watch(
    () => props.modules,
    (newModules) => {
        if (newModules.length > 0) {
            selectedModule.value = newModules[0].id;
        }
    },
);
watch(selectedSection, (newSection) => {
    console.log('Selected Section: ', newSection);
});
console.log(
    selectedSection.value,
    props.subjects.map((s) => s.section_id),
);

const applyFilters = () => {
    filtersApplied.value = false;
    router.get(
        '/principal-students-proficiency',
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
        },
    );
};

const canGeneratePdf = computed(() => {
    return filtersApplied.value && !!selectedSubject.value && !!selectedModule.value && props.resultsByActivity.length > 0;
});

const pdfUrl = computed(() => {
    return `/principal-students-proficiency/pdf?year_level_id=${selectedYearLevel.value ?? ''}&section_id=${selectedSection.value ?? ''}&subject_id=${selectedSubject.value ?? ''}&module_id=${selectedModule.value ?? ''}&type=${selectedType.value}`;
});
</script>
<template>
    <Head title="Proficiency Report" />
    <AppLayout>
        <div class="mx-auto w-full max-w-7xl space-y-6 p-6">
            <!-- Header -->
            <h1 class="flex items-center gap-2 text-2xl font-bold text-[#01006c]"><BarChart2 class="h-6 w-6" /> Proficiency Reports</h1>

            <!-- Filters -->
            <div class="flex flex-wrap gap-4 rounded-lg border bg-white p-4">
                <div class="relative min-w-[180px] flex-grow">
                    <Layers class="absolute top-2.5 left-3 h-4 w-4 text-[#01006c]" />
                    <select
                        v-model="selectedYearLevel"
                        class="w-full rounded border border-[#01006c] bg-white py-2 pr-3 pl-10 text-sm focus:border-[#ffc60b] focus:outline-none"
                    >
                        <option :value="null">Select Year Level</option>
                        <option v-for="y in props.yearLevels" :key="y.id" :value="y.id">{{ y.name }}</option>
                    </select>
                </div>

                <div class="relative min-w-[180px] flex-grow">
                    <LayoutTemplate class="absolute top-2.5 left-3 h-4 w-4 text-[#01006c]" />
                    <select
                        v-model="selectedSection"
                        class="w-full rounded border border-[#01006c] bg-white py-2 pr-3 pl-10 text-sm focus:border-[#ffc60b] focus:outline-none"
                    >
                        <option :value="null">Select Section</option>
                        <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                </div>

                <div class="relative min-w-[180px] flex-grow">
                    <BookOpen class="absolute top-2.5 left-3 h-4 w-4 text-[#01006c]" />
                    <select
                        v-model="selectedSubject"
                        class="w-full rounded border border-[#01006c] bg-white py-2 pr-3 pl-10 text-sm focus:border-[#ffc60b] focus:outline-none"
                    >
                        <option :value="null">Select Subject</option>
                        <option v-for="sub in filteredSubjects" :key="sub.id" :value="sub.id">{{ sub.name }}</option>
                    </select>
                </div>

                <div class="relative min-w-[180px] flex-grow">
                    <FileText class="absolute top-2.5 left-3 h-4 w-4 text-[#01006c]" />
                    <select
                        v-model="selectedModule"
                        class="w-full rounded border border-[#01006c] bg-white py-2 pr-3 pl-10 text-sm focus:border-[#ffc60b] focus:outline-none"
                    >
                        <option :value="null">Select Module</option>
                        <option v-for="m in props.modules" :key="m.id" :value="m.id">{{ m.name }}</option>
                    </select>
                </div>

                <div class="relative min-w-[160px] flex-grow">
                    <ClipboardList class="absolute top-2.5 left-3 h-4 w-4 text-[#01006c]" />
                    <select
                        v-model="selectedType"
                        class="w-full rounded border border-[#01006c] bg-white py-2 pr-3 pl-10 text-sm focus:border-[#ffc60b] focus:outline-none"
                    >
                        <option value="quiz">Quiz</option>
                        <option value="exam">Exam</option>
                    </select>
                </div>

                <!-- Generate Button -->
                <div class="flex-shrink-0">
                    <button
                        @click="applyFilters"
                        class="inline-flex items-center gap-2 rounded bg-[#01006c] px-4 py-2 text-sm text-white hover:bg-[#0d1282]"
                    >
                        <Search class="h-4 w-4" /> Generate Report
                    </button>
                </div>
            </div>

            <!-- PDF Button -->
            <div v-if="canGeneratePdf" class="mt-4 flex justify-end">
                <a
                    :href="pdfUrl"
                    target="_blank"
                    class="inline-flex items-center gap-2 rounded bg-[#ff69b4] px-4 py-2 text-sm text-white hover:bg-[#e85aa7]"
                >
                    <FileText class="h-4 w-4" /> Generate PDF
                </a>
            </div>

            <!-- Results Table -->
            <div v-if="props.resultsByActivity.length > 0" class="mt-6 space-y-8">
                <div v-for="group in props.resultsByActivity" :key="group.activity_title">
                    <h2 class="text-lg font-semibold text-[#01006c]">{{ group.activity_title }}</h2>
                    <table class="mt-2 min-w-full border text-sm">
                        <thead class="bg-[#01006c] text-left text-white">
                            <tr>
                                <th class="border px-4 py-2">Student</th>
                                <th class="border px-4 py-2">Score</th>
                                <th class="border px-4 py-2">Total</th>
                                <th class="border px-4 py-2">Average (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="entry in group.entries" :key="entry.student" class="bg-white">
                                <td class="border px-4 py-2 text-[#01006c]">{{ entry.student }}</td>
                                <td class="border px-4 py-2 text-[#01006c]">{{ entry.score }}</td>
                                <td class="border px-4 py-2 text-[#01006c]">{{ entry.total }}</td>
                                <td class="border px-4 py-2 text-[#01006c]">{{ entry.average }}%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- No Results -->
            <div v-else class="mt-8 text-center text-gray-400 italic">No data to show</div>
        </div>
    </AppLayout>
</template>
