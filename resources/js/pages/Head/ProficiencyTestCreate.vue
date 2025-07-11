<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, BookOpen, CalendarClock, CalendarDays, FileText, ListChecks, Send } from 'lucide-vue-next';

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
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-[#01006c]">Create Proficiency Test</h1>
                <Link
                    href="/proficiency-test"
                    class="inline-flex items-center gap-2 rounded border border-[#01006c] bg-white px-4 py-2 text-sm font-semibold text-[#01006c] shadow hover:bg-[#ffc60b]"
                >
                    <ArrowLeft class="h-4 w-4 text-gray-600" />
                    <span>Back</span>
                </Link>
            </div>

            <form @submit.prevent="form.post('/proficiency-test')" class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Title -->
                <div class="col-span-1">
                    <label class="mb-1 block flex items-center gap-2 text-sm font-medium text-[#ff69b4]">
                        <FileText class="h-4 w-4 text-[#ff69b4]" />
                        Title
                    </label>
                    <input
                        v-model="form.title"
                        type="text"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                    />
                </div>

                <!-- Type -->
                <div class="col-span-1">
                    <label class="mb-1 block flex items-center gap-2 text-sm font-medium text-[#ff69b4]">
                        <ListChecks class="h-4 w-4 text-[#ff69b4]" />
                        Type
                    </label>
                    <select
                        v-model="form.type"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                    >
                        <option value="reading">Reading</option>
                        <option value="numerical">Numerical</option>
                    </select>
                </div>

                <!-- Year Level -->
                <div class="col-span-1">
                    <label class="mb-1 block flex items-center gap-2 text-sm font-medium text-[#ff69b4]">
                        <BookOpen class="h-4 w-4 text-[#ff69b4]" />
                        Year Level
                    </label>
                    <select
                        v-model="form.year_level_id"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                    >
                        <option disabled value="">-- Select Year Level --</option>
                        <option v-for="level in props.yearLevels" :key="level.id" :value="level.id">
                            {{ level.name }}
                        </option>
                    </select>
                </div>

                <!-- Scheduled At -->
                <div class="col-span-1">
                    <label class="mb-1 block flex items-center gap-2 text-sm font-medium text-[#ff69b4]">
                        <CalendarClock class="h-4 w-4 text-[#ff69b4]" />
                        Scheduled At
                    </label>
                    <input
                        v-model="form.scheduled_at"
                        type="datetime-local"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                    />
                </div>

                <!-- Due Date -->
                <div class="col-span-1">
                    <label class="mb-1 block flex items-center gap-2 text-sm font-medium text-[#ff69b4]">
                        <CalendarDays class="h-4 w-4 text-[#ff69b4]" />
                        Due Date
                    </label>
                    <input
                        v-model="form.due_date"
                        type="datetime-local"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                    />
                </div>

                <!-- Description -->
                <div class="col-span-full">
                    <label class="mb-1 block flex items-center gap-2 text-sm font-medium text-[#ff69b4]">
                        <FileText class="h-4 w-4 text-[#ff69b4]" />
                        Description
                    </label>
                    <textarea
                        v-model="form.description"
                        rows="4"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                    ></textarea>
                </div>

                <!-- Submit -->
                <div class="col-span-full">
                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded bg-[#01006c] px-4 py-2 text-white hover:bg-[#0d1282]"
                    >
                        <Send class="h-5 w-5" />
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
