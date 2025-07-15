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
                }, 1000);
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

        <div class="space-y-6 p-6">
            <div class="flex items-center gap-3">
                <FileText class="text-[#01006c]" />
                <h1 class="text-2xl font-bold text-[#01006c]">Student Proficiency Report</h1>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-md">
                <div class="flex flex-wrap items-end gap-4">
                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-[#ff69b4]">Year Level</label>
                        <select
                            v-model="selectedYearLevel"
                            class="rounded border border-[#01006c] bg-white px-3 py-2 text-sm focus:border-[#ffc60b] focus:outline-none"
                        >
                            <option :value="null">Select Year Level</option>
                            <option v-for="y in props.yearLevels" :key="y.id" :value="y.id">{{ y.name }}</option>
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-[#ff69b4]">Section</label>
                        <select
                            v-model="selectedSection"
                            class="rounded border border-[#01006c] bg-white px-3 py-2 text-sm focus:border-[#ffc60b] focus:outline-none"
                        >
                            <option :value="null">Select Section</option>
                            <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label class="text-sm font-medium text-[#ff69b4]">Test Type</label>
                        <select
                            v-model="selectedType"
                            class="rounded border border-[#01006c] bg-white px-3 py-2 text-sm focus:border-[#ffc60b] focus:outline-none"
                        >
                            <option value="">Select Type</option>
                            <option value="reading">Reading</option>
                            <option value="numerical">Numerical</option>
                        </select>
                    </div>

                    <button
                        @click="applyFilters"
                        class="mt-4 flex items-center gap-2 rounded bg-[#01006c] px-4 py-2 text-sm font-medium text-white shadow hover:bg-[#0d1282]"
                    >
                        <Send class="h-4 w-4" /> Generate Report
                    </button>

                    <a
                        v-if="filtersApplied && props.individuals.length > 0"
                        :href="pdfUrl"
                        target="_blank"
                        class="mt-4 flex items-center gap-2 rounded bg-red-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-red-700"
                    >
                        <FileText class="h-4 w-4" /> Download PDF
                    </a>
                </div>
            </div>

            <div v-if="filtersApplied && props.individuals.length > 0" class="space-y-8">
                <div>
                    <div class="mb-2 flex items-center gap-2 text-xl font-semibold text-[#01006c]">
                        <BookOpen class="h-5 w-5 text-green-600" /> Individual Averages
                    </div>
                    <table class="w-full overflow-hidden rounded-md border border-[#01006c] text-sm">
                        <thead class="bg-[#01006c] text-white">
                            <tr>
                                <th class="border border-[#01006c] px-4 py-2 text-left">Student</th>
                                <th class="border border-[#01006c] px-4 py-2 text-left">Section</th>
                                <th class="border border-[#01006c] px-4 py-2 text-left">Average (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="i in props.individuals" :key="i.student.id" class="even:bg-gray-50">
                                <td class="border border-[#01006c] px-4 py-2 text-[#01006c]">{{ i.student.name }}</td>
                                <td class="border border-[#01006c] px-4 py-2 text-[#01006c]">{{ i.student.section?.name ?? 'N/A' }}</td>
                                <td class="border border-[#01006c] px-4 py-2 font-medium text-green-700">{{ i.average }}%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div>
                    <div class="mb-2 flex items-center gap-2 text-xl font-semibold text-[#01006c]">
                        <ListChecks class="h-5 w-5 text-indigo-600" /> Section Averages
                    </div>
                    <table class="w-full overflow-hidden rounded-md border border-[#01006c] text-sm">
                        <thead class="bg-[#01006c] text-white">
                            <tr>
                                <th class="border border-[#01006c] px-4 py-2 text-left">Section</th>
                                <th class="border border-[#01006c] px-4 py-2 text-left">Average (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="s in props.sectionsAvg" :key="s.section.id" class="even:bg-gray-50">
                                <td class="border border-[#01006c] px-4 py-2 text-[#01006c]">{{ s.section.name }}</td>
                                <td class="border border-[#01006c] px-4 py-2 font-medium text-pink-600">{{ s.average }}%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-2 flex items-center gap-2 text-lg font-bold text-[#01006c]">
                    <CalendarClock class="h-5 w-5 text-yellow-500" />
                    Year Level Average:
                    <span class="ml-2 text-green-700">{{ props.yearLevelAverage }}%</span>
                </div>
            </div>

            <div v-else-if="filtersApplied" class="mt-8 text-center text-sm text-gray-500 italic">
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
