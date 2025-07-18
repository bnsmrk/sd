<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { BarChart2, BookOpen, ClipboardList, FileText, Layers, LayoutTemplate, Search } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const isLoading = computed(() => isCreating.value || isUpdating.value || isDeleting.value);
const isCreating = ref(false);
const isUpdating = ref(false);
const isDeleting = ref(false);
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
    isCreating.value = true;
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
            onFinish: () => {
                setTimeout(() => {
                    isCreating.value = false;
                }, 1000);
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

const sortState = ref<Record<string, { key: keyof ResultEntry; asc: boolean }>>({});

function toggleSort(activityTitle: string, key: keyof ResultEntry) {
    const current = sortState.value[activityTitle] || { key, asc: true };

    if (current.key === key) {
        current.asc = !current.asc;
    } else {
        current.key = key;
        current.asc = true;
    }

    sortState.value[activityTitle] = current;
}

function getSortedEntries(activity: GroupedResult): ResultEntry[] {
    const sort = sortState.value[activity.activity_title];
    if (!sort) return activity.entries;

    return [...activity.entries].sort((a, b) => {
        const aVal = a[sort.key];
        const bVal = b[sort.key];

        if (typeof aVal === 'number' && typeof bVal === 'number') {
            return sort.asc ? aVal - bVal : bVal - aVal;
        } else {
            return sort.asc ? String(aVal).localeCompare(String(bVal)) : String(bVal).localeCompare(String(aVal));
        }
    });
}
</script>
<template>
    <Head title="Proficiency Report" />
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
        <div class="mx-auto w-full max-w-7xl space-y-6 p-6">
            <h1 class="flex items-center gap-2 text-2xl font-bold text-[#01006c]"><BarChart2 class="h-6 w-6" /> Students Activities Reports</h1>

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

                <div class="flex-shrink-0">
                    <button
                        @click="applyFilters"
                        class="inline-flex items-center gap-2 rounded bg-[#01006c] px-4 py-2 text-sm text-white hover:bg-[#0d1282]"
                    >
                        <Search class="h-4 w-4" /> Generate Report
                    </button>
                </div>
            </div>

            <div v-if="canGeneratePdf" class="mt-4 flex justify-end">
                <a
                    :href="pdfUrl"
                    target="_blank"
                    class="inline-flex items-center gap-2 rounded bg-[#ff69b4] px-4 py-2 text-sm text-white hover:bg-[#e85aa7]"
                >
                    <FileText class="h-4 w-4" /> Generate PDF
                </a>
            </div>

            <div v-if="props.resultsByActivity.length > 0" class="mt-6 space-y-8">
                <div v-for="group in props.resultsByActivity" :key="group.activity_title">
                    <h2 class="text-lg font-semibold text-[#01006c]">{{ group.activity_title }}</h2>
                    <table class="mt-2 min-w-full border text-sm">
                        <thead class="bg-[#01006c] text-left text-white">
                            <tr>
                                <th class="cursor-pointer border px-4 py-2" @click="toggleSort(group.activity_title, 'student')">
                                    Student
                                    <span v-if="sortState[group.activity_title]?.key === 'student'">
                                        {{ sortState[group.activity_title]?.asc ? '↑' : '↓' }}
                                    </span>
                                </th>
                                <th class="cursor-pointer border px-4 py-2" @click="toggleSort(group.activity_title, 'score')">
                                    Score
                                    <span v-if="sortState[group.activity_title]?.key === 'score'">
                                        {{ sortState[group.activity_title]?.asc ? '↑' : '↓' }}
                                    </span>
                                </th>
                                <th class="cursor-pointer border px-4 py-2" @click="toggleSort(group.activity_title, 'total')">
                                    Total
                                    <span v-if="sortState[group.activity_title]?.key === 'total'">
                                        {{ sortState[group.activity_title]?.asc ? '↑' : '↓' }}
                                    </span>
                                </th>
                                <th class="cursor-pointer border px-4 py-2" @click="toggleSort(group.activity_title, 'average')">
                                    Average (%)
                                    <span v-if="sortState[group.activity_title]?.key === 'average'">
                                        {{ sortState[group.activity_title]?.asc ? '↑' : '↓' }}
                                    </span>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="entry in getSortedEntries(group)" :key="entry.student" class="bg-white">
                                <td class="border px-4 py-2 text-[#01006c]">{{ entry.student }}</td>
                                <td class="border px-4 py-2 text-[#01006c]">{{ entry.score }}</td>
                                <td class="border px-4 py-2 text-[#01006c]">{{ entry.total }}</td>
                                <td class="border px-4 py-2 text-[#01006c]">{{ entry.average }}%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div v-else class="mt-8 text-center text-gray-400 italic">No data to show</div>
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
