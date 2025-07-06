<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

// Lucide icons
import { BookOpen, Pencil, Trash2, Eye, PlusCircle,XCircle } from 'lucide-vue-next';

const props = defineProps<{
  tests: Array<{
    id: number;
    title: string;
    type: string;
    scheduled_at: string;
    due_date: string;
    year_level: { name: string };
  }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Proficiency Test', href: '/proficiency-test' }];

const showDeleteModal = ref(false);
const deleteId = ref<number | null>(null);

function confirmDelete(id: number) {
  deleteId.value = id;
  showDeleteModal.value = true;
}

function destroyTest() {
  if (deleteId.value !== null) {
    router.delete(`/proficiency-test/${deleteId.value}`);
    showDeleteModal.value = false;
    deleteId.value = null;
  }
}

function cancelDelete() {
  showDeleteModal.value = false;
  deleteId.value = null;
}
</script>

<template>
  <Head title="Proficiency Test" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-[#01006c]">Proficiency Tests</h1>
        <Link
          href="/proficiency-test/create"
          class="inline-flex items-center gap-2 rounded bg-[#01006c] px-4 py-2 text-white hover:bg-[#0d1282]"
        >
          <PlusCircle class="h-5 w-5" />
          Create Test
        </Link>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto rounded-lg border border-gray-300">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-[#01006c] text-white">
            <tr>
              <th class="px-4 py-2 text-left font-semibold">Title</th>
              <th class="px-4 py-2 text-left font-semibold">Type</th>
              <th class="px-4 py-2 text-left font-semibold">Scheduled At</th>
              <th class="px-4 py-2 text-left font-semibold">Due Date</th>
              <th class="px-4 py-2 text-left font-semibold">Year Level</th>
              <th class="px-4 py-2 text-left font-semibold">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="test in props.tests" :key="test.id" class="border-b">
              <td class="px-4 py-2 flex items-center gap-2">
                <BookOpen class="h-4 w-4 text-blue-500" />
                {{ test.title }}
              </td>
              <td class="px-4 py-2 capitalize">{{ test.type }}</td>
              <td class="px-4 py-2">{{ new Date(test.scheduled_at).toLocaleString() }}</td>
              <td class="px-4 py-2">{{ new Date(test.due_date).toLocaleString() }}</td>
              <td class="px-4 py-2">{{ test.year_level.name }}</td>
              <td class="space-x-2 px-4 py-2">
                <Link
                  :href="`/proficiency-test/${test.id}/edit`"
                  class="inline-flex items-center gap-1 text-blue-600 hover:underline"
                >
                  <Pencil class="h-4 w-4" /> Edit
                </Link>
                <Link
                  :href="`/proficiency-test/${test.id}/questions/create`"
                  class="inline-flex items-center gap-1 text-indigo-600 hover:underline"
                >
                  <PlusCircle class="h-4 w-4" /> Add Questions
                </Link>
                <button
                  @click="confirmDelete(test.id)"
                  class="inline-flex items-center gap-1 text-red-600 hover:underline"
                >
                  <Trash2 class="h-4 w-4" /> Delete
                </button>
              </td>
            </tr>
            <tr v-if="props.tests.length === 0">
              <td colspan="6" class="px-4 py-4 text-center text-gray-500">No proficiency tests found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
<div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-white/30 backdrop-blur-sm">
  <div class="w-full max-w-md rounded bg-white p-6 shadow-lg">
    <h2 class="mb-4 text-lg font-semibold text-gray-900">Confirm Deletion</h2>
    <p class="mb-6 text-gray-600">Are you sure you want to delete this test?</p>
    <div class="flex justify-end space-x-4">
      <!-- Cancel Button -->
      <button
        @click="cancelDelete"
        class="inline-flex items-center gap-2 rounded bg-gray-300 px-4 py-2 text-sm text-gray-800 hover:bg-gray-400"
      >
        <XCircle class="w-4 h-4 text-gray-700" />
        Cancel
      </button>

      <!-- Confirm Delete Button -->
      <button
        @click="destroyTest"
        class="inline-flex items-center gap-2 rounded bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700"
      >
        <Trash2 class="w-4 h-4 text-white" />
        Confirm
      </button>
    </div>
  </div>
</div>

  </AppLayout>
</template>
