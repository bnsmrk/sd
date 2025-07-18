<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { Chart, registerables } from 'chart.js';
import debounce from 'lodash/debounce';
import { ClipboardList, Layers, Users } from 'lucide-vue-next';
import { onMounted, ref, watch } from 'vue';
Chart.register(...registerables);

const props = defineProps<{
    yearLevels: { id: number; name: string; students_count: number }[];
    sectionCount: number;
    teacherCount: number;
    teachers: {
        data: {
            id: number;
            name: string;
            email: string;
            subject_count: number;
            section_count: number;
            year_level_count: number;
        }[];
        links: any[];
    };
    filters: { search?: string };
    chart: {
        labels: string[];
        barData: number[];
        lineData: number[];
    };
}>();

const search = ref(props.filters?.search ?? '');

watch(
    search,
    debounce((val) => {
        router.get(route('head.dashboard'), { search: val }, { preserveState: true, replace: true });
    }, 300),
);

const totalStudents = props.yearLevels.reduce((sum, yl) => sum + yl.students_count, 0);
const hoverBorders = ['hover:border-[#ff69b4]', 'hover:border-[#01006c]', 'hover:border-[#ffc60b]'];

function getCardClasses(index: number) {
    const borderHover = hoverBorders[index % hoverBorders.length];
    return `relative overflow-hidden rounded-xl border border-pink-200 bg-white p-5 shadow-md transition-all hover:scale-[1.02] hover:shadow-xl ${borderHover}`;
}

onMounted(() => {
    const ctx = document.getElementById('chart') as HTMLCanvasElement;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: props.chart.labels,
            datasets: [
                {
                    label: 'Students Registered',
                    data: props.chart.barData,
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                },
                {
                    label: 'Activity Score',
                    data: props.chart.lineData,
                    type: 'line',
                    borderColor: 'rgba(244, 63, 94, 0.9)',
                    tension: 0.4,
                    fill: false,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
        },
    });
});
</script>

<template>
    <Head title="Head Dashboard" />
    <AppLayout :breadcrumbs="[{ title: 'Head Dashboard', href: '/head-dashboard' }]">
        <div class="min-h-screen space-y-6 bg-pink-50 px-6 py-8">
            <h1 class="text-3xl font-bold text-[#01006c]">📊 Head Dashboard</h1>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div :class="getCardClasses(0)">
                    <div class="absolute top-0 left-0 h-1 w-full bg-yellow-400"></div>
                    <div class="flex h-full flex-col justify-between">
                        <div class="pb-4">
                            <h2 class="flex items-center gap-2 text-lg font-semibold text-[#01006c]"><Users class="h-5 w-5" /> Total Students</h2>
                            <p class="mt-2 text-4xl font-bold text-blue-800">{{ totalStudents }}</p>
                        </div>
                    </div>
                </div>

                <div :class="getCardClasses(1)">
                    <div class="absolute top-0 left-0 h-1 w-full bg-pink-400"></div>
                    <div class="flex h-full flex-col justify-between">
                        <div class="pb-4">
                            <h2 class="flex items-center gap-2 text-lg font-semibold text-[#01006c]"><Layers class="h-5 w-5" /> Total Sections</h2>
                            <p class="mt-2 text-4xl font-bold text-green-800">{{ props.sectionCount }}</p>
                        </div>
                    </div>
                </div>

                <div :class="getCardClasses(2)">
                    <div class="absolute top-0 left-0 h-1 w-full bg-indigo-400"></div>
                    <div class="flex h-full flex-col justify-between">
                        <div class="pb-4">
                            <h2 class="flex items-center gap-2 text-lg font-semibold text-[#01006c]">
                                <ClipboardList class="h-5 w-5" /> Total Teachers
                            </h2>
                            <p class="mt-2 text-4xl font-bold text-purple-800">{{ props.teacherCount }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-10 h-[400px] rounded-xl bg-white p-6 shadow">
                <h2 class="mb-2 text-lg font-semibold text-indigo-800">📊 Students & Activity Overview</h2>
                <canvas id="chart" class="h-full w-full"></canvas>
            </div>
        </div>
        <div class="mt-10 rounded-xl bg-white p-6 shadow">
            <h2 class="mb-4 text-lg font-semibold text-indigo-800">👩‍🏫 All Teachers</h2>
            <div class="mb-4 flex justify-end">
                <input
                    v-model="search"
                    @input="updateSearch"
                    type="text"
                    placeholder="Search teachers..."
                    class="w-full max-w-xs rounded border px-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500"
                />
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border border-gray-300">
                    <thead class="bg-indigo-100 text-indigo-800">
                        <tr>
                            <th class="px-4 py-2 text-left">#</th>
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-left">Email</th>
                            <th class="px-4 py-2 text-left">Subjects</th>
                            <th class="px-4 py-2 text-left">Sections</th>
                            <th class="px-4 py-2 text-left">Year Levels</th>
                            <th class="px-4 py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(teacher, i) in props.teachers.data" :key="teacher.id" class="border-t">
                            <td class="px-4 py-2">{{ i + 1 }}</td>
                            <td class="px-4 py-2">{{ teacher.name }}</td>
                            <td class="px-4 py-2">{{ teacher.email }}</td>
                            <td class="px-4 py-2">{{ teacher.subject_count }}</td>
                            <td class="px-4 py-2">{{ teacher.section_count }}</td>
                            <td class="px-4 py-2">{{ teacher.year_level_count }}</td>
                            <td class="px-4 py-2">
                                <Link
                                    :href="route('head.teachers.show', teacher.id)"
                                    class="rounded bg-indigo-600 px-3 py-1 text-sm text-white hover:bg-indigo-700"
                                >
                                    View
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-4 flex flex-wrap gap-2">
                <component
                    v-for="link in props.teachers.links"
                    :key="link.label"
                    :is="link.url ? Link : 'span'"
                    :href="link.url || undefined"
                    v-html="link.label"
                    :class="[
                        'rounded border px-3 py-1 text-sm',
                        link.active ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100',
                        !link.url && 'pointer-events-none text-gray-400',
                    ]"
                />
            </div>
        </div>
    </AppLayout>
</template>
