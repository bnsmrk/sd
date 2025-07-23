<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Book, ClipboardList, GraduationCap, LayoutGrid, MonitorSmartphone, User, Users } from 'lucide-vue-next';

const props = defineProps<{
    counts: {
        yearLevels: number;
        sections: number;
        subjects: number;
        teachers: number;
        students: number;
        heads: number;
        ict: number;
        lessonPlans: number;
        noComments: number;
        withComments: number;
    };
}>();

const hoverBorders = ['hover:border-[#ff69b4]', 'hover:border-[#01006c]', 'hover:border-[#ffc60b]'];
const topBorderColors = ['bg-yellow-400', 'bg-pink-400', 'bg-indigo-400'];

function getCardClasses(index: number) {
    const borderHover = hoverBorders[index % hoverBorders.length];
    return `relative overflow-hidden rounded-xl border border-pink-200 bg-white p-5 shadow-md transition-all hover:scale-[1.02] hover:shadow-xl ${borderHover}`;
}

function getTopBorderColor(index: number) {
    return topBorderColors[index % topBorderColors.length];
}

const statsKeys = (Object.keys(props.counts) as (keyof typeof props.counts)[]).filter(
    (key) => !['lessonPlans', 'noComments', 'withComments'].includes(key),
);
</script>

<template>
    <Head title="Principal Dashboard" />
    <AppLayout :breadcrumbs="[{ title: 'Principal Dashboard', href: '/principal-dashboard' }]">
        <div class="min-h-screen space-y-8 bg-pink-50 p-6">
            <h1 class="text-3xl font-bold text-[#01006c]">Principal Dashboard</h1>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="(key, index) in statsKeys" :key="key" :class="getCardClasses(index)">
                    <div :class="getTopBorderColor(index)" class="absolute top-0 left-0 h-1 w-full"></div>
                    <div class="flex h-full flex-col justify-between">
                        <div class="pb-4">
                            <h2 class="flex items-center gap-2 text-lg font-semibold text-[#01006c]">
                                <Book v-if="key === 'yearLevels'" class="h-5 w-5" />
                                <LayoutGrid v-else-if="key === 'sections'" class="h-5 w-5" />
                                <ClipboardList v-else-if="key === 'subjects'" class="h-5 w-5" />
                                <User v-else-if="key === 'teachers'" class="h-5 w-5" />
                                <GraduationCap v-else-if="key === 'students'" class="h-5 w-5" />
                                <Users v-else-if="key === 'heads'" class="h-5 w-5" />
                                <MonitorSmartphone v-else-if="key === 'ict'" class="h-5 w-5" />
                                {{
                                    key === 'yearLevels'
                                        ? 'Year Levels'
                                        : key === 'sections'
                                          ? 'Sections'
                                          : key === 'subjects'
                                            ? 'Subjects'
                                            : key === 'teachers'
                                              ? 'Teachers'
                                              : key === 'students'
                                                ? 'Students'
                                                : key === 'heads'
                                                  ? 'Year Level Heads'
                                                  : key === 'ict'
                                                    ? 'ICT Personnel'
                                                    : key
                                }}
                            </h2>
                            <p class="mt-2 text-4xl font-bold text-blue-800">{{ props.counts[key] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="mt-10 mb-4 text-2xl font-bold text-[#01006c]">Lesson Plan</h2>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div :class="getCardClasses(100)">
                        <div class="absolute top-0 left-0 h-1 w-full bg-green-400"></div>
                        <div class="flex h-full flex-col justify-between">
                            <div class="pb-4">
                                <h2 class="flex items-center gap-2 text-lg font-semibold text-[#01006c]">Total Lesson Plans</h2>
                                <p class="mt-2 text-4xl font-bold text-blue-800">{{ props.counts.lessonPlans }}</p>
                            </div>
                        </div>
                    </div>
                    <div :class="getCardClasses(102)">
                        <div class="absolute top-0 left-0 h-1 w-full bg-blue-400"></div>
                        <div class="flex h-full flex-col justify-between">
                            <div class="pb-4">
                                <h2 class="flex items-center gap-2 text-lg font-semibold text-[#01006c]">Commented Lesson Plans</h2>
                                <p class="mt-2 text-4xl font-bold text-blue-800">{{ props.counts.withComments }}</p>
                            </div>
                        </div>
                    </div>
                    <div :class="getCardClasses(101)">
                        <div class="absolute top-0 left-0 h-1 w-full bg-red-400"></div>
                        <div class="flex h-full flex-col justify-between">
                            <div class="pb-4">
                                <h2 class="flex items-center gap-2 text-lg font-semibold text-[#01006c]">No Comments Yet</h2>
                                <p class="mt-2 text-4xl font-bold text-blue-800">{{ props.counts.noComments }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
