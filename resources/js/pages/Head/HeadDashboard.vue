<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import dayGridPlugin from '@fullcalendar/daygrid';
import FullCalendar from '@fullcalendar/vue3';
import { Head } from '@inertiajs/vue3';
import { Chart, registerables } from 'chart.js';
import { onMounted, ref } from 'vue';

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

const calendarEvents = ref([
    { title: 'English Proficiency Test', date: '2025-07-04' },
    { title: 'Science Activity', date: '2025-07-06' },
]);

const calendarOptions = ref({
    plugins: [dayGridPlugin],
    initialView: 'dayGridMonth',
    events: calendarEvents.value,
});

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
        <div class="mx-auto max-w-full space-y-6 p-4 sm:p-6">
            <h1 class="text-xl font-bold text-[#01006c] sm:text-2xl">Head Dashboard</h1>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="level in props.yearLevels"
                    :key="level.id"
                    class="flex flex-col justify-between rounded-lg bg-white p-4 shadow dark:bg-gray-800"
                >
                    <p class="text-sm text-gray-500">Year Level</p>
                    <p class="text-lg font-bold">{{ level.name }}</p>
                    <p class="text-blue-600 dark:text-blue-400">{{ level.students_count }} students</p>
                </div>

                <div class="rounded-lg bg-white p-4 shadow dark:bg-gray-800">
                    <p class="text-sm text-gray-500">Total Sections</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ props.sectionCount }}</p>
                </div>

                <div class="rounded-lg bg-white p-4 shadow dark:bg-gray-800">
                    <p class="text-sm text-gray-500">Total Teachers</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ props.teacherCount }}</p>
                </div>
            </div>

            <div class="sha dow rounded-lg bg-white p-4 dark:bg-gray-800">
                <FullCalendar :options="calendarOptions" />
            </div>

            <div class="mt-4 h-[400px] rounded-lg bg-white p-4 shadow sm:p-6 dark:bg-gray-800">
                <canvas id="chart" class="h-full w-full"></canvas>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.fc {
    font-size: 0.95rem;
}
</style>
