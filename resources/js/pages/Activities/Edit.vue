<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, CalendarClock, ClipboardList, FileText, FileUp, Layers, Tag } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    activity: {
        id: number;
        title: string;
        type: string;
        module_id: number;
        scheduled_at: string | null;
        due_date: string | null;
        description: string | null;
        file_path: string | null;
    };
    modules: Array<{
        id: number;
        name: string;
        year_level: { name: string };
        section: { name: string };
        subject: { name: string };
    }>;
}>();

const form = useForm({
    _method: 'put',
    title: props.activity.title,
    type: props.activity.type,
    module_id: props.activity.module_id,
    scheduled_at: props.activity.scheduled_at?.substring(0, 16) ?? '',
    due_date: props.activity.due_date?.substring(0, 16) ?? '',
    description: props.activity.description ?? '',
    file: null as File | null,
});

const selectedModule = computed(() => props.modules.find((m) => m.id === Number(form.module_id)));
</script>

<template>
    <Head title="Edit Activity" />
    <AppLayout>
        <div class="mx-auto max-w-4xl space-y-6 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-800">Edit Activity</h1>
                <Link href="/activities" class="inline-flex items-center gap-2 text-sm text-gray-600 transition hover:text-blue-600 hover:underline">
                    <ArrowLeft class="h-4 w-4" /> Back to Activities
                </Link>
            </div>

            <form @submit.prevent="form.post(`/activities/${props.activity.id}`, { forceFormData: true })" class="space-y-6">
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
                        <label class="flex items-center gap-1 text-sm font-medium text-gray-700">
                            <CalendarClock class="h-4 w-4" /> Scheduled At
                        </label>
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
                        <textarea v-model="form.description" class="w-full rounded border px-3 py-2" rows="4" />
                    </div>

                    <div>
                        <label class="flex items-center gap-1 text-sm font-medium text-gray-700"> <FileUp class="h-4 w-4" /> Attach File </label>
                        <input type="file" @change="(e) => (form.file = (e.target as HTMLInputElement)?.files?.[0] ?? null)" />
                        <div v-if="props.activity.file_path" class="mt-2 text-sm text-gray-500">
                            Current file:
                            <a :href="`/storage/${props.activity.file_path}`" target="_blank" class="text-blue-600 underline"> View </a>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button class="mt-4 rounded bg-blue-600 px-6 py-2 text-white hover:bg-blue-700">Update</button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
