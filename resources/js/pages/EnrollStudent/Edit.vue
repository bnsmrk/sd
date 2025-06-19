<!-- resources/js/Pages/EnrollStudent/Edit.vue -->
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    enrollment: {
        id: number;
        user: { id: number; name: string };
        year_level_id: number;
    };
    yearLevels: { id: number; name: string }[];
    subjects: { id: number; name: string; section_id: number; year_level_id: number }[];
}>();

const form = useForm({
    user_id: props.enrollment.user.id,
    year_level_id: props.enrollment.year_level_id,
});

const filteredSubjects = computed(() => props.subjects.filter((s) => s.year_level_id === form.year_level_id));

const submitForm = () => {
    form.put(`/enroll/${props.enrollment.id}`);
};
</script>

<template>
    <Head title="Edit Enrollment" />
    <AppLayout :breadcrumbs="[{ title: 'Enrollments', href: '/enroll' }]">
        <div class="mx-auto max-w-xl space-y-6 p-6">
            <h2 class="text-xl font-bold">Edit Student Enrollment</h2>

            <div>
                <label class="mb-1 block font-medium">Year Level</label>
                <select v-model="form.year_level_id" class="w-full rounded border p-2">
                    <option v-for="yl in yearLevels" :key="yl.id" :value="yl.id">{{ yl.name }}</option>
                </select>
                <p class="text-sm text-red-500" v-if="form.errors.year_level_id">{{ form.errors.year_level_id }}</p>
            </div>

            <div v-if="filteredSubjects.length">
                <p class="text-sm text-gray-600">Student will be enrolled in the following subjects:</p>
                <ul class="mt-2 ml-5 list-disc text-sm text-gray-700">
                    <li v-for="subject in filteredSubjects" :key="subject.id">{{ subject.name }} (Section ID: {{ subject.section_id }})</li>
                </ul>
            </div>

            <button @click="submitForm" class="w-full rounded bg-blue-600 py-2 text-white">Update</button>
        </div>
    </AppLayout>
</template>
