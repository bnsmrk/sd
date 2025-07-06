<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Chart, registerables } from 'chart.js';
import { onMounted, ref, watch } from 'vue';

Chart.register(...registerables);

const props = defineProps<{
    stats: {
        total_students: number;
        total_sections: number;
        total_assignments: number;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/teacher-dashboard' },
];

// Refs for chart instances
const barChartInstance = ref<Chart | null>(null);
const lineChartInstance = ref<Chart | null>(null);

// Initial mount
onMounted(() => {
    const barCtx = document.getElementById('barChart') as HTMLCanvasElement;
    const lineCtx = document.getElementById('lineChart') as HTMLCanvasElement;

    barChartInstance.value = new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: ['Students', 'Sections', 'Assignments'],
            datasets: [{
                label: 'Overview',
                data: [
                    props.stats.total_students,
                    props.stats.total_sections,
                    props.stats.total_assignments
                ],
                backgroundColor: ['#3B82F6', '#10B981', '#8B5CF6'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Bar Chart Overview' }
            }
        }
    });

    lineChartInstance.value = new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: ['Students', 'Sections', 'Assignments'],
            datasets: [{
                label: 'Trends',
                data: [
                    props.stats.total_students,
                    props.stats.total_sections,
                    props.stats.total_assignments
                ],
                fill: false,
                borderColor: '#EF4444',
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'Line Chart Overview' }
            }
        }
    });
});

// Watch for reactive updates to props.stats
watch(
    () => props.stats,
    (newStats) => {
        const newData = [
            newStats.total_students,
            newStats.total_sections,
            newStats.total_assignments,
        ];

        if (barChartInstance.value) {
            barChartInstance.value.data.datasets[0].data = newData;
            barChartInstance.value.update();
        }

        if (lineChartInstance.value) {
            lineChartInstance.value.data.datasets[0].data = newData;
            lineChartInstance.value.update();
        }
    },
    { deep: true }
);
</script>


<template>
    <Head title="Teacher Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <h1 class="text-2xl font-bold text-gray-800">👨‍🏫 Teacher Dashboard</h1>

            <!-- Dashboard Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="rounded-xl bg-blue-100 p-4 shadow">
                    <h2 class="text-lg font-semibold text-blue-700">👥 Total Students</h2>
                    <p class="text-3xl font-bold text-blue-900">{{ props.stats.total_students }}</p>
                </div>

                <div class="rounded-xl bg-green-100 p-4 shadow">
                    <h2 class="text-lg font-semibold text-green-700">🏫 Total Sections</h2>
                    <p class="text-3xl font-bold text-green-900">{{ props.stats.total_sections }}</p>
                </div>

                <div class="rounded-xl bg-purple-100 p-4 shadow">
                    <h2 class="text-lg font-semibold text-purple-700">📚 Total Assignments</h2>
                    <p class="text-3xl font-bold text-purple-900">{{ props.stats.total_assignments }}</p>
                </div>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-10">
                <div class="bg-white rounded-xl p-4 shadow">
                    <canvas id="barChart"></canvas>
                </div>

                <div class="bg-white rounded-xl p-4 shadow">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
