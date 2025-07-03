<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    teachers: Array<{ id: number; name: string }>;
    yearLevels: Array<{ id: number; name: string }>;
    sections: Array<{ id: number; name: string; year_level_id: number }>;
    subjects: Array<{ id: number; name: string; year_level_id: number; section_id: number | null }>;
}>();

const form = useForm({
    user_id: '',
    year_level_id: '',
    section_id: '',
    subject_id: '',
});

const filteredSections = computed(() => props.sections.filter((section) => section.year_level_id === Number(form.year_level_id)));

const filteredSubjects = computed(() => {
    const ylId = Number(form.year_level_id);
    const secId = Number(form.section_id);

    // Filter shared or matched section subjects for the selected year level
    return props.subjects.filter((subject) => subject.year_level_id === ylId && (subject.section_id === null || subject.section_id === secId));
});
</script>

<template>
  <Head title="Assign Teacher" />
  <AppLayout>
    <form @submit.prevent="form.post('/teacher-assignments')" class="max-w-3xl mx-auto mt-10 space-y-6 bg-white p-8 rounded-lg shadow dark:bg-gray-800">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Assign Teacher</h1>

      <div class="grid grid-cols-4 gap-4">
        <!-- Teacher -->
        <div>
          <label for="teacher" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Teacher</label>
          <select id="teacher" v-model="form.user_id" required
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <option value="">Select a Teacher</option>
            <option v-for="t in props.teachers" :key="t.id" :value="t.id">{{ t.name }}</option>
          </select>
          <p v-if="form.errors.user_id" class="text-sm text-red-600 mt-1">{{ form.errors.user_id }}</p>
        </div>

        <!-- Year Level -->
        <div>
          <label for="year" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Year Level</label>
          <select id="year" v-model="form.year_level_id" required
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <option value="">Select a Year Level</option>
            <option v-for="y in props.yearLevels" :key="y.id" :value="y.id">{{ y.name }}</option>
          </select>
          <p v-if="form.errors.year_level_id" class="text-sm text-red-600 mt-1">{{ form.errors.year_level_id }}</p>
        </div>

        <!-- Section -->
        <div>
          <label for="section" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Section</label>
          <select id="section" v-model="form.section_id" required
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <option value="">Select a Section</option>
            <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
          </select>
          <p v-if="form.errors.section_id" class="text-sm text-red-600 mt-1">{{ form.errors.section_id }}</p>
        </div>

        <!-- Subject -->
        <div>
          <label for="subject" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Subject</label>
          <select id="subject" v-model="form.subject_id" required
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <option value="">Select a Subject</option>
            <option v-for="s in filteredSubjects" :key="s.id" :value="s.id">{{ s.name }}</option>
          </select>
          <p v-if="form.errors.subject_id" class="text-sm text-red-600 mt-1">{{ form.errors.subject_id }}</p>
        </div>
      </div>

      <!-- Submit Button -->
      <div class="flex justify-end pt-4">
        <button type="submit"
          class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-8 py-2.5 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700"
          :disabled="form.processing">
          Assign Teacher
        </button>
      </div>
    </form>
  </AppLayout>
</template>

