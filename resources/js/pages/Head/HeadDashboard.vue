<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import { Chart, registerables } from 'chart.js';

// Register Chart.js plugins
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
}>()

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
    <div class="space-y-6 p-4 sm:p-6 max-w-full mx-auto">
      <h1 class="text-xl sm:text-2xl font-bold text-[#01006c]">Head Dashboard</h1>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="level in props.yearLevels"
          :key="level.id"
          class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow flex flex-col justify-between"
        >
          <p class="text-sm text-gray-500">Year Level</p>
          <p class="text-lg font-bold">{{ level.name }}</p>
          <p class="text-blue-600 dark:text-blue-400">{{ level.students_count }} students</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
          <p class="text-sm text-gray-500">Total Sections</p>
          <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ props.sectionCount }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
          <p class="text-sm text-gray-500">Total Teachers</p>
          <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ props.teacherCount }}</p>
        </div>
      </div>

      <!-- Calendar -->
      <div class="bg-white dark:bg-gray-800 rounded-lg sha  dow p-4">
        <FullCalendar :options="calendarOptions" />
      </div>

      <!-- Chart -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 sm:p-6 mt-4 h-[400px]">
        <canvas id="chart" class="w-full h-full"></canvas>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.fc {
  font-size: 0.95rem;
}
</style>
