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
interface Result {
    student: string;
    score: number;
    total: number;
    average: number;
}

const props = defineProps<{
    yearLevels: YearLevel[];
    sections: Section[];
    subjects: Subject[];
    modules: Module[];
    results: Result[];
    filters: {
        year_level_id: number | null;
        section_id: number | null;
        subject_id: number | null;
        module_id: number | null;
        type: string;
    };
}>();

const selectedYearLevel = ref<number | null>(props.filters.year_level_id);
const selectedSection = ref<number | null>(props.filters.section_id);
const selectedSubject = ref<number | null>(props.filters.subject_id);
const selectedModule = ref<number | null>(props.filters.module_id);
const selectedType = ref<string>(props.filters.type || 'quiz');

// Filtered sections based on year level
const filteredSections = computed(() => {
    return props.sections.filter((s) => s.year_level_id === selectedYearLevel.value);
});

// Filtered subjects based on section
const filteredSubjects = computed(() => {
    return props.subjects.filter((s) => s.section_id === selectedSection.value);
});

// Watch subject change → fetch modules
watch(selectedSubject, () => {
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

// Reset dropdowns on hierarchy change
watch(selectedYearLevel, () => {
    selectedSection.value = null;
    selectedSubject.value = null;
    selectedModule.value = null;
});
watch(selectedSection, () => {
    selectedSubject.value = null;
    selectedModule.value = null;
});
watch(selectedSubject, () => {
    selectedModule.value = null;
});

// Apply filter
const applyFilters = () => {
    router.get('/students-proficiency', {
        year_level_id: selectedYearLevel.value,
        section_id: selectedSection.value,
        subject_id: selectedSubject.value,
        module_id: selectedModule.value,
        type: selectedType.value,
    });
};
</script>

<template>
    <AppLayout>
        <div class="space-y-6 p-6">
            <h1 class="text-2xl font-bold">Proficiency Report</h1>

            <!-- Filters -->
            <div class="flex flex-wrap gap-4">
                <!-- Year Level -->
                <select v-model="selectedYearLevel" class="rounded border px-3 py-2">
                    <option :value="null">Select Year Level</option>
                    <option v-for="y in props.yearLevels" :key="y.id" :value="y.id">
                        {{ y.name }}
                    </option>
                </select>

                <!-- Section -->
                <select v-model="selectedSection" class="rounded border px-3 py-2">
                    <option :value="null">Select Section</option>
                    <option v-for="s in filteredSections" :key="s.id" :value="s.id">
                        {{ s.name }}
                    </option>
                </select>

                <!-- Subject -->
                <select v-model="selectedSubject" class="rounded border px-3 py-2">
                    <option :value="null">Select Subject</option>
                    <option v-for="sub in filteredSubjects" :key="sub.id" :value="sub.id">
                        {{ sub.name }}
                    </option>
                </select>

                <!-- Module -->
                <select v-model="selectedModule" class="rounded border px-3 py-2">
                    <option :value="null">Select Module</option>
                    <option v-for="m in props.modules" :key="m.id" :value="m.id">
                        {{ m.name }}
                    </option>
                </select>

                <!-- Type -->
                <select v-model="selectedType" class="rounded border px-3 py-2">
                    <option value="quiz">Quiz</option>
                    <option value="exam">Exam</option>
                </select>

                <button @click="applyFilters" class="rounded bg-blue-600 px-4 py-2 text-white">Generate Report</button>
            </div>

            <!-- Results Table -->
            <table class="mt-6 min-w-full border">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="border px-4 py-2">Student</th>
                        <th class="border px-4 py-2">Score</th>
                        <th class="border px-4 py-2">Total</th>
                        <th class="border px-4 py-2">Average (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="res in props.results" :key="res.student">
                        <td class="border px-4 py-2">{{ res.student }}</td>
                        <td class="border px-4 py-2">{{ res.score }}</td>
                        <td class="border px-4 py-2">{{ res.total }}</td>
                        <td class="border px-4 py-2">{{ res.average }}%</td>
                    </tr>
                    <tr v-if="props.results.length === 0">
                        <td colspan="4" class="py-4 text-center text-gray-500 italic">No data to show</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
