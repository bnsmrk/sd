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
                }, 800);
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

const sortBy = ref<'student' | 'score' | 'total' | 'average'>('student');
const sortAsc = ref(true);

const toggleSort = (key: 'student' | 'score' | 'total' | 'average') => {
    if (sortBy.value === key) {
        sortAsc.value = !sortAsc.value;
    } else {
        sortBy.value = key;
        sortAsc.value = true;
    }
};

const getSortedEntries = (entries: ResultEntry[]) => {
    return [...entries].sort((a, b) => {
        let aVal = a[sortBy.value];
        let bVal = b[sortBy.value];

        if (typeof aVal === 'string' && typeof bVal === 'string') {
            aVal = aVal.toLowerCase();
            bVal = bVal.toLowerCase();
        }

        if (aVal < bVal) return sortAsc.value ? -1 : 1;
        if (aVal > bVal) return sortAsc.value ? 1 : -1;
        return 0;
    });
};
</script>

<template>
    <AppLayout>
        <div v-if="isLoading" class="fixed inset-0 z-50 flex items-center justify-center bg-white/30 backdrop-blur-sm">
            <div class="flex flex-col items-center gap-4">
                <div class="relative h-16 w-16">
                    <div class="animate-spin-slow-cw absolute inset-0 rounded-full border-4 border-blue-500 border-t-transparent"></div>
                    <div class="animate-spin-slow-ccw absolute inset-2 rounded-full border-4 border-yellow-500 border-t-transparent"></div>
                    <div class="animate-spin-fast-cw absolute inset-4 rounded-full border-4 border-pink-700 border-t-transparent"></div>
                </div>
                <div class="text-center">
                    <span class="block animate-pulse text-base font-semibold text-pink-500">Processing Request...</span>
                    <span class="text-xs text-pink-500/70">This may take a moment</span>
                </div>
            </div>
        </div>

        <div class="mx-auto w-full max-w-screen-xl space-y-6 px-6 py-8">
            <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
                <h1 class="flex items-center gap-2 text-2xl font-bold text-pink-400">Students Activities Results</h1>
            </div>

            <div class="grid grid-cols-1 items-end gap-4 sm:grid-cols-2 md:grid-cols-4 xl:grid-cols-6 2xl:grid-cols-7">
                <div>
                    <label class="flex items-center gap-1 text-sm font-semibold text-pink-400"> <GraduationCap class="h-4 w-4" /> Year Level </label>
                    <select
                        v-model="selectedYearLevel"
                        class="w-full rounded border border-pink-400 bg-white px-3 py-2 text-sm focus:border-pink-400 focus:ring-pink-400"
                    >
                        <option :value="null">Select Year Level</option>
                        <option v-for="y in props.yearLevels" :key="y.id" :value="y.id">{{ y.name }}</option>
                    </select>
                </div>

                <div>
                    <label class="flex items-center gap-1 text-sm font-semibold text-pink-400"> <Users class="h-4 w-4" /> Section </label>
                    <select
                        v-model="selectedSection"
                        class="w-full rounded border border-pink-500 bg-white px-3 py-2 text-sm focus:border-pink-400 focus:ring-pink-400"
                    >
                        <option :value="null">Select Section</option>
                        <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                </div>

                <div>
                    <label class="flex items-center gap-1 text-sm font-semibold text-pink-400"> <Book class="h-4 w-4" /> Subject </label>
                    <select
                        v-model="selectedSubject"
                        class="w-full rounded border border-pink-500 bg-white px-3 py-2 text-sm focus:border-pink-400 focus:ring-pink-400"
                    >
                        <option :value="null">Select Subject</option>
                        <option v-for="sub in filteredSubjects" :key="sub.id" :value="sub.id">{{ sub.name }}</option>
                    </select>
                </div>

                <div>
                    <label class="flex items-center gap-1 text-sm font-semibold text-pink-400"> <Layers class="h-4 w-4" /> Module </label>
                    <select
                        v-model="selectedModule"
                        class="w-full rounded border border-pink-500 bg-white px-3 py-2 text-sm focus:border-pink-400 focus:ring-pink-400"
                    >
                        <option :value="null">Select Module</option>
                        <option v-for="m in props.modules" :key="m.id" :value="m.id">{{ m.name }}</option>
                    </select>
                </div>

                <div>
                    <label class="flex items-center gap-1 text-sm font-semibold text-pink-400"> <ListChecks class="h-4 w-4" /> Test Type </label>
                    <select
                        v-model="selectedType"
                        class="w-full rounded border border-pink-400 bg-white px-3 py-2 text-sm focus:border-pink-400 focus:ring-pink-400"
                    >
                        <option value="quiz">Quiz</option>
                        <option value="exam">Exam</option>
                    </select>
                </div>

                <div>
                    <button @click="applyFilters" class="w-full rounded bg-pink-400 px-4 py-2 text-sm font-medium text-white hover:bg-pink-500">
                        Generate
                    </button>
                </div>
            </div>

            <div v-if="canGeneratePdf" class="mt-4 flex justify-end gap-2">
                <a
                    :href="pdfUrl"
                    target="_blank"
                    class="inline-flex items-center gap-2 rounded bg-pink-400 px-4 py-2 text-sm text-white hover:bg-pink-500"
                >
                    Generate PDF
                </a>
            </div>

            <div v-if="props.resultsByActivity.length > 0" class="mt-8 space-y-10">
                <div v-for="group in props.resultsByActivity" :key="group.activity_title">
                    <h2 class="text-xl font-semibold text-pink-400">{{ group.activity_title }}</h2>

                    <div class="overflow-x-auto rounded-lg border border-pink-200 shadow">
                        <table class="text-pink-00 min-w-full table-auto text-left text-sm">
                            <thead class="bg-pink-100 text-xs font-semibold text-pink-400 uppercase">
                                <tr>
                                    <th class="cursor-pointer p-3" @click="toggleSort('student')">
                                        Student <span v-if="sortBy === 'student'">{{ sortAsc ? '↑' : '↓' }}</span>
                                    </th>
                                    <th class="cursor-pointer p-3" @click="toggleSort('score')">
                                        Score <span v-if="sortBy === 'score'">{{ sortAsc ? '↑' : '↓' }}</span>
                                    </th>
                                    <th class="cursor-pointer p-3" @click="toggleSort('total')">
                                        Total <span v-if="sortBy === 'total'">{{ sortAsc ? '↑' : '↓' }}</span>
                                    </th>
                                    <th class="cursor-pointer p-3" @click="toggleSort('average')">
                                        Average (%) <span v-if="sortBy === 'average'">{{ sortAsc ? '↑' : '↓' }}</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-pink-800">
                                <tr v-for="entry in getSortedEntries(group.entries)" :key="entry.student" class="border-t hover:bg-pink-50">
                                    <td class="p-3 align-top text-[#01006c]">{{ entry.student }}</td>
                                    <td class="p-3 align-top text-[#01006c]">{{ entry.score }}</td>
                                    <td class="p-3 align-top text-[#01006c]">{{ entry.total }}</td>
                                    <td class="p-3 align-top text-[#01006c]">{{ entry.average }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div v-else class="mt-10 text-center text-sm text-pink-400 italic">No data to show.</div>
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
