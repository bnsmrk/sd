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
    form.put(route('head-teacher-assignments.update', props.teacher.id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Edit Assignments" />
    <AppLayout :breadcrumbs="[{ title: 'Back', href: route('head-teacher-assignments.index') }]">
        <div class="min-h-screen bg-pink-50 px-6 py-8">
            <h1 class="mb-6 text-3xl font-bold text-[#01006c]">Edit Assignments for {{ teacher.name }}</h1>

            <form @submit.prevent="submit" class="space-y-4 rounded-xl bg-white p-6 shadow">
                <div
                    v-for="(a, index) in form.assignments"
                    :key="index"
                    class="rounded border p-4 shadow-sm md:flex md:items-center md:gap-4"
                >
                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                        <select v-model="a.section_id" class="w-full rounded border px-4 py-2">
                            <option :value="null" disabled>Select Section</option>
                            <option v-for="s in sections" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                        <p
                            v-if="(form.errors as Record<string, string>)[`assignments.${index}.section_id`]"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ (form.errors as Record<string, string>)[`assignments.${index}.section_id`] }}
                        </p>
                    </div>

                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                        <select v-model="a.subject_id" class="w-full rounded border px-4 py-2">
                            <option :value="null" disabled>Select Subject</option>
                            <option v-for="s in subjects" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                        <p
                            v-if="(form.errors as Record<string, string>)[`assignments.${index}.subject_id`]"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ (form.errors as Record<string, string>)[`assignments.${index}.subject_id`] }}
                        </p>
                    </div>

                    <div class="mt-3 md:mt-0 md:w-auto">
                        <button
                            type="button"
                            @click="removeAssignment(index)"
                            class="text-red-600 hover:underline text-sm"
                        >
                            Remove
                        </button>
                    </div>
                </div>

                <p v-if="form.errors.assignments" class="text-sm text-red-600">
                    {{ form.errors.assignments }}
                </p>

                <div class="pt-4">
                    <button
                        type="submit"
                        class="rounded bg-indigo-600 px-6 py-2 text-white hover:bg-indigo-700"
                        :disabled="form.processing"
                    >
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
