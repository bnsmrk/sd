<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

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
        <div class="mx-auto max-w-xl space-y-6 p-6">
            <h1 class="text-xl font-bold">Create Activity</h1>

            <form @submit.prevent="form.post('/activities', { forceFormData: true })">
                <div>
                    <label>Activity Title</label>
                    <input v-model="form.title" class="w-full rounded border px-3 py-2" required />
                </div>

                <div class="mt-4">
                    <label>Activity Type</label>
                    <select v-model="form.type" class="w-full rounded border px-3 py-2" required>
                        <option value="quiz">Quiz</option>
                        <option value="exam">Exam</option>
                        <option value="essay">Essay</option>
                    </select>
                </div>

                <div class="mt-4">
                    <label>Select Module</label>
                    <select v-model="form.module_id" class="w-full rounded border px-3 py-2" required>
                        <option value="">-- Choose Module --</option>
                        <option v-for="m in props.modules" :key="m.id" :value="m.id">{{ m.name }}</option>
                    </select>
                </div>

                <div v-if="selectedModule" class="mt-2 space-y-1 text-sm text-gray-700">
                    <div><strong>Year Level:</strong> {{ selectedModule.year_level.name }}</div>
                    <div><strong>Section:</strong> {{ selectedModule.section.name }}</div>
                    <div><strong>Subject:</strong> {{ selectedModule.subject.name }}</div>
                </div>

                <div class="mt-4">
                    <label>Date & Time</label>
                    <input type="datetime-local" v-model="form.scheduled_at" class="w-full rounded border px-3 py-2" required />
                </div>

                <div class="mt-4">
                <label>Due Date</label>
                <input type="datetime-local" v-model="form.due_date" class="w-full rounded border px-3 py-2" />
                </div>

                <div v-if="form.type === 'essay'" class="mt-4">
                    <label>Description</label>
                    <textarea v-model="form.description" class="w-full rounded border px-3 py-2" rows="4"></textarea>
                </div>

                <div v-if="form.type === 'essay'" class="mt-4">
                    <label>Attach File (optional)</label>
                    <input type="file" @change="(e) => (form.file = (e.target as HTMLInputElement)?.files?.[0] ?? null)" />
                </div>

                <button class="mt-4 rounded bg-blue-600 px-4 py-2 text-white">Save</button>
            </form>
        </div>
    </AppLayout>
</template>
