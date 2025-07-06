<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
  ArrowLeft,
  CheckCircle2,
  XCircle,
  Download,
  FileText,
  Medal,
} from 'lucide-vue-next';

const props = defineProps<{
  submission: {
    id: number;
    content: string | null;
    file_path: string | null;
    score: number | null;
    graded: string;
    user: { id: number; name: string };
    activity: { id: number; title: string };
  };
}>();

const score = ref<number | null>(props.submission.score ?? null);

function saveScore() {
  if (score.value !== null) {
    router.post(
      `/submissions/${props.submission.id}/score`,
      { score: score.value },
      { preserveScroll: true }
    );
  }
}
</script>

<template>
  <AppLayout>
    <Head :title="`View Essay - ${props.submission.user.name}`" />

    <div class="mx-auto w-full max-w-screen-2xl space-y-6 p-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-800">
          Essay by {{ props.submission.user.name }} – {{ props.submission.activity.title }}
        </h1>

        <a
          href="/activities"
          class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600 hover:underline transition"
        >
          <ArrowLeft class="w-4 h-4" /> Back to Activities
        </a>
      </div>

      <!-- Status -->
      <div class="flex items-center gap-2 text-sm text-gray-600">
        Status:
        <span
          :class="submission.graded === 'Graded'
            ? 'inline-flex items-center gap-1 text-green-600'
            : 'inline-flex items-center gap-1 text-red-600'"
        >
          <component
            :is="submission.graded === 'Graded' ? CheckCircle2 : XCircle"
            class="w-4 h-4"
          />
          {{ submission.graded }}
        </span>
      </div>

      <!-- Essay Content -->
      <div v-if="submission.content" class="rounded bg-gray-50 p-4 text-gray-800 whitespace-pre-wrap shadow-sm">
        {{ submission.content }}
      </div>
      <div v-else class="italic text-gray-500">No essay content submitted.</div>

      <!-- Download -->
      <div v-if="submission.file_path">
        <a
          :href="`/storage/${submission.file_path}`"
          target="_blank"
          class="inline-flex items-center gap-1 text-blue-600 hover:underline"
        >
          <Download class="w-4 h-4" /> Download Attached File
        </a>
      </div>

      <!-- Score Input -->
      <div class="mt-6">
        <label class="flex items-center gap-1 text-sm font-medium text-gray-700 mb-1">
          <Medal class="w-4 h-4" /> Score
        </label>
        <input
          v-model.number="score"
          type="number"
          min="0"
          max="100"
          class="w-32 rounded border px-3 py-2 text-sm"
          placeholder="0–100"
        />

        <button
          @click="saveScore"
          class="ml-3 inline-flex items-center gap-1 rounded bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700"
        >
          <FileText class="w-4 h-4" /> Save Score
        </button>
      </div>
    </div>
  </AppLayout>
</template>
