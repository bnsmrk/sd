<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface YearLevel {
    id: number;
    name: string;
}
interface Section {
    id: number;
    name: string;
    year_level_id: number;
}
interface Individual {
    student: {
        id: number;
        name: string;
        section: Section | null;
    };
    average: number;
}
interface SectionAverage {
    section: Section;
    average: number;
}

const props = defineProps<{
    yearLevels: YearLevel[];
    sections: Section[];
    individuals: Individual[];
    sectionsAvg: SectionAverage[];
    yearLevelAverage: number | null;
    filters: {
        year_level_id: number | null;
        section_id: number | null;
        type: string;
    };
}>();

const selectedYearLevel = ref<number | null>(props.filters.year_level_id);
const selectedSection = ref<number | null>(props.filters.section_id);
const selectedType = ref<string>(props.filters.type || '');

const filteredSections = computed(() => props.sections.filter((s) => s.year_level_id === selectedYearLevel.value));

const filtersApplied = ref(false);

const applyFilters = () => {
    filtersApplied.value = false;
    router.get(
        '/proficiency-result',
        {
            year_level_id: selectedYearLevel.value,
            section_id: selectedSection.value,
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
</script>

<template>
    <AppLayout>
        <div class="space-y-6 p-6">
            <h1 class="text-2xl font-bold text-blue-800">Proficiency Report</h1>

            <!-- Filters -->
            <div class="flex flex-wrap gap-4">
                <select v-model="selectedYearLevel" class="rounded border px-3 py-2">
                    <option :value="null">Select Year Level</option>
                    <option v-for="y in props.yearLevels" :key="y.id" :value="y.id">{{ y.name }}</option>
                </select>

                <select v-model="selectedSection" class="rounded border px-3 py-2">
                    <option :value="null">Select Section</option>
                    <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>

                <select v-model="selectedType" class="rounded border px-3 py-2">
                    <option value="">Select Type</option>
                    <option value="reading">Reading</option>
                    <option value="numerical">Numerical</option>
                </select>

                <button @click="applyFilters" class="rounded bg-blue-600 px-4 py-2 text-white">Generate Report</button>
            </div>

            <!-- Results -->
            <div v-if="filtersApplied && props.individuals.length > 0" class="mt-6 space-y-6">
                <!-- Individual -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-700">Individual Averages</h2>
                    <table class="mt-2 w-full border text-left">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2">Student</th>
                                <th class="border px-3 py-2">Section</th>
                                <th class="border px-3 py-2">Average (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="i in props.individuals" :key="i.student.id">
                                <td class="border px-3 py-2">{{ i.student.name }}</td>
                                <td class="border px-3 py-2">{{ i.student.section?.name ?? 'N/A' }}</td>
                                <td class="border px-3 py-2">{{ i.average }}%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Section -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-700">Section Averages</h2>
                    <table class="mt-2 w-full border text-left">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2">Section</th>
                                <th class="border px-3 py-2">Average (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="s in props.sectionsAvg" :key="s.section.id">
                                <td class="border px-3 py-2">{{ s.section.name }}</td>
                                <td class="border px-3 py-2">{{ s.average }}%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Year level average -->
                <div class="mt-4 font-bold text-primary">Year Level Average: {{ props.yearLevelAverage }}%</div>
            </div>

            <div v-else-if="filtersApplied" class="mt-6 text-center text-gray-500 italic">No results available.</div>
        </div>
    </AppLayout>
</template>
