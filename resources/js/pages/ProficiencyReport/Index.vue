<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { Book, GraduationCap, Layers, ListChecks, Users } from 'lucide-vue-next';
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
            onSuccess: () => (filtersApplied.value = true),
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
        <div class="mx-auto max-w-full space-y-6 px-6 py-8">
            <h1 class="text-3xl font-bold text-gray-800">📊 Proficiency Report</h1>

            <!-- Filter Row -->
            <div class="grid grid-cols-1 items-end gap-4 sm:grid-cols-2 md:grid-cols-4 xl:grid-cols-6 2xl:grid-cols-7">
                <!-- Year Level -->
                <div>
                    <label class="flex items-center gap-1 text-sm font-semibold text-gray-700"> <GraduationCap class="h-4 w-4" /> Year Level </label>
                    <select v-model="selectedYearLevel" class="w-full rounded border px-3 py-2">
                        <option :value="null">Select Year Level</option>
                        <option v-for="y in props.yearLevels" :key="y.id" :value="y.id">{{ y.name }}</option>
                    </select>
                </div>

                <!-- Section -->
                <div>
                    <label class="flex items-center gap-1 text-sm font-semibold text-gray-700"> <Users class="h-4 w-4" /> Section </label>
                    <select v-model="selectedSection" class="w-full rounded border px-3 py-2">
                        <option :value="null">Select Section</option>
                        <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                </div>

                <!-- Subject -->
                <div>
                    <label class="flex items-center gap-1 text-sm font-semibold text-gray-700"> <Book class="h-4 w-4" /> Subject </label>
                    <select v-model="selectedSubject" class="w-full rounded border px-3 py-2">
                        <option :value="null">Select Subject</option>
                        <option v-for="sub in filteredSubjects" :key="sub.id" :value="sub.id">{{ sub.name }}</option>
                    </select>
                </div>

                <!-- Module -->
                <div>
                    <label class="flex items-center gap-1 text-sm font-semibold text-gray-700"> <Layers class="h-4 w-4" /> Module </label>
                    <select v-model="selectedModule" class="w-full rounded border px-3 py-2">
                        <option :value="null">Select Module</option>
                        <option v-for="m in props.modules" :key="m.id" :value="m.id">{{ m.name }}</option>
                    </select>
                </div>

                <!-- Type -->
                <div>
                    <label class="flex items-center gap-1 text-sm font-semibold text-gray-700"> <ListChecks class="h-4 w-4" /> Test Type </label>
                    <select v-model="selectedType" class="w-full rounded border px-3 py-2">
                        <option value="quiz">Quiz</option>
                        <option value="exam">Exam</option>
                    </select>
                </div>

                <!-- Generate Button -->
                <div>
                    <button @click="applyFilters" class="w-full rounded bg-blue-600 px-4 py-2 whitespace-nowrap text-white hover:bg-blue-700">
                        Generate
                    </button>
                </div>
            </div>

            <!-- PDF Button -->
            <div v-if="canGeneratePdf" class="mt-4 flex justify-end">
                <a :href="pdfUrl" target="_blank" class="inline-flex items-center gap-2 rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                    🧾 Generate PDF
                </a>
            </div>

            <!-- Results -->
            <div v-if="props.resultsByActivity.length > 0" class="mt-8 space-y-10">
                <div v-for="group in props.resultsByActivity" :key="group.activity_title">
                    <h2 class="text-xl font-semibold text-blue-700">{{ group.activity_title }}</h2>
                    <table class="mt-2 w-full border text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-4 py-2 text-left">Student</th>
                                <th class="border px-4 py-2 text-left">Score</th>
                                <th class="border px-4 py-2 text-left">Total</th>
                                <th class="border px-4 py-2 text-left">Average (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="entry in group.entries" :key="entry.student" class="hover:bg-gray-50">
                                <td class="border px-4 py-2">{{ entry.student }}</td>
                                <td class="border px-4 py-2">{{ entry.score }}</td>
                                <td class="border px-4 py-2">{{ entry.total }}</td>
                                <td class="border px-4 py-2">{{ entry.average }}%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div v-else class="mt-10 text-center text-gray-500 italic">No data to show.</div>
        </div>
    </AppLayout>
</template>
