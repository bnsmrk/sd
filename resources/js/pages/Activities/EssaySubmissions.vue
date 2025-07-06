<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Eye, CheckCircle2, XCircle } from 'lucide-vue-next';

const props = defineProps<{
  activity: { id: number; title: string };
  submissions: {
    id: number;
    content: string | null;
    file_path: string | null;
    score: number | null;
    user: { id: number; name: string };
  }[];
}>();
</script>

<template>
  <AppLayout>
    <Head :title="`Essay Submissions - ${props.activity.title}`" />

    <div class="mx-auto w-full max-w-screen-2xl space-y-6 p-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-800">
          Essay Submissions – {{ props.activity.title }}
        </h1>

        <Link
          href="/activities"
          class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600 hover:underline transition"
        >
          <ArrowLeft class="w-4 h-4" /> Back to Activities
        </Link>
      </div>

      <!-- No Submissions -->
      <div
        v-if="props.submissions.length === 0"
        class="rounded-lg bg-yellow-50 px-4 py-3 text-yellow-700 shadow-sm"
      >
        No students have submitted yet.
      </div>

      <!-- Submissions Table -->
      <div v-else class="overflow-x-auto rounded-lg border bg-white shadow-sm">
        <table class="min-w-full divide-y text-sm text-gray-700">
          <thead class="bg-gray-100 text-left text-sm font-semibold">
            <tr>
              <th class="px-6 py-3">Student</th>
              <th class="px-6 py-3">Graded</th>
              <th class="px-6 py-3">Score</th>
              <th class="px-6 py-3">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr
              v-for="submission in props.submissions"
              :key="submission.id"
              class="hover:bg-gray-50"
            >
              <td class="px-6 py-3 font-medium">{{ submission.user.name }}</td>

              <td class="px-6 py-3">
                <span
                  v-if="submission.score !== null"
                  class="inline-flex items-center gap-1 rounded bg-green-100 px-2 py-1 text-green-700"
                >
                  <CheckCircle2 class="w-4 h-4" /> Graded
                </span>
                <span
                  v-else
                  class="inline-flex items-center gap-1 rounded bg-red-100 px-2 py-1 text-red-700"
                >
                  <XCircle class="w-4 h-4" /> Not Graded
                </span>
              </td>

              <td class="px-6 py-3 font-semibold">
                <span v-if="submission.score !== null">{{ submission.score }}</span>
                <span v-else>—</span>
              </td>

              <td class="px-6 py-3">
                <Link
                  :href="`/submissions/${submission.id}`"
                  class="inline-flex items-center gap-1 text-blue-600 hover:underline"
                >
                  <Eye class="w-4 h-4" /> View
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
