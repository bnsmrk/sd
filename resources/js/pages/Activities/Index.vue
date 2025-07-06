<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
  Pencil,
  Trash2,
  FileText,
  PlusCircle
} from 'lucide-vue-next';
const props = defineProps<{
    activities: {
        id: number;
        title: string;
        type: string;
        scheduled_at: string;
        due_date: string;
        year_level: string;
        section: string;
        subject: string;
    }[];
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Activities', href: '/activities' }];

const showDeleteModal = ref(false);
const deleteId = ref<number | null>(null);

function confirmDelete(id: number) {
    deleteId.value = id;
    showDeleteModal.value = true;
}

function deleteActivity() {
    if (deleteId.value !== null) {
        router.delete(`/activities/${deleteId.value}`);
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
  <Head title="Activities" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-4 p-4">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Activity List</h1>
        <Link
          href="/activities/create"
          class="inline-flex items-center gap-2 rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
        >
          <PlusCircle class="w-4 h-4" /> Create Activity
        </Link>
      </div>

      <!-- Table -->
      <div class="relative overflow-x-auto shadow ring-1 ring-gray-200 sm:rounded-lg">
        <table class="min-w-full text-sm text-left text-gray-600 dark:text-gray-300">
          <thead class="bg-gray-100 text-xs uppercase text-gray-600 dark:bg-gray-700 dark:text-gray-400">
            <tr>
              <th class="px-6 py-3">Title</th>
              <th class="px-6 py-3">Type</th>
              <th class="px-6 py-3">Scheduled At</th>
              <th class="px-6 py-3">Due Date</th>
              <th class="px-6 py-3 text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="a in props.activities"
              :key="a.id"
              class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700"
            >
              <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ a.title }}</td>

              <!-- Type Badge -->
              <td class="px-6 py-4">
                <span
                  class="inline-block rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800 capitalize dark:bg-blue-800 dark:text-blue-100"
                >
                  {{ a.type }}
                </span>
              </td>

              <td class="px-6 py-4">{{ a.scheduled_at }}</td>
              <td class="px-6 py-4">{{ a.due_date }}</td>

              <!-- Action Buttons -->
              <td class="px-6 py-4 text-center">
                <div class="flex justify-center gap-3">
                  <!-- Edit -->
                  <Link :href="`/activities/${a.id}/edit`" class="text-blue-600 hover:text-blue-800">
                    <Pencil class="w-4 h-4 inline" />
                  </Link>

                  <!-- Delete -->
                  <button @click="confirmDelete(a.id)" class="text-red-600 hover:text-red-800">
                    <Trash2 class="w-4 h-4 inline" />
                  </button>

                  <!-- View Essay / Add Question -->
                  <template v-if="a.type === 'essay'">
                    <Link
                      :href="`/activities/${a.id}/essay-submissions`"
                      class="text-purple-600 hover:text-purple-800"
                    >
                      <FileText class="w-4 h-4 inline" />
                    </Link>
                  </template>
                  <template v-else>
                    <Link
                      :href="`/activities/${a.id}/questions/create`"
                      class="text-indigo-600 hover:text-indigo-800"
                    >
                      <PlusCircle class="w-4 h-4 inline" />
                    </Link>
                  </template>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
      <div class="w-full max-w-md rounded bg-white p-6 shadow-xl dark:bg-gray-800">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Confirm Deletion</h2>
        <p class="mb-6 text-gray-600 dark:text-gray-300">Are you sure you want to delete this activity?</p>
        <div class="flex justify-end space-x-4">
          <button
            @click="cancelDelete"
            class="rounded bg-gray-300 px-4 py-2 text-sm text-gray-800 hover:bg-gray-400 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500"
          >
            Cancel
          </button>
          <button @click="deleteActivity" class="rounded bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700">
            Confirm
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

