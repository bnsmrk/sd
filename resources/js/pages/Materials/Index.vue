<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { BookOpen, Pencil, Trash2, Eye } from 'lucide-vue-next';
defineProps<{
    materials: Array<{
        id: number;
        title: string;
        type: string;
        file_path: string;
        year_level: { name: string };
        section?: { name: string };
        subject: { name: string };
        user: { name: string };
        comments?: Array<{
            id: number;
            comment: string;
            user: { name: string };
        }>;
    }>;
}>();

const showDeleteModal = ref(false);
const deleteId = ref<number | null>(null);

function createMaterial() {
    router.get('/materials/create');
}

function editMaterial(id: number) {
    router.get(`/materials/${id}/edit`);
}

function confirmDelete(id: number) {
    deleteId.value = id;
    showDeleteModal.value = true;
}

function destroyMaterial() {
    if (deleteId.value !== null) {
        router.delete(`/materials/${deleteId.value}`);
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
  <Head title="My Materials" />
  <AppLayout>
    <div class="space-y-6 p-6">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
          <BookOpen class="w-6 h-6 text-blue-600" /> My Uploaded Materials
        </h1>
        <button
          @click="createMaterial"
          class="inline-flex items-center gap-2 rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 transition"
        >
          <BookOpen class="w-4 h-4" />
          <span>Upload Material</span>
        </button>
      </div>

      <div class="overflow-x-auto rounded-lg shadow">
        <table class="min-w-full table-auto border border-gray-200 bg-white">
          <thead class="bg-gray-100 text-left text-sm font-semibold text-gray-700">
            <tr>
              <th class="p-3">Title</th>
              <th class="p-3">Type</th>
              <th class="p-3">Year Level</th>
              <th class="p-3">Section</th>
              <th class="p-3">Subject</th>
              <th class="p-3">File</th>
              <th class="p-3 text-center">Actions</th>
            </tr>
          </thead>
          <tbody class="text-sm text-gray-700">
            <template v-for="material in materials" :key="material.id">
              <tr class="border-t hover:bg-gray-50">
                <td class="p-3 align-top">{{ material.title }}</td>
                <td class="p-3 align-top capitalize">{{ material.type.replace('_', ' ') }}</td>
                <td class="p-3 align-top">{{ material.year_level.name }}</td>
                <td class="p-3 align-top">{{ material.section?.name || '—' }}</td>
                <td class="p-3 align-top">{{ material.subject.name }}</td>
                <td class="p-3 align-top">
                  <a
                    :href="`/storage/${material.file_path}`"
                    target="_blank"
                    class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-medium transition"
                  >
                    <Eye class="w-4 h-4" />
                    <span>View</span>
                  </a>
                </td>
                <td class="p-3 align-top text-center">
                  <div class="flex justify-center gap-3">
                    <button
                      class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-medium transition"
                      @click="editMaterial(material.id)"
                    >
                      <Pencil class="w-4 h-4" />
                      <span>Edit</span>
                    </button>
                    <button
                      class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 font-medium transition"
                      @click="confirmDelete(material.id)"
                    >
                      <Trash2 class="w-4 h-4" />
                      <span>Delete</span>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- Comments -->
              <tr v-if="material.comments?.length" class="bg-gray-50 text-sm">
                <td colspan="7" class="p-4">
                  <p class="mb-2 font-medium text-gray-700">Comments from Principal:</p>
                  <ul class="ml-4 list-disc space-y-1 text-gray-600">
                    <li v-for="comment in material.comments" :key="comment.id">
                      "{{ comment.comment }}" — <i>{{ comment.user.name }}</i>
                    </li>
                  </ul>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
      <div class="w-full max-w-md rounded bg-white p-6 shadow-lg dark:bg-gray-800">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Confirm Deletion</h2>
        <p class="mb-6 text-gray-600 dark:text-gray-300">Are you sure you want to delete this material?</p>
        <div class="flex justify-end space-x-4">
          <button
            @click="cancelDelete"
            class="rounded bg-gray-300 px-4 py-2 text-sm text-gray-800 hover:bg-gray-400 dark:bg-gray-600 dark:text-white dark:hover:bg-gray-500"
          >
            Cancel
          </button>
          <button @click="destroyMaterial" class="rounded bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700">Confirm</button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
