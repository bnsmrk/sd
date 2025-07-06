<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm , Link} from '@inertiajs/vue3';
import { ArrowLeft, BookOpen, CalendarClock, CalendarDays, ClipboardList, FileText, ListChecks, ScrollText, Send } from 'lucide-vue-next';


const props = defineProps<{
  yearLevels: Array<{ id: number; name: string }>;
  sections: Array<{ id: number; name: string }>;
  subjects: Array<{ id: number; name: string }>;
}>();

const form = useForm({
  title: '',
  type: 'reading',
  year_level_id: '',
  section_id: '',
  subject_id: '',
  scheduled_at: '',
  due_date: '',
  description: '',
});
</script>

<template>
    <Head title="Create Proficiency Test" />
  
    <AppLayout>
      <div class="mx-auto w-full max-w-7xl space-y-6 p-6">
        <!-- Header with Back Button -->
        <div class="flex items-center justify-between">
          <h1 class="text-2xl font-bold text-[#01006c]">Create Proficiency Test</h1>
          <Link
            href="/proficiency-test"
            class="inline-flex items-center gap-2 rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 hover:text-gray-900 transition"
          >
            <ArrowLeft class="w-4 h-4 text-gray-600" />
            <span>Back</span>
          </Link>
        </div>
  
        <!-- Form -->
        <form @submit.prevent="form.post('/proficiency-test')" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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
            >
              <option disabled value="">-- Select Year Level --</option>
              <option v-for="level in props.yearLevels" :key="level.id" :value="level.id">
                {{ level.name }}
              </option>
            </select>
          </div>
  
          <!-- Section -->
          <!-- <div class="col-span-1">
            <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-2">
              <ClipboardList class="w-4 h-4 text-gray-500" />
              Section
            </label>
            <select
              v-model="form.section_id"
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#01006c] focus:border-transparent"
            >
              <option disabled value="">-- Select Section --</option>
              <option v-for="section in props.sections" :key="section.id" :value="section.id">
                {{ section.name }}
              </option>
            </select>
          </div> -->
  
          <!-- Subject -->
          <!-- <div class="col-span-1">
            <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-2">
              <ScrollText class="w-4 h-4 text-gray-500" />
              Subject
            </label>
            <select
              v-model="form.subject_id"
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#01006c] focus:border-transparent"
            >
              <option disabled value="">-- Select Subject --</option>
              <option v-for="subject in props.subjects" :key="subject.id" :value="subject.id">
                {{ subject.name }}
              </option>
            </select>
          </div> -->
  
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
  
          <!-- Submit Button -->
          <div class="col-span-full">
            <button
              type="submit"
              class="inline-flex items-center justify-center gap-2 w-full rounded bg-[#01006c] px-4 py-2 text-white hover:bg-[#0d1282]"
            >
              <Send class="w-5 h-5" />
              Submit
            </button>
          </div>
        </form>
      </div>
    </AppLayout>
  </template>
  
