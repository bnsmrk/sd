<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Chart, registerables } from 'chart.js';
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

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/dashboard' }];
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-10 p-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 p-4 text-white shadow">
                    <h3 class="text-sm tracking-wide uppercase">Total Students</h3>
                    <p class="mt-2 text-3xl font-bold">{{ props.userSummary.students }}</p>
                </div>
                <div class="rounded-xl bg-gradient-to-r from-green-500 to-green-600 p-4 text-white shadow">
                    <h3 class="text-sm tracking-wide uppercase">Total Teachers</h3>
                    <p class="mt-2 text-3xl font-bold">{{ props.userSummary.teachers }}</p>
                </div>
                <div class="rounded-xl bg-gradient-to-r from-yellow-400 to-yellow-500 p-4 text-white shadow">
                    <h3 class="text-sm tracking-wide uppercase">Total Admins</h3>
                    <p class="mt-2 text-3xl font-bold">{{ props.userSummary.admins }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="rounded-xl bg-white p-4 shadow dark:bg-gray-800">
                    <h2 class="mb-2 text-lg font-semibold">User Role Distribution</h2>
                    <div class="h-64">
                        <canvas ref="barChartRef"></canvas>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-4 shadow dark:bg-gray-800">
                    <h2 class="mb-2 text-lg font-semibold">Monthly Growth</h2>
                    <div class="h-64">
                        <canvas ref="lineChartRef"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
