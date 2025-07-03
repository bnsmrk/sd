<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';

import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Head Dashboard',
    href: '/head-dashboard',
  },
];

// Sample events (you can fetch from backend later)
const calendarEvents = ref([
  { title: 'English Proficiency Test', date: '2025-07-04' },
  { title: 'Science Activity', date: '2025-07-06' },
]);

const calendarOptions = ref({
  plugins: [dayGridPlugin],
  initialView: 'dayGridMonth',
  events: calendarEvents.value,
});
</script>

<template>
  <Head title="Head Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
      <h1 class="text-2xl font-bold text-[#01006c]">Head Dashboard</h1>

      <!-- FullCalendar -->
      <div class="bg-white rounded-xl shadow p-4">
        <FullCalendar :options="calendarOptions" />
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Optional: better calendar spacing */
.fc {
  font-size: 0.95rem;
}
</style>
