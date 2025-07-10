<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Chart, registerables } from 'chart.js';
import { ClipboardList, Layers, Users } from 'lucide-vue-next';
import { onMounted } from 'vue';

Chart.register(...registerables);

const props = defineProps<{
    yearLevels: { id: number; name: string; students_count: number }[];
    sectionCount: number;
    teacherCount: number;
    chart: {
        labels: string[];
        barData: number[];
        lineData: number[];
    };
}>();

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
    </AppLayout>
</template>
