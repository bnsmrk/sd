<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import {
  BarChart2,
  Layers,
  LayoutTemplate,
  BookOpen,
  FileText,
  ClipboardList,
  Search
} from 'lucide-vue-next'; // Or your preferred icon lib

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

// When subject changes, fetch modules
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

// Auto-select first module when modules change
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
    <AppLayout>
      <div class="space-y-6 p-6 max-w-full mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
          <BarChart2 class="w-6 h-6" /> Proficiency Report
        </h1>
  
        <!-- Filters -->
        <div class="flex flex-wrap gap-4 bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
          <!-- Year Level -->
          <div class="relative flex-grow min-w-[180px]">
            <Layers class="w-4 h-4 absolute left-3 top-2.5 text-gray-500" />
            <select
              v-model="selectedYearLevel"
              class="pl-10 pr-3 py-2 w-full rounded border text-sm bg-white dark:bg-gray-700 dark:text-white"
            >
              <option :value="null">Select Year Level</option>
              <option v-for="y in props.yearLevels" :key="y.id" :value="y.id">{{ y.name }}</option>
            </select>
          </div>
  
          <!-- Section -->
          <div class="relative flex-grow min-w-[180px]">
            <LayoutTemplate class="w-4 h-4 absolute left-3 top-2.5 text-gray-500" />
            <select
              v-model="selectedSection"
              class="pl-10 pr-3 py-2 w-full rounded border text-sm bg-white dark:bg-gray-700 dark:text-white"
            >
              <option :value="null">Select Section</option>
              <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
          </div>
  
          <!-- Subject -->
          <div class="relative flex-grow min-w-[180px]">
            <BookOpen class="w-4 h-4 absolute left-3 top-2.5 text-gray-500" />
            <select
              v-model="selectedSubject"
              class="pl-10 pr-3 py-2 w-full rounded border text-sm bg-white dark:bg-gray-700 dark:text-white"
            >
              <option :value="null">Select Subject</option>
              <option v-for="sub in filteredSubjects" :key="sub.id" :value="sub.id">{{ sub.name }}</option>
            </select>
          </div>
  
          <!-- Module -->
          <div class="relative flex-grow min-w-[180px]">
            <FileText class="w-4 h-4 absolute left-3 top-2.5 text-gray-500" />
            <select
              v-model="selectedModule"
              class="pl-10 pr-3 py-2 w-full rounded border text-sm bg-white dark:bg-gray-700 dark:text-white"
            >
              <option :value="null">Select Module</option>
              <option v-for="m in props.modules" :key="m.id" :value="m.id">{{ m.name }}</option>
            </select>
          </div>
  
          <!-- Type -->
          <div class="relative flex-grow min-w-[160px]">
            <ClipboardList class="w-4 h-4 absolute left-3 top-2.5 text-gray-500" />
            <select
              v-model="selectedType"
              class="pl-10 pr-3 py-2 w-full rounded border text-sm bg-white dark:bg-gray-700 dark:text-white"
            >
              <option value="quiz">Quiz</option>
              <option value="exam">Exam</option>
            </select>
          </div>
  
          <!-- Generate Report Button -->
          <div class="flex-shrink-0">
            <button
              @click="applyFilters"
              class="inline-flex items-center gap-2 rounded bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700"
            >
              <Search class="w-4 h-4" /> Generate Report
            </button>
          </div>
        </div>
  
        <!-- PDF Button -->
        <div v-if="canGeneratePdf" class="mt-4 flex justify-end">
          <a
            :href="pdfUrl"
            target="_blank"
            class="inline-flex items-center gap-2 rounded bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700"
          >
            <FileText class="w-4 h-4" /> Generate PDF
          </a>
        </div>
  
        <!-- Results Table -->
        <div v-if="props.resultsByActivity.length > 0" class="mt-6 space-y-8">
          <div v-for="group in props.resultsByActivity" :key="group.activity_title">
            <h2 class="text-lg font-semibold text-blue-700">{{ group.activity_title }}</h2>
            <table class="mt-2 min-w-full border text-sm">
              <thead class="bg-gray-100 dark:bg-gray-700 text-left text-gray-700 dark:text-gray-200">
                <tr>
                  <th class="border px-4 py-2">Student</th>
                  <th class="border px-4 py-2">Score</th>
                  <th class="border px-4 py-2">Total</th>
                  <th class="border px-4 py-2">Average (%)</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="entry in group.entries"
                  :key="entry.student"
                  class="bg-white dark:bg-gray-800"
                >
                  <td class="border px-4 py-2">{{ entry.student }}</td>
                  <td class="border px-4 py-2">{{ entry.score }}</td>
                  <td class="border px-4 py-2">{{ entry.total }}</td>
                  <td class="border px-4 py-2">{{ entry.average }}%</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
  
        <!-- No Data -->
        <div v-else class="mt-8 text-center text-gray-500 italic">No data to show</div>
      </div>
    </AppLayout>
  </template>
  