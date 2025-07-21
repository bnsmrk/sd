<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { BookOpen, CalendarClock, FileText, ListChecks, Send } from 'lucide-vue-next';
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
    isLoading.value = true;
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
            onFinish: () => {
                setTimeout(() => {
                    isLoading.value = false;
                }, 800);
            },
        },
    );
};
const isLoading = ref(false);

const pdfUrl = computed(() => {
    const year = selectedYearLevel.value ?? '';
    const section = selectedSection.value ?? '';
    const type = selectedType.value;
    return `/students-proficiency-result/export?year_level_id=${year}&section_id=${section}&type=${type}`;
});

const sortKeyIndividual = ref<'name' | 'section' | 'average'>('name');
const sortAscIndividual = ref(true);

function toggleSortIndividual(key: typeof sortKeyIndividual.value) {
    if (sortKeyIndividual.value === key) {
        sortAscIndividual.value = !sortAscIndividual.value;
    } else {
        sortKeyIndividual.value = key;
        sortAscIndividual.value = true;
    }
}

const sortedIndividuals = computed(() => {
    return [...props.individuals].sort((a, b) => {
        let aVal: any, bVal: any;

        switch (sortKeyIndividual.value) {
            case 'name':
                aVal = a.student.name.toLowerCase();
                bVal = b.student.name.toLowerCase();
                break;
            case 'section':
                aVal = a.student.section?.name?.toLowerCase() ?? '';
                bVal = b.student.section?.name?.toLowerCase() ?? '';
                break;
            case 'average':
                aVal = a.average;
                bVal = b.average;
                break;
        }

        if (aVal < bVal) return sortAscIndividual.value ? -1 : 1;
        if (aVal > bVal) return sortAscIndividual.value ? 1 : -1;
        return 0;
    });
});

const sortKeySection = ref<'section' | 'average'>('section');
const sortAscSection = ref(true);

function toggleSortSection(key: typeof sortKeySection.value) {
    if (sortKeySection.value === key) {
        sortAscSection.value = !sortAscSection.value;
    } else {
        sortKeySection.value = key;
        sortAscSection.value = true;
    }
}

const sortedSections = computed(() => {
    return [...props.sectionsAvg].sort((a, b) => {
        let aVal: any = sortKeySection.value === 'section' ? a.section.name.toLowerCase() : a.average;
        let bVal: any = sortKeySection.value === 'section' ? b.section.name.toLowerCase() : b.average;

        if (aVal < bVal) return sortAscSection.value ? -1 : 1;
        if (aVal > bVal) return sortAscSection.value ? 1 : -1;
        return 0;
    });
});
</script>

<template>
    <AppLayout>
        <!-- Loading Overlay -->
        <div v-if="isLoading" class="fixed inset-0 z-50 flex items-center justify-center bg-white/30 backdrop-blur-sm">
            <div class="flex flex-col items-center gap-4">
                <div class="relative h-16 w-16">
                    <div class="animate-spin-slow-cw absolute inset-0 rounded-full border-4 border-blue-600 border-t-transparent"></div>
                    <div class="animate-spin-slow-ccw absolute inset-2 rounded-full border-4 border-yellow-500 border-t-transparent"></div>
                    <div class="animate-spin-fast-cw absolute inset-4 rounded-full border-4 border-pink-500 border-t-transparent"></div>
                </div>
                <div class="text-center">
                    <span class="block animate-pulse text-base font-semibold text-[#ff69b4]">Processing Request...</span>
                    <span class="text-xs text-pink-500/70">This may take a moment</span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="space-y-6 p-6">
            <div class="flex items-center gap-3">
                <FileText class="text-[#ff69b4]" />
                <h1 class="text-2xl font-bold text-[#ff69b4]">Student Proficiency Report</h1>
            </div>

            <!-- Filters -->
            <div class="rounded-lg border border-pink-300 bg-white p-4 shadow-md">
                <div class="flex flex-wrap items-end gap-4">
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-[#ff69b4]">Year Level</label>
                        <select
                            v-model="selectedYearLevel"
                            class="rounded border border-pink-500 bg-white px-3 py-2 text-sm focus:border-pink-400 focus:outline-none"
                        >
                            <option :value="null">Select Year Level</option>
                            <option v-for="y in props.yearLevels" :key="y.id" :value="y.id">{{ y.name }}</option>
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-[#ff69b4]">Section</label>
                        <select
                            v-model="selectedSection"
                            class="rounded border border-pink-500 bg-white px-3 py-2 text-sm focus:border-pink-400 focus:outline-none"
                        >
                            <option :value="null">Select Section</option>
                            <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-[#ff69b4]">Test Type</label>
                        <select
                            v-model="selectedType"
                            class="rounded border border-pink-500 bg-white px-3 py-2 text-sm focus:border-pink-400 focus:outline-none"
                        >
                            <option value="">Select Type</option>
                            <option value="reading">Reading</option>
                            <option value="numerical">Numerical</option>
                        </select>
                    </div>

                    <button
                        @click="applyFilters"
                        class="mt-4 flex items-center gap-2 rounded bg-[#ff69b4] px-4 py-2 text-sm font-medium text-white shadow hover:bg-[#e858a1]"
                    >
                        <Send class="h-4 w-4" /> Generate Report
                    </button>

                    <a
                        v-if="filtersApplied && props.individuals.length > 0"
                        :href="pdfUrl"
                        target="_blank"
                        class="mt-4 flex items-center gap-2 rounded bg-pink-500 px-4 py-2 text-sm font-medium text-white shadow hover:bg-pink-600"
                    >
                        <FileText class="h-4 w-4" /> Download PDF
                    </a>
                </div>
            </div>

            <!-- Report -->
            <div v-if="filtersApplied && props.individuals.length > 0" class="space-y-8">
                <!-- Individual Averages -->
                <div>
                    <div class="mb-2 flex items-center gap-2 text-xl font-semibold text-pink-700">
                        <BookOpen class="h-5 w-5 text-pink-600" /> Individual Averages
                    </div>
                    <div class="overflow-x-auto rounded-lg border border-pink-300 shadow">
                        <table class="min-w-full table-auto text-left text-sm text-pink-900">
                            <thead class="bg-pink-100 text-xs font-semibold text-pink-700 uppercase">
                                <tr>
                                    <th class="cursor-pointer border px-4 py-2" @click="toggleSortIndividual('name')">
                                        Student <span v-if="sortKeyIndividual === 'name'">{{ sortAscIndividual ? '↑' : '↓' }}</span>
                                    </th>
                                    <th class="cursor-pointer border px-4 py-2" @click="toggleSortIndividual('section')">
                                        Section <span v-if="sortKeyIndividual === 'section'">{{ sortAscIndividual ? '↑' : '↓' }}</span>
                                    </th>
                                    <th class="cursor-pointer border px-4 py-2" @click="toggleSortIndividual('average')">
                                        Average (%) <span v-if="sortKeyIndividual === 'average'">{{ sortAscIndividual ? '↑' : '↓' }}</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-pink-100 bg-white">
                                <tr v-for="i in sortedIndividuals" :key="i.student.id" class="hover:bg-pink-50">
                                    <td class="border border-pink-300 px-4 py-2 text-pink-800">{{ i.student.name }}</td>
                                    <td class="border border-pink-300 px-4 py-2 text-pink-800">{{ i.student.section?.name ?? 'N/A' }}</td>
                                    <td class="border border-pink-300 px-4 py-2 font-medium text-green-700">{{ i.average }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Section Averages -->
                <div>
                    <div class="mb-2 flex items-center gap-2 text-xl font-semibold text-pink-700">
                        <ListChecks class="h-5 w-5 text-pink-600" /> Section Averages
                    </div>
                    <div class="overflow-x-auto rounded-lg border border-pink-300 shadow">
                        <table class="min-w-full table-auto text-left text-sm text-pink-900">
                            <thead class="bg-pink-100 text-xs font-semibold text-pink-700 uppercase">
                                <tr>
                                    <th class="cursor-pointer border px-4 py-2" @click="toggleSortSection('section')">
                                        Section <span v-if="sortKeySection === 'section'">{{ sortAscSection ? '↑' : '↓' }}</span>
                                    </th>
                                    <th class="cursor-pointer border px-4 py-2" @click="toggleSortSection('average')">
                                        Average (%) <span v-if="sortKeySection === 'average'">{{ sortAscSection ? '↑' : '↓' }}</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-pink-100 bg-white">
                                <tr v-for="s in sortedSections" :key="s.section.id" class="hover:bg-pink-50">
                                    <td class="border border-pink-300 px-4 py-2 text-pink-800">{{ s.section.name }}</td>
                                    <td class="border border-pink-300 px-4 py-2 font-medium text-pink-600">{{ s.average }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Year Level Average -->
                <div class="mt-2 flex items-center gap-2 text-lg font-bold text-pink-700">
                    <CalendarClock class="h-5 w-5 text-yellow-400" />
                    Year Level Average:
                    <span class="ml-2 text-green-700">{{ props.yearLevelAverage }}%</span>
                </div>
            </div>

            <!-- No Results -->
            <div v-else-if="filtersApplied" class="mt-8 text-center text-sm text-pink-400 italic">
                <p>No results available for the selected filters.</p>
            </div>
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
