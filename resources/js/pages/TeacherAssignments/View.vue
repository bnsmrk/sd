<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps<{
    assignment: {
        id: number;
        teacher: { id: number; name: string };
        year_level: { id: number; name: string };
        section: { id: number; name: string } | null;
        subject: { id: number; name: string };
    };
}>();
</script>

<template>
  <Head title="View Assignment" />
  <AppLayout :breadcrumbs="[{ title: 'Assignments', href: '/teacher-assignments' }]">
    <!-- Wider layout wrapper -->
    <div class="max-w-6xl mx-auto mt-10 px-6 space-y-8">
      
      <!-- Header Row -->
      <div class="flex items-center justify-between">
        <h1 class="text-lg font-bold text-gray-900 dark:text-white">Teacher Assignment Details</h1>
        <Link
          href="/teacher-assignments"
          class="text-sm font-medium text-white bg-gray-800 hover:bg-gray-900 focus:ring-2 focus:ring-gray-300 rounded-lg px-2 py-2 dark:hover:bg-gray-700 dark:focus:ring-gray-700"
        >
          ← Back to List
        </Link>
      </div>

      <!-- Info Card -->
      <div class="grid grid-cols-2 gap-2 bg-white dark:bg-gray-800 p-4 rounded-lg shadow border border-gray-200 dark:border-gray-700">
        <!-- Teacher -->
        <div>
          <label class="block mb-1 text-sm font-medium text-gray-600 dark:text-gray-300">Teacher</label>
          <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ props.assignment.teacher.name }}</p>
        </div>

        <!-- Year Level -->
        <div>
          <label class="block mb-1 text-sm font-medium text-gray-600 dark:text-gray-300">Year Level</label>
          <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ props.assignment.year_level.name }}</p>
        </div>

        <!-- Section -->
        <div>
          <label class="block mb-1 text-sm font-medium text-gray-600 dark:text-gray-300">Section</label>
          <p class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ props.assignment.section?.name ?? '— (Shared Subject)' }}
          </p>
        </div>

        <!-- Subject -->
        <div>
          <label class="block mb-1 text-sm font-medium text-gray-600 dark:text-gray-300">Subject</label>
          <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ props.assignment.subject.name }}</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>



