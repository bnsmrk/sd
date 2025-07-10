<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Chart, registerables } from 'chart.js';
import { Shield, User, Users } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

Chart.register(...registerables);

const props = defineProps<{
    userSummary: {
        students: number;
        teachers: number;
        admins: number;
    };
    monthlyStats: {
        labels: string[];
        students: number[];
        teachers: number[];
    };
}>();

const barChartRef = ref<HTMLCanvasElement | null>(null);
const lineChartRef = ref<HTMLCanvasElement | null>(null);

const hoverBorders = ['hover:border-[#ff69b4]', 'hover:border-[#01006c]', 'hover:border-[#ffc60b]'];
const topBorderColors = ['bg-blue-400', 'bg-green-400', 'bg-yellow-400'];

function getCardClasses(index: number) {
    const borderHover = hoverBorders[index % hoverBorders.length];
    return `relative overflow-hidden rounded-xl border border-pink-200 bg-white p-5 shadow-md transition-all hover:scale-[1.02] hover:shadow-xl ${borderHover}`;
}

function getTopBorderColor(index: number) {
    return topBorderColors[index % topBorderColors.length];
}

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/dashboard' }];

onMounted(() => {
    if (barChartRef.value) {
        new Chart(barChartRef.value, {
            type: 'bar',
            data: {
                labels: ['Students', 'Teachers', 'Admins'],
                datasets: [
                    {
                        label: 'User Count',
                        data: [props.userSummary.students, props.userSummary.teachers, props.userSummary.admins],
                        backgroundColor: ['#3B82F6', '#10B981', '#F59E0B'],
                        borderRadius: 8,
                        barThickness: 40,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
            },
        });
    }

    if (lineChartRef.value) {
        new Chart(lineChartRef.value, {
            type: 'line',
            data: {
                labels: props.monthlyStats.labels,
                datasets: [
                    {
                        label: 'Students',
                        data: props.monthlyStats.students,
                        borderColor: '#3B82F6',
                        tension: 0.3,
                        fill: false,
                    },
                    {
                        label: 'Teachers',
                        data: props.monthlyStats.teachers,
                        borderColor: '#10B981',
                        tension: 0.3,
                        fill: false,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: '#4B5563',
                            font: { size: 12 },
                        },
                    },
                },
            },
        });
    }
});
</script>

<template>
    <Head title="" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen space-y-10 bg-pink-50 px-6 py-8">
            <h1 class="text-3xl font-bold text-[#01006c]">💻 ICT Dashboard</h1>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div :class="getCardClasses(0)">
                    <div :class="getTopBorderColor(0)" class="absolute top-0 left-0 h-1 w-full"></div>
                    <div class="flex h-full flex-col justify-between">
                        <div class="pb-4">
                            <h2 class="flex items-center gap-2 text-lg font-semibold text-[#01006c]"><Users class="h-5 w-5" /> Total Students</h2>
                            <p class="mt-2 text-4xl font-bold text-blue-800">{{ props.userSummary.students }}</p>
                        </div>
                    </div>
                </div>

                <div :class="getCardClasses(1)">
                    <div :class="getTopBorderColor(1)" class="absolute top-0 left-0 h-1 w-full"></div>
                    <div class="flex h-full flex-col justify-between">
                        <div class="pb-4">
                            <h2 class="flex items-center gap-2 text-lg font-semibold text-[#01006c]"><User class="h-5 w-5" /> Total Teachers</h2>
                            <p class="mt-2 text-4xl font-bold text-green-800">{{ props.userSummary.teachers }}</p>
                        </div>
                    </div>
                </div>

                <div :class="getCardClasses(2)">
                    <div :class="getTopBorderColor(2)" class="absolute top-0 left-0 h-1 w-full"></div>
                    <div class="flex h-full flex-col justify-between">
                        <div class="pb-4">
                            <h2 class="flex items-center gap-2 text-lg font-semibold text-[#01006c]"><Shield class="h-5 w-5" /> Total Admins</h2>
                            <p class="mt-2 text-4xl font-bold text-yellow-800">{{ props.userSummary.admins }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="rounded-xl bg-white p-6 shadow transition hover:shadow-lg">
                    <h2 class="mb-2 text-lg font-semibold text-indigo-800">📊 User Role Distribution</h2>
                    <div class="h-64">
                        <canvas ref="barChartRef"></canvas>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-6 shadow transition hover:shadow-lg">
                    <h2 class="mb-2 text-lg font-semibold text-red-600">📈 Monthly Growth</h2>
                    <div class="h-64">
                        <canvas ref="lineChartRef"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
