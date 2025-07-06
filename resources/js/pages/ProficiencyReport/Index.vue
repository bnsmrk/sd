<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
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

// Filtered dropdown lists
const filteredSections = computed(() => props.sections.filter((s) => s.year_level_id === selectedYearLevel.value));
const filteredSubjects = computed(() => props.subjects.filter((s) => s.section_id === selectedSection.value));

// Fetch modules when subject changes
watch(selectedSubject, () => {
    selectedModule.value = null; // reset module when subject changes
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

// Reset cascade on selection change
watch(selectedYearLevel, () => {
    selectedSection.value = null;
    selectedSubject.value = null;
    selectedModule.value = null;
});
watch(selectedSection, () => {
    selectedSubject.value = null;
    selectedModule.value = null;
});

// Automatically select first module after fetch
watch(
    () => props.modules,
    (newModules) => {
        if (newModules.length > 0) {
            selectedModule.value = newModules[0].id;
        }
    },
);

// Apply filters
const applyFilters = () => {
    filtersApplied.value = false; // Reset before request
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
        },
    );
};

// Computed URL for PDF
const pdfUrl = computed(() => {
    return `/students-proficiency/pdf?year_level_id=${selectedYearLevel.value ?? ''}&section_id=${selectedSection.value ?? ''}&subject_id=${selectedSubject.value ?? ''}&module_id=${selectedModule.value ?? ''}&type=${selectedType.value ?? 'quiz'}`;
});

// Whether to show the PDF button
const canGeneratePdf = computed(() => {
    return filtersApplied.value && !!selectedSubject.value && !!selectedModule.value && props.resultsByActivity.length > 0;
});
</script>

<template>
    <AppLayout>
        <div class="space-y-6 p-6">
            <h1 class="text-2xl font-bold">Proficiency Report</h1>

            <!-- Filters -->
            <!-- <div>
                <p><strong>Subject:</strong> {{ selectedSubject }}</p>
                <p><strong>Module:</strong> {{ selectedModule }}</p>
                <p><strong>Can Generate PDF?:</strong> {{ canGeneratePdf }}</p>
            </div> -->

            <div class="flex flex-wrap gap-4">
                <select v-model="selectedYearLevel" class="rounded border px-3 py-2">
                    <option :value="null">Select Year Level</option>
                    <option v-for="y in props.yearLevels" :key="y.id" :value="y.id">{{ y.name }}</option>
                </select>

                <select v-model="selectedSection" class="rounded border px-3 py-2">
                    <option :value="null">Select Section</option>
                    <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>

                <select v-model="selectedSubject" class="rounded border px-3 py-2">
                    <option :value="null">Select Subject</option>
                    <option v-for="sub in filteredSubjects" :key="sub.id" :value="sub.id">{{ sub.name }}</option>
                </select>

                <select v-model="selectedModule" class="rounded border px-3 py-2">
                    <option :value="null">Select Module</option>
                    <option v-for="m in props.modules" :key="m.id" :value="m.id">{{ m.name }}</option>
                </select>

                <select v-model="selectedType" class="rounded border px-3 py-2">
                    <option value="quiz">Quiz</option>
                    <option value="exam">Exam</option>
                </select>

                <button @click="applyFilters" class="rounded bg-blue-600 px-4 py-2 text-white">Generate Report</button>
            </div>

            <!-- PDF Button -->
            <!-- PDF Button -->
            <div v-if="props.resultsByActivity.length > 0 && canGeneratePdf" class="mt-4 flex justify-end">
                <a :href="pdfUrl" target="_blank" class="rounded bg-red-600 px-4 py-2 text-white">Generate PDF</a>
            </div>

            <!-- Results Table -->
            <div v-if="props.resultsByActivity.length > 0" class="mt-6 space-y-8">
                <div v-for="group in props.resultsByActivity" :key="group.activity_title">
                    <h2 class="text-lg font-semibold text-blue-700">{{ group.activity_title }}</h2>
                    <table class="mt-2 min-w-full border">
                        <thead class="bg-gray-100 text-left">
                            <tr>
                                <th class="border px-4 py-2">Student</th>
                                <th class="border px-4 py-2">Score</th>
                                <th class="border px-4 py-2">Total</th>
                                <th class="border px-4 py-2">Average (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="entry in group.entries" :key="entry.student">
                                <td class="border px-4 py-2">{{ entry.student }}</td>
                                <td class="border px-4 py-2">{{ entry.score }}</td>
                                <td class="border px-4 py-2">{{ entry.total }}</td>
                                <td class="border px-4 py-2">{{ entry.average }}%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div v-else class="mt-8 text-center text-gray-500 italic">No data to show</div>
        </div>
    </AppLayout>
</template>
