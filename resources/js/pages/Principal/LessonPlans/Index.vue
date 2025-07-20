<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const isLoading = computed(() => isCreating.value || isUpdating.value || isDeleting.value);
const isCreating = ref(false);
const isUpdating = ref(false);
const isDeleting = ref(false);

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

    isCreating.value = true;

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
            onFinish: () => {
                setTimeout(() => {
                    isCreating.value = false;
                }, 800);
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
    isCreating.value = true;
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
            onFinish: () => {
                setTimeout(() => {
                    isCreating.value = false;
                }, 800);
            },
        },
    );
};

const sortKey = ref<'title' | 'teacher' | 'year_level' | 'section'>('title');
const sortAsc = ref(true);

function toggleSort(key: typeof sortKey.value) {
    if (sortKey.value === key) {
        sortAsc.value = !sortAsc.value;
    } else {
        sortKey.value = key;
        sortAsc.value = true;
    }
}

const sortedLessonPlans = computed(() => {
    return [...props.lessonPlans].sort((a, b) => {
        let aVal = '';
        let bVal = '';

        switch (sortKey.value) {
            case 'title':
                aVal = a.title.toLowerCase();
                bVal = b.title.toLowerCase();
                break;
            case 'teacher':
                aVal = a.uploader?.name?.toLowerCase() ?? '';
                bVal = b.uploader?.name?.toLowerCase() ?? '';
                break;
            case 'year_level':
                aVal = a.year_level?.name?.toLowerCase() ?? '';
                bVal = b.year_level?.name?.toLowerCase() ?? '';
                break;
            case 'section':
                aVal = a.section?.name?.toLowerCase() ?? '';
                bVal = b.section?.name?.toLowerCase() ?? '';
                break;
        }

        if (aVal < bVal) return sortAsc.value ? -1 : 1;
        if (aVal > bVal) return sortAsc.value ? 1 : -1;
        return 0;
    });
});
</script>

<template>
    <Head title="Teachers Lesson Plans" />
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
        <div class="mx-auto w-full max-w-7xl space-y-6 p-6">
            <h1 class="flex items-center gap-2 text-2xl font-bold text-[#01006c]"><FileText class="h-6 w-6" /> Teachers Lesson Plans</h1>

            <div class="flex flex-wrap items-center gap-4 rounded-lg border bg-white p-4">
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

            <div v-if="filtersApplied && props.lessonPlans.length > 0" class="overflow-x-auto">
                <table class="min-w-full border border-[#01006c] text-left text-sm">
                    <thead class="bg-[#01006c] text-white">
                        <tr>
                            <th class="cursor-pointer border px-4 py-2" @click="toggleSort('title')">
                                Title <span v-if="sortKey === 'title'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th class="cursor-pointer border px-4 py-2" @click="toggleSort('teacher')">
                                Teacher <span v-if="sortKey === 'teacher'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th class="cursor-pointer border px-4 py-2" @click="toggleSort('year_level')">
                                Year Level <span v-if="sortKey === 'year_level'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th class="cursor-pointer border px-4 py-2" @click="toggleSort('section')">
                                Section <span v-if="sortKey === 'section'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th class="border px-4 py-2">File & Comments</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="plan in sortedLessonPlans" :key="plan.id" class="bg-white hover:bg-gray-50">
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
                                                <span class="text-[#01006c] italic">{{ c.user.name }}:</span> {{ c.comment }}
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

            <div v-else-if="filtersApplied" class="text-center text-gray-500 italic">No lesson plans found for selected filters.</div>
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
