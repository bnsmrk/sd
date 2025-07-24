<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    teacher: { id: number; name: string };
    subAssignments: {
        id: number;
        subject_id: number;
        section_id: number;
        subject_name: string;
        section_name: string;
    }[];
    subjects: { id: number; name: string }[];
    sections: { id: number; name: string }[];
}>();

const form = useForm({
    assignments: props.subAssignments.map(({ id, subject_id, section_id }) => ({
        id,
        subject_id,
        section_id,
    })),
});

function removeAssignment(index: number) {
    form.assignments.splice(index, 1);
}

function submit() {
    form.put(route('head-teacher-assignments.update', props.teacher.id));
}
</script>

<template>
    <Head title="Edit Assignments" />
    <AppLayout :breadcrumbs="[{ title: 'Back', href: route('head-teacher-assignments.index') }]">
        <div class="min-h-screen space-y-4 bg-pink-50 p-6">
            <h1 class="text-2xl font-bold text-indigo-800">Edit Assignments for {{ teacher.name }}</h1>

            <form @submit.prevent="submit" class="space-y-4">
                <div v-for="(a, index) in form.assignments" :key="index" class="flex flex-col gap-1 md:flex-row md:items-center md:gap-4">
                    <div class="w-full md:w-1/3">
                        <select v-model="a.section_id" class="w-full rounded border p-2">
                            <option :value="null" disabled>Select Section</option>
                            <option v-for="s in sections" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                        <p v-if="form.errors[`assignments.${index}.section_id`]" class="text-sm text-red-600">
                            {{ form.errors[`assignments.${index}.section_id`] }}
                        </p>
                    </div>
                    <div class="w-full md:w-1/3">
                        <select v-model="a.subject_id" class="w-full rounded border p-2">
                            <option :value="null" disabled>Select Subject</option>
                            <option v-for="s in subjects" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                        <p v-if="form.errors[`assignments.${index}.subject_id`]" class="text-sm text-red-600">
                            {{ form.errors[`assignments.${index}.subject_id`] }}
                        </p>
                    </div>

                    <button type="button" @click="removeAssignment(index)" class="text-red-600">Remove</button>
                </div>

                <p v-if="form.errors.assignments" class="text-sm text-red-600">
                    {{ form.errors.assignments }}
                </p>

                <div>
                    <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700" :disabled="form.processing">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
