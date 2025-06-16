<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    enrollment: {
        id: number;
        user: { id: number; name: string };
        year_level_id: number;
        section_id: number;
        subject_id: number;
    };
    students: { id: number; name: string }[];
    yearLevels: { id: number; name: string }[];
    sections: { id: number; name: string; year_level_id: number }[];
    subjects: { id: number; name: string; section_id: number; year_level_id: number }[];
}>();

const form = useForm({
    user_id: props.enrollment.user.id,
    year_level_id: props.enrollment.year_level_id,
    section_id: props.enrollment.section_id,
    subject_id: props.enrollment.subject_id,
});

const filteredSections = computed(() => props.sections.filter((s) => s.year_level_id === form.year_level_id));

const filteredSubjects = computed(() => props.subjects.filter((s) => s.year_level_id === form.year_level_id && s.section_id === form.section_id));

function submitForm() {
    form.put(`/enroll/${props.enrollment.id}`);
}
</script>

<template>
    <Head title="Edit Enrollment" />
    <AppLayout :breadcrumbs="[{ title: 'Enroll', href: '/enroll' }]">
        <div class="mx-auto max-w-xl space-y-4 p-6">
            <h2 class="text-xl font-bold">Edit Student Enrollment</h2>

            <div>
                <label>Year Level</label>
                <select v-model="form.year_level_id" class="w-full rounded border p-2">
                    <option v-for="yl in yearLevels" :key="yl.id" :value="yl.id">{{ yl.name }}</option>
                </select>
                <div v-if="form.errors.year_level_id" class="text-sm text-red-600">{{ form.errors.year_level_id }}</div>
            </div>

            <div>
                <label>Section</label>
                <select v-model="form.section_id" class="w-full rounded border p-2">
                    <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>
                <div v-if="form.errors.section_id" class="text-sm text-red-600">{{ form.errors.section_id }}</div>
            </div>

            <div>
                <label>Subject</label>
                <select v-model="form.subject_id" class="w-full rounded border p-2">
                    <option v-for="s in filteredSubjects" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>
                <div v-if="form.errors.subject_id" class="text-sm text-red-600">{{ form.errors.subject_id }}</div>
            </div>

            <button @click="submitForm" class="w-full rounded bg-blue-600 p-2 text-white">Update</button>
        </div>
    </AppLayout>
</template>
