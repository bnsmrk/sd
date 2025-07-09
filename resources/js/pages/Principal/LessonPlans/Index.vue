<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

import { FileText, Filter, Send } from 'lucide-vue-next';

interface YearLevel {
    id: number;
    name: string;
}

interface Section {
    id: number;
    name: string;
    year_level_id: number;
}

interface LessonPlan {
    id: number;
    title: string;
    file_path: string;
    year_level?: YearLevel;
    section?: Section;
    uploader?: {
        id: number;
        name: string;
    };
    comments?: {
        id: number;
        comment: string;
        user: {
            id: number;
            name: string;
        };
    }[];
}

const newComments = ref<Record<number, string>>({});

const submitComment = (planId: number) => {
    const comment = newComments.value[planId]?.trim();
    if (!comment) return;

    router.post(
        '/principal-teachers-lesson-plans/comment',
        {
            material_id: planId,
            comment,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                newComments.value[planId] = '';
            },
        },
    );
};

interface Filters {
    year_level_id: number | null;
    section_id: number | null;
}

const props = defineProps<{
    lessonPlans: LessonPlan[];
    yearLevels: YearLevel[];
    sections: Section[];
    filters: Filters;
}>();

const selectedYearLevel = ref<number | null>(props.filters.year_level_id ?? null);
const selectedSection = ref<number | null>(props.filters.section_id ?? null);
const filtersApplied = ref(false);

const filteredSections = computed(() => props.sections.filter((s) => s.year_level_id === selectedYearLevel.value));

const applyFilters = () => {
    filtersApplied.value = true;
    router.get(
        '/principal-teachers-lesson-plans',
        {
            year_level_id: selectedYearLevel.value,
            section_id: selectedSection.value,
        },
        {
            preserveScroll: true,
            preserveState: true,
        },
    );
};
</script>

<template>
    <Head title="Teachers Lesson Plans" />
    <AppLayout>
        <div class="mx-auto max-w-7xl space-y-8 px-6 py-8">
            <h1 class="flex items-center gap-2 text-2xl font-bold text-[#01006c]">
                <FileText class="h-6 w-6" /> Teachers Lesson Plans
            </h1>

            <!-- Filter Bar -->
            <div class="flex flex-wrap items-center gap-4 rounded-lg border  bg-white p-4">
                <select
                    v-model="selectedYearLevel"
                    @change="selectedSection = null"
                    class="w-56 rounded border border-[#01006c] bg-white px-3 py-2 text-sm focus:border-[#ffc60b] focus:outline-none sm:w-64 md:w-72 lg:w-80"
                >
                    <option :value="null">Select Year Level</option>
                    <option v-for="y in props.yearLevels" :key="y.id" :value="y.id">{{ y.name }}</option>
                </select>

                <select
                    v-model="selectedSection"
                    class="w-56 rounded border border-[#01006c] bg-white px-3 py-2 text-sm focus:border-[#ffc60b] focus:outline-none sm:w-64 md:w-72 lg:w-80"
                >
                    <option :value="null">Select Section</option>
                    <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>

                <button
                    @click="applyFilters"
                    class="inline-flex items-center gap-2 rounded bg-[#01006c] px-4 py-2 text-sm text-white hover:bg-[#0d1282]"
                >
                    <Filter class="h-4 w-4" /> Apply Filters
                </button>
            </div>

            <!-- Lesson Plans Table -->
            <div v-if="filtersApplied && props.lessonPlans.length > 0" class="overflow-x-auto">
                <table class="min-w-full border border-[#01006c] text-left text-sm">
                    <thead class="bg-[#01006c] text-white">
                        <tr>
                            <th class="border px-4 py-2">Title</th>
                            <th class="border px-4 py-2">Teacher</th>
                            <th class="border px-4 py-2">Year Level</th>
                            <th class="border px-4 py-2">Section</th>
                            <th class="border px-4 py-2">File & Comments</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="plan in props.lessonPlans"
                            :key="plan.id"
                            class="bg-white hover:bg-gray-50"
                        >
                            <td class="border px-4 py-2 text-[#01006c]">{{ plan.title }}</td>
                            <td class="border px-4 py-2 text-[#01006c]">{{ plan.uploader?.name ?? 'N/A' }}</td>
                            <td class="border px-4 py-2 text-[#01006c]">{{ plan.year_level?.name ?? '—' }}</td>
                            <td class="border px-4 py-2 text-[#01006c]">{{ plan.section?.name ?? '—' }}</td>
                            <td class="space-y-3 border px-4 py-2">
                                <a
                                    :href="`/storage/${plan.file_path}`"
                                    target="_blank"
                                    class="inline-flex items-center gap-1 text-blue-600 hover:text-[#ff69b4]"
                                >
                                    <FileText class="h-4 w-4" /> View File
                                </a>

                                <div class="space-y-1 text-xs text-gray-600">
                                    <div v-if="plan.comments?.length">
                                        <p class="font-medium text-[#ff69b4]">Comments:</p>
                                        <ul class="ml-4 list-disc">
                                            <li v-for="c in plan.comments" :key="c.id">
                                                <span class="italic text-[#01006c]">{{ c.user.name }}:</span> {{ c.comment }}
                                            </li>
                                        </ul>
                                    </div>

                                    <form @submit.prevent="submitComment(plan.id)" class="mt-2 space-y-1">
                                        <input
                                            v-model="newComments[plan.id]"
                                            class="w-full rounded border border-[#01006c] px-2 py-1 text-sm focus:border-[#ffc60b] focus:outline-none"
                                            placeholder="Add a comment"
                                        />
                                        <button
                                            type="submit"
                                            class="inline-flex items-center gap-1 rounded bg-green-600 px-3 py-1 text-xs text-white hover:bg-green-700"
                                        >
                                            <Send class="h-4 w-4" /> Submit
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- No Results -->
            <div v-else-if="filtersApplied" class="text-center text-gray-500 italic">
                No lesson plans found for selected filters.
            </div>
        </div>
    </AppLayout>
</template>

