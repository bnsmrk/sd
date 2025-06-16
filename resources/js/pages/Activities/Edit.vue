<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    activity: any;
    yearLevels: Array<{ id: number; name: string }>;
    sections: Array<{ id: number; name: string; year_level_id: number }>;
    subjects: Array<{ id: number; name: string; year_level_id: number; section_id: number }>;
}>();
const scheduledFormatted = computed(() => {
    if (!props.activity?.scheduled_at) return '';
    return props.activity.scheduled_at.replace(' ', 'T').slice(0, 16);
});
const form = useForm({
    title: props.activity.title,
    type: props.activity.type,
    scheduled_at: scheduledFormatted.value || '',
    year_level_id: props.activity.year_level_id,
    section_id: props.activity.section_id,
    subject_id: props.activity.subject_id,
});

const filteredSections = computed(() => props.sections.filter((s) => s.year_level_id === Number(form.year_level_id)));

const filteredSubjects = computed(() =>
    props.subjects.filter((s) => s.year_level_id === Number(form.year_level_id) && s.section_id === Number(form.section_id)),
);
</script>

<template>
    <Head title="Edit Activity" />
    <AppLayout>
        <div class="mx-auto max-w-xl space-y-6 p-4">
            <h1 class="text-xl font-bold">Edit Activity</h1>
            <form @submit.prevent="form.put(`/activities/${props.activity.id}`)">
                <div>
                    <label>Title</label>
                    <input v-model="form.title" class="w-full rounded border px-3 py-2" />
                </div>

                <div>
                    <label>Type</label>
                    <select v-model="form.type" class="w-full rounded border px-3 py-2">
                        <option value="quiz">Quiz</option>
                        <option value="exam">Exam</option>
                    </select>
                </div>

                <div>
                    <label>Date</label>
                    <input type="datetime-local" v-model="form.scheduled_at" class="w-full rounded border px-3 py-2" />
                </div>

                <div>
                    <label>Year Level</label>
                    <select v-model="form.year_level_id" class="w-full rounded border px-3 py-2">
                        <option value="">Select Year</option>
                        <option v-for="y in props.yearLevels" :value="y.id" :key="y.id">{{ y.name }}</option>
                    </select>
                </div>

                <div>
                    <label>Section</label>
                    <select v-model="form.section_id" class="w-full rounded border px-3 py-2">
                        <option value="">Select Section</option>
                        <option v-for="s in filteredSections" :value="s.id" :key="s.id">{{ s.name }}</option>
                    </select>
                </div>

                <div>
                    <label>Subject</label>
                    <select v-model="form.subject_id" class="w-full rounded border px-3 py-2">
                        <option value="">Select Subject</option>
                        <option v-for="s in filteredSubjects" :value="s.id" :key="s.id">{{ s.name }}</option>
                    </select>
                </div>

                <button class="mt-4 rounded bg-green-600 px-4 py-2 text-white">Update</button>
            </form>
        </div>
    </AppLayout>
</template>
