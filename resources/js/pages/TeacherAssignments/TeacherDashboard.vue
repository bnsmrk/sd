<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Chart, registerables } from 'chart.js';
import { ClipboardList, Layers, Users } from 'lucide-vue-next';
import { onMounted, ref, watch } from 'vue';

Chart.register(...registerables);

const props = defineProps<{
    stats: {
        total_students: number;
        total_sections: number;
        total_assignments: number;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/teacher-dashboard' }];

const barChartInstance = ref<Chart | null>(null);
const lineChartInstance = ref<Chart | null>(null);

const hoverBorders = ['hover:border-[#ff69b4]', 'hover:border-[#01006c]', 'hover:border-[#ffc60b]'];

function getCardClasses(index: number) {
    const borderHover = hoverBorders[index % hoverBorders.length];
    return `relative overflow-hidden rounded-xl border border-pink-200 bg-white p-5 shadow-md transition-all hover:scale-[1.02] hover:shadow-xl ${borderHover}`;
}

onMounted(() => {
    const barCtx = document.getElementById('barChart') as HTMLCanvasElement;
    const lineCtx = document.getElementById('lineChart') as HTMLCanvasElement;

    barChartInstance.value = new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: ['Students', 'Sections', 'Assignments'],
            datasets: [
                {
                    label: 'Overview',
                    data: [props.stats.total_students, props.stats.total_sections, props.stats.total_assignments],
                    backgroundColor: ['#3B82F6', '#10B981', '#8B5CF6'],
                },
            ],
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Bar Chart Overview' },
            },
        },
    });

    lineChartInstance.value = new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: ['Students', 'Sections', 'Assignments'],
            datasets: [
                {
                    label: 'Trends',
                    data: [props.stats.total_students, props.stats.total_sections, props.stats.total_assignments],
                    fill: false,
                    borderColor: '#EF4444',
                    tension: 0.3,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'Line Chart Overview' },
            },
        },
    });
});

watch(
    () => props.stats,
    (newStats) => {
        const newData = [newStats.total_students, newStats.total_sections, newStats.total_assignments];

        if (barChartInstance.value) {
            barChartInstance.value.data.datasets[0].data = newData;
            barChartInstance.value.update();
        }

        if (lineChartInstance.value) {
            lineChartInstance.value.data.datasets[0].data = newData;
            lineChartInstance.value.update();
        }
    },
    { deep: true },
);
</script>

<template>
    <Head title="Teacher Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen space-y-6 bg-pink-50 px-6 py-8">
            <h1 class="flex items-center gap-2 text-3xl font-bold text-[#01006c]">Teacher Dashboard</h1>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div :class="getCardClasses(0)">
                    <div class="absolute top-0 left-0 h-1 w-full bg-yellow-400"></div>
                    <div class="flex h-full flex-col justify-between">
                        <div class="pb-4">
                            <h2 class="flex items-center gap-2 text-lg font-semibold text-[#01006c]"><Users class="h-5 w-5" /> Total Students</h2>
                            <p class="mt-2 text-4xl font-bold text-blue-800">{{ props.stats.total_students }}</p>
                        </div>
                    </div>
                </div>

                <div :class="getCardClasses(1)">
                    <div class="absolute top-0 left-0 h-1 w-full bg-pink-400"></div>
                    <div class="flex h-full flex-col justify-between">
                        <div class="pb-4">
                            <h2 class="flex items-center gap-2 text-lg font-semibold text-[#01006c]"><Layers class="h-5 w-5" /> Total Sections</h2>
                            <p class="mt-2 text-4xl font-bold text-green-800">{{ props.stats.total_sections }}</p>
                        </div>
                    </div>
                </div>

                <div :class="getCardClasses(2)">
                    <div class="absolute top-0 left-0 h-1 w-full bg-indigo-400"></div>
                    <div class="flex h-full flex-col justify-between">
                        <div class="pb-4">
                            <h2 class="flex items-center gap-2 text-lg font-semibold text-[#01006c]">
                                <ClipboardList class="h-5 w-5" /> Total Assignments
                            </h2>
                            <p class="mt-2 text-4xl font-bold text-purple-800">{{ props.stats.total_assignments }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="rounded-xl bg-white p-6 shadow transition hover:shadow-lg">
                    <h2 class="mb-2 text-lg font-semibold text-indigo-800">📊 Bar Chart Overview</h2>
                    <canvas id="barChart"></canvas>
                </div>

                <div class="rounded-xl bg-white p-6 shadow transition hover:shadow-lg">
                    <h2 class="mb-2 text-lg font-semibold text-red-600">📈 Line Chart Overview</h2>
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
