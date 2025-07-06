<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm, Link } from '@inertiajs/vue3';
import {
  ArrowLeft,
  BookOpen,
  CalendarClock,
  CalendarDays,
  FileText,
  ListChecks,
  Send
} from 'lucide-vue-next';

const props = defineProps<{
    test: {
        id: number;
        title: string;
        type: string;
        year_level_id: number;
        scheduled_at: string;
        due_date: string;
        description: string | null;
    };
    yearLevels: Array<{ id: number; name: string }>;
}>();

const form = useForm({
    title: props.test.title,
    type: props.test.type,
    year_level_id: props.test.year_level_id,
    scheduled_at: props.test.scheduled_at,
    due_date: props.test.due_date ,
    description: props.test.description ?? '',
});
function cancelEdit() {
    router.get('/proficiency-test');
}
</script>

<template>
    <Head title="Edit Proficiency Test" />
    <AppLayout>
      <div class="mx-auto w-full max-w-4xl space-y-6 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
          <h1 class="text-2xl font-bold text-[#01006c]">Edit Proficiency Test</h1>
          <button
            @click.prevent="cancelEdit"
            class="inline-flex items-center gap-2 rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 hover:text-gray-900 transition"
          >
            <ArrowLeft class="w-4 h-4 text-gray-600" />
            Back
          </button>
        </div>
  
        <!-- Form -->
        <form @submit.prevent="form.put(`/proficiency-test/${props.test.id}`)" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Title -->
          <div class="col-span-1">
            <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-2">
              <FileText class="w-4 h-4 text-gray-500" />
              Title
            </label>
            <input
              v-model="form.title"
              type="text"
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#01006c] focus:border-transparent"
              required
            />
          </div>
  
          <!-- Type -->
          <div class="col-span-1">
            <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-2">
              <ListChecks class="w-4 h-4 text-gray-500" />
              Type
            </label>
            <select
              v-model="form.type"
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#01006c] focus:border-transparent"
              required
            >
              <option value="reading">Reading</option>
              <option value="numerical">Numerical</option>
            </select>
          </div>
  
          <!-- Year Level -->
          <div class="col-span-1">
            <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-2">
              <BookOpen class="w-4 h-4 text-gray-500" />
              Year Level
            </label>
            <select
              v-model="form.year_level_id"
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#01006c] focus:border-transparent"
              required
            >
              <option disabled value="">-- Choose Year Level --</option>
              <option v-for="yl in props.yearLevels" :key="yl.id" :value="yl.id">{{ yl.name }}</option>
            </select>
          </div>
  
          <!-- Scheduled At -->
          <div class="col-span-1">
            <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-2">
              <CalendarClock class="w-4 h-4 text-gray-500" />
              Scheduled At
            </label>
            <input
              v-model="form.scheduled_at"
              type="datetime-local"
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#01006c] focus:border-transparent"
              required
            />
          </div>
  
          <!-- Due Date -->
          <div class="col-span-1">
            <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-2">
              <CalendarDays class="w-4 h-4 text-gray-500" />
              Due Date
            </label>
            <input
              v-model="form.due_date"
              type="datetime-local"
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#01006c] focus:border-transparent"
            />
          </div>
  
          <!-- Description -->
          <div class="col-span-full">
            <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-2">
              <FileText class="w-4 h-4 text-gray-500" />
              Description
            </label>
            <textarea
              v-model="form.description"
              rows="4"
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#01006c] focus:border-transparent"
            ></textarea>
          </div>
  
          <!-- Buttons -->
          <div class="col-span-full flex gap-4 justify-end pt-4">
            <button
              type="button"
              @click.prevent="cancelEdit"
              class="inline-flex items-center gap-2 rounded bg-gray-500 px-4 py-2 text-sm text-white hover:bg-gray-700"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="inline-flex items-center gap-2 rounded bg-[#01006c] px-4 py-2 text-sm text-white hover:bg-[#0d1282]"
            >
              <Send class="w-4 h-4" />
              Update
            </button>
          </div>
        </form>
      </div>
    </AppLayout>
  </template>
  

<style scoped>
.floating-input {
    width: 100%;
    border: 2px solid #01006c;
    border-radius: 0.5rem;
    padding: 0.5rem 0.75rem;
    margin-top: 0.25rem;
}
</style>
