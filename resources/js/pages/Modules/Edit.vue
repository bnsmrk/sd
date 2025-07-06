<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import {
  Pencil,
  BookText,
  Users,
  School,
  ArrowLeft,
  FileSignature,
} from 'lucide-vue-next';

const props = defineProps<{
  module: {
    id: number;
    name: string;
    year_level_id: number;
    section_id: number;
    subject_id: number;
  };
  assignments: Array<{
    id: number;
    name: string;
    sections: Array<{ id: number; name: string }>;
    subjects: Array<{ id: number; name: string; section_ids: number[] }>;
  }>;
}>();

const form = useForm({
  name: props.module.name,
  year_level_id: props.module.year_level_id,
  section_id: props.module.section_id,
  subject_id: props.module.subject_id,
});

const selectedYearLevel = computed(() =>
  props.assignments.find((a) => a.id === form.year_level_id)
);

const availableSections = computed(() =>
  selectedYearLevel.value ? selectedYearLevel.value.sections : []
);

const filteredSubjects = computed(() => {
  if (!selectedYearLevel.value || !form.section_id) return [];
  return selectedYearLevel.value.subjects.filter((subject) =>
    subject.section_ids.includes(form.section_id)
  );
});

watch(
  () => form.year_level_id,
  () => {
    form.section_id = 0;
    form.subject_id = 0;
  }
);

watch(
  () => form.section_id,
  () => {
    form.subject_id = 0;
  }
);
</script>

<template>
  <Head title="Edit Module" />
  <AppLayout>
    <div class="w-full max-w-5xl mx-auto space-y-6 px-6">

      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-800 inline-flex items-center gap-2">
          <Pencil class="w-5 h-5" /> Edit Module
        </h1>
        <Link
          href="/modules"
          class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600 hover:underline transition"
        >
          <ArrowLeft class="w-4 h-4" /> Back to Modules
        </Link>
      </div>

      <form @submit.prevent="form.put(`/modules/${props.module.id}`)" class="space-y-4">
        <!-- Module Name -->
        <div>
          <label class="flex items-center gap-1 text-sm font-medium text-gray-700">
            <FileSignature class="w-4 h-4" /> Module Name
          </label>
          <input v-model="form.name" class="w-full rounded border p-2" required />
          <div v-if="form.errors.name" class="text-sm text-red-600">
            {{ form.errors.name }}
          </div>
        </div>

        <!-- Year Level -->
        <div>
          <label class="flex items-center gap-1 text-sm font-medium text-gray-700">
            <School class="w-4 h-4" /> Year Level
          </label>
          <select v-model.number="form.year_level_id" class="w-full rounded border p-2" required>
            <option value="">Select Year Level</option>
            <option v-for="yl in props.assignments" :key="yl.id" :value="yl.id">
              {{ yl.name }}
            </option>
          </select>
          <div v-if="form.errors.year_level_id" class="text-sm text-red-600">
            {{ form.errors.year_level_id }}
          </div>
        </div>

        <!-- Section -->
        <div v-if="availableSections.length > 0">
          <label class="flex items-center gap-1 text-sm font-medium text-gray-700">
            <Users class="w-4 h-4" /> Section
          </label>
          <select v-model.number="form.section_id" class="w-full rounded border p-2" required>
            <option value="">Select Section</option>
            <option v-for="section in availableSections" :key="section.id" :value="section.id">
              {{ section.name }}
            </option>
          </select>
          <div v-if="form.errors.section_id" class="text-sm text-red-600">
            {{ form.errors.section_id }}
          </div>
        </div>

        <!-- Subject -->
        <div v-if="filteredSubjects.length > 0">
          <label class="flex items-center gap-1 text-sm font-medium text-gray-700">
            <BookText class="w-4 h-4" /> Subject
          </label>
          <select v-model.number="form.subject_id" class="w-full rounded border p-2" required>
            <option value="">Select Subject</option>
            <option v-for="subject in filteredSubjects" :key="subject.id" :value="subject.id">
              {{ subject.name }}
            </option>
          </select>
          <div v-if="form.errors.subject_id" class="text-sm text-red-600">
            {{ form.errors.subject_id }}
          </div>
        </div>

        <!-- Submit -->
        <div>
          <button class="w-full rounded bg-green-600 py-2 text-white hover:bg-green-700" :disabled="form.processing">
            Update
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
