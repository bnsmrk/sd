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

// Refs for filters
const selectedYearLevel = ref<number | null>(props.filters.year_level_id ?? null);
const selectedSection = ref<number | null>(props.filters.section_id ?? null);
const filtersApplied = ref(false);

// Filtered section dropdown based on year level
const filteredSections = computed(() => props.sections.filter((s) => s.year_level_id === selectedYearLevel.value));

// Filter submit
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
    <AppLayout>
        <div class="space-y-6 p-6">
            <h1 class="text-2xl font-bold text-gray-800">Teachers Lesson Plans</h1>

            <!-- Filters -->
            <div class="flex flex-wrap gap-4">
                <!-- Year Level -->
                <select v-model="selectedYearLevel" @change="selectedSection = null" class="rounded border px-3 py-2">
                    <option :value="null">Select Year Level</option>
                    <option v-for="y in props.yearLevels" :key="y.id" :value="y.id">{{ y.name }}</option>
                </select>

                <!-- Section -->
                <select v-model="selectedSection" class="rounded border px-3 py-2">
                    <option :value="null">Select Section</option>
                    <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>

                <!-- Apply Button -->
                <button @click="applyFilters" class="rounded bg-blue-600 px-4 py-2 text-white">Apply Filters</button>
            </div>

            <!-- Lesson Plan Table -->
            <div v-if="filtersApplied && props.lessonPlans.length > 0" class="mt-6 overflow-x-auto">
                <table class="w-full border text-left">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2">Title</th>
                            <th class="border px-4 py-2">Teacher</th>
                            <th class="border px-4 py-2">Year Level</th>
                            <th class="border px-4 py-2">Section</th>
                            <th class="border px-4 py-2">File</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="plan in props.lessonPlans" :key="plan.id">
                            <td class="border px-4 py-2">{{ plan.title }}</td>
                            <td class="border px-4 py-2">{{ plan.uploader?.name ?? 'N/A' }}</td>
                            <td class="border px-4 py-2">{{ plan.year_level?.name ?? '—' }}</td>
                            <td class="border px-4 py-2">{{ plan.section?.name ?? '—' }}</td>
                            <td class="border px-4 py-2">
                                <a :href="`/storage/${plan.file_path}`" target="_blank" class="text-blue-600 hover:underline">View</a>
                                <!-- Comments Section -->
                                <div class="mt-3 text-sm">
                                    <div v-if="plan.comments?.length">
                                        <p class="font-semibold text-gray-700">Comments:</p>
                                        <ul class="ml-5 list-disc">
                                            <li v-for="c in plan.comments" :key="c.id">
                                                {{ c.comment }} — <i>{{ c.user.name }}</i>
                                            </li>
                                        </ul>
                                    </div>

                                    <form @submit.prevent="submitComment(plan.id)" class="mt-2">
                                        <input
                                            v-model="newComments[plan.id]"
                                            class="w-full rounded border px-2 py-1 text-sm"
                                            placeholder="Add a comment"
                                        />
                                        <button type="submit" class="mt-1 rounded bg-green-600 px-3 py-1 text-xs text-white">Submit</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- No Data -->
            <div v-else-if="filtersApplied" class="mt-8 text-center text-gray-500 italic">No lesson plans found for selected filters.</div>
        </div>
    </AppLayout>
</template>
