<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    teachers: { id: number; name: string }[];
    subjects: { id: number; name: string }[];
    sections: { id: number; name: string }[];
}>();

const form = useForm({
    teacher_id: '',
    subject_id: '',
    section_id: '',
});

function submit() {
    form.post(route('head-teacher-assignments.store'), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Assign Teacher" />
    <AppLayout>
        <div class="min-h-screen bg-pink-50 px-6 py-8">
            <h1 class="mb-6 text-3xl font-bold text-[#01006c]">Assign Teacher</h1>

            <form @submit.prevent="submit" class="space-y-4 rounded-xl bg-white p-6 shadow">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Teacher</label>
                    <select v-model="form.teacher_id" class="mt-1 w-full rounded border px-4 py-2">
                        <option disabled value="">Select a teacher</option>
                        <option v-for="t in props.teachers" :key="t.id" :value="t.id">{{ t.name }}</option>
                    </select>
                    <p v-if="form.errors.teacher_id" class="mt-1 text-sm text-red-600">{{ form.errors.teacher_id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Section</label>
                    <select v-model="form.section_id" class="mt-1 w-full rounded border px-4 py-2">
                        <option disabled value="">Select a section</option>
                        <option v-for="s in props.sections" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                    <p v-if="form.errors.section_id" class="mt-1 text-sm text-red-600">{{ form.errors.section_id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Subject</label>
                    <select v-model="form.subject_id" class="mt-1 w-full rounded border px-4 py-2">
                        <option disabled value="">Select a subject</option>
                        <option v-for="s in props.subjects" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                    <p v-if="form.errors.subject_id" class="mt-1 text-sm text-red-600">{{ form.errors.subject_id }}</p>
                </div>

                <button type="submit" class="rounded bg-indigo-600 px-6 py-2 text-white hover:bg-indigo-700" :disabled="form.processing">
                    Assign
                </button>
            </form>
        </div>
    </AppLayout>
</template>
