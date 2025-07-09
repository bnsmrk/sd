<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

import { ArrowLeft, CalendarClock, ClipboardList, FileText, FileUp, Layers, Tag } from 'lucide-vue-next';
const props = defineProps<{
    modules: Array<{
        id: number;
        name: string;
        year_level: { name: string };
        section: { name: string };
        subject: { name: string };
    }>;
}>();

const form = useForm<{
    title: string;
    type: string;
    module_id: string;
    scheduled_at: string;
    due_date: string;
    description: string;
    file: File | null;
}>({
    title: '',
    type: 'quiz',
    module_id: '',
    scheduled_at: '',
    due_date: '',
    description: '',
    file: null,
});

const selectedModule = computed(() => props.modules.find((m) => m.id === Number(form.module_id)));
</script>

<template>
    <Head title="Create Activity" />
    <AppLayout>
        <div class="mx-auto max-w-4xl space-y-6 p-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-[#01006c]">Create Activity</h1>
                <Link
                    href="/activities"
                    class="inline-flex items-center gap-2 rounded border border-[#01006c] bg-white px-4 py-2 text-sm font-semibold text-[#01006c] shadow hover:bg-[#ffc60b]"
                >
                    <ArrowLeft class="h-4 w-4 text-[#ff69b4]" />
                    Back to Activities
                </Link>
            </div>

            <!-- Form -->
            <form @submit.prevent="form.post('/activities', { forceFormData: true })" class="space-y-6">
                <!-- Title, Type, Module -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label class="ttext-[#ff69b4] flex items-center gap-1 text-sm font-medium">
                            <Tag class="h-4 w-4 text-[#ff69b4]" /> Title
                        </label>
                        <input
                            v-model="form.title"
                            class="w-full rounded border px-3 py-2 focus:border-[#ffc60b] focus:ring-2 focus:ring-[#ffc60b] focus:outline-none"
                            required
                        />
                    </div>

                    <div>
                        <label class="ttext-[#ff69b4] flex items-center gap-1 text-sm font-medium">
                            <ClipboardList class="h-4 w-4 text-[#ff69b4]" /> Type
                        </label>
                        <select
                            v-model="form.type"
                            class="w-full rounded border px-3 py-2 focus:border-[#ffc60b] focus:ring-2 focus:ring-[#ffc60b] focus:outline-none"
                            required
                        >
                            <option value="quiz">Quiz</option>
                            <option value="exam">Exam</option>
                            <option value="essay">Essay</option>
                        </select>
                    </div>

                    <div>
                        <label class="ttext-[#ff69b4] flex items-center gap-1 text-sm font-medium">
                            <Layers class="h-4 w-4 text-[#ff69b4]" /> Module
                        </label>
                        <select
                            v-model="form.module_id"
                            class="w-full rounded border px-3 py-2 focus:border-[#ffc60b] focus:ring-2 focus:ring-[#ffc60b] focus:outline-none"
                            required
                        >
                            <option value="">-- Choose Module --</option>
                            <option v-for="m in props.modules" :key="m.id" :value="m.id">{{ m.name }}</option>
                        </select>
                    </div>
                </div>

                <!-- Module Details -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3" v-if="selectedModule">
                    <div>
                        <label class="ttext-[#ff69b4] block text-sm font-medium">Year Level</label>
                        <select disabled class="w-full rounded border bg-gray-100 px-3 py-2 text-gray-600">
                            <option>{{ selectedModule.year_level.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="ttext-[#ff69b4] block text-sm font-medium">Section</label>
                        <select disabled class="w-full rounded border bg-gray-100 px-3 py-2 text-gray-600">
                            <option>{{ selectedModule.section.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="ttext-[#ff69b4] block text-sm font-medium">Subject</label>
                        <select disabled class="w-full rounded border bg-gray-100 px-3 py-2 text-gray-600">
                            <option>{{ selectedModule.subject.name }}</option>
                        </select>
                    </div>
                </div>

                <!-- Schedule & Due Date -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="ttext-[#ff69b4] flex items-center gap-1 text-sm font-medium">
                            <CalendarClock class="h-4 w-4 text-[#ff69b4]" /> Schedule
                        </label>
                        <input
                            type="datetime-local"
                            v-model="form.scheduled_at"
                            class="w-full rounded border px-3 py-2 focus:border-[#ffc60b] focus:ring-2 focus:ring-[#ffc60b] focus:outline-none"
                            required
                        />
                    </div>
                    <div>
                        <label class="ttext-[#ff69b4] flex items-center gap-1 text-sm font-medium">
                            <CalendarClock class="h-4 w-4 text-[#ff69b4]" /> Due Date
                        </label>
                        <input
                            type="datetime-local"
                            v-model="form.due_date"
                            class="w-full rounded border px-3 py-2 focus:border-[#ffc60b] focus:ring-2 focus:ring-[#ffc60b] focus:outline-none"
                        />
                    </div>
                </div>

                <!-- Essay Fields -->
                <div v-if="form.type === 'essay'" class="space-y-4">
                    <div>
                        <label class="ttext-[#ff69b4] flex items-center gap-1 text-sm font-medium">
                            <FileText class="h-4 w-4 text-[#ff69b4]" /> Description
                        </label>
                        <textarea
                            v-model="form.description"
                            rows="4"
                            class="w-full rounded border px-3 py-2 focus:border-[#ffc60b] focus:ring-2 focus:ring-[#ffc60b] focus:outline-none"
                        />
                    </div>
                    <div>
                        <label class="ttext-[#ff69b4] flex items-center gap-1 text-sm font-medium">
                            <FileUp class="h-4 w-4 text-[#ff69b4]" /> Attach File
                        </label>
                        <input
                            type="file"
                            @change="(e) => (form.file = (e.target as HTMLInputElement)?.files?.[0] ?? null)"
                            class="text-sm text-[#ff69b4]"
                        />
                    </div>
                </div>

                <!-- Save Button -->
                <button
                    class="mt-4 inline-flex items-center justify-center gap-2 rounded bg-[#01006c] px-6 py-2 text-white transition hover:bg-[#ffc60b] hover:text-[#01006c]"
                    :disabled="form.processing"
                >
                    <Save class="h-4 w-4" />
                    Save
                </button>
            </form>
        </div>
    </AppLayout>
</template>
