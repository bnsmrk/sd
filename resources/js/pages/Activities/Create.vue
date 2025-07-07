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
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-800">Create Activity</h1>
                <Link href="/activities" class="inline-flex items-center gap-2 text-sm text-gray-600 transition hover:text-blue-600 hover:underline">
                    <ArrowLeft class="h-4 w-4" /> Back to Activities
                </Link>
            </div>

            <form @submit.prevent="form.post('/activities', { forceFormData: true })" class="space-y-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label class="flex items-center gap-1 text-sm font-medium text-gray-700"> <Tag class="h-4 w-4" /> Title </label>
                        <input v-model="form.title" class="w-full rounded border px-3 py-2" required />
                    </div>

                    <div>
                        <label class="flex items-center gap-1 text-sm font-medium text-gray-700"> <ClipboardList class="h-4 w-4" /> Type </label>
                        <select v-model="form.type" class="w-full rounded border px-3 py-2" required>
                            <option value="quiz">Quiz</option>
                            <option value="exam">Exam</option>
                            <option value="essay">Essay</option>
                        </select>
                    </div>

                    <div>
                        <label class="flex items-center gap-1 text-sm font-medium text-gray-700"> <Layers class="h-4 w-4" /> Module </label>
                        <select v-model="form.module_id" class="w-full rounded border px-3 py-2" required>
                            <option value="">-- Choose Module --</option>
                            <option v-for="m in props.modules" :key="m.id" :value="m.id">{{ m.name }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3" v-if="selectedModule">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Year Level</label>
                        <select disabled class="w-full rounded border bg-gray-100 px-3 py-2 text-gray-600">
                            <option>{{ selectedModule.year_level.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Section</label>
                        <select disabled class="w-full rounded border bg-gray-100 px-3 py-2 text-gray-600">
                            <option>{{ selectedModule.section.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Subject</label>
                        <select disabled class="w-full rounded border bg-gray-100 px-3 py-2 text-gray-600">
                            <option>{{ selectedModule.subject.name }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="flex items-center gap-1 text-sm font-medium text-gray-700"> <CalendarClock class="h-4 w-4" /> Schedule </label>
                        <input type="datetime-local" v-model="form.scheduled_at" class="w-full rounded border px-3 py-2" required />
                    </div>
                    <div>
                        <label class="flex items-center gap-1 text-sm font-medium text-gray-700"> <CalendarClock class="h-4 w-4" /> Due Date </label>
                        <input type="datetime-local" v-model="form.due_date" class="w-full rounded border px-3 py-2" />
                    </div>
                </div>

                <div v-if="form.type === 'essay'" class="space-y-4">
                    <div>
                        <label class="flex items-center gap-1 text-sm font-medium text-gray-700"> <FileText class="h-4 w-4" /> Description </label>
                        <textarea v-model="form.description" rows="4" class="w-full rounded border px-3 py-2" />
                    </div>
                    <div>
                        <label class="flex items-center gap-1 text-sm font-medium text-gray-700"> <FileUp class="h-4 w-4" /> Attach File </label>
                        <input type="file" @change="(e) => (form.file = (e.target as HTMLInputElement)?.files?.[0] ?? null)" />
                    </div>
                </div>

                <button class="mt-4 rounded bg-blue-600 px-6 py-2 text-white hover:bg-blue-700">Save</button>
            </form>
        </div>
    </AppLayout>
</template>
