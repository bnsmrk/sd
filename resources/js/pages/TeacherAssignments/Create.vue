<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, BookOpen, Layers, Layout, Send, User } from 'lucide-vue-next';
import { computed } from 'vue';
const props = defineProps<{
    teachers: Array<{ id: number; name: string }>;
    yearLevels: Array<{ id: number; name: string }>;
    sections: Array<{ id: number; name: string; year_level_id: number }>;
    subjects: Array<{ id: number; name: string; year_level_id: number; section_id: number | null }>;
}>();

const form = useForm({
    user_id: '',
    year_level_id: '',
    section_id: '',
    subject_id: '',
});

const filteredSections = computed(() => props.sections.filter((section) => section.year_level_id === Number(form.year_level_id)));

const filteredSubjects = computed(() => {
    const ylId = Number(form.year_level_id);
    const secId = Number(form.section_id);

    return props.subjects.filter((subject) => subject.year_level_id === ylId && (subject.section_id === null || subject.section_id === secId));
});
</script>

<template>
    <Head title="Assign Teacher" />
    <AppLayout>
        <div class="mx-auto mt-10 max-w-3xl rounded-lg bg-white p-8 shadow dark:bg-gray-800">
            <div class="mb-6">
                <Link
                    href="/teacher-assignments"
                    class="inline-flex items-center gap-2 rounded border border-[#01006c] bg-white px-4 py-2 text-sm font-semibold text-[#01006c] shadow hover:bg-[#ffc60b]"
                >
                    <ArrowLeft class="h-4 w-4" /> Back
                </Link>
            </div>

            <h1 class="mb-6 text-2xl font-bold text-gray-900 dark:text-white">Assign Teacher</h1>

            <form @submit.prevent="form.post('/teacher-assignments')" class="space-y-6">
                <div class="grid grid-cols-4 gap-4">
                    <div>
                        <label class="mb-2 flex items-center gap-1 text-sm font-medium text-gray-900 dark:text-white">
                            <User class="h-4 w-4" /> Teacher
                        </label>
                        <select v-model="form.user_id" required class="input-select">
                            <option value="">Select a Teacher</option>
                            <option v-for="t in props.teachers" :key="t.id" :value="t.id">{{ t.name }}</option>
                        </select>
                        <p v-if="form.errors.user_id" class="mt-1 text-sm text-red-600">{{ form.errors.user_id }}</p>
                    </div>

                    <div>
                        <label class="mb-2 flex items-center gap-1 text-sm font-medium text-gray-900 dark:text-white">
                            <Layers class="h-4 w-4" /> Year Level
                        </label>
                        <select v-model="form.year_level_id" required class="input-select">
                            <option value="">Select a Year Level</option>
                            <option v-for="y in props.yearLevels" :key="y.id" :value="y.id">{{ y.name }}</option>
                        </select>
                        <p v-if="form.errors.year_level_id" class="mt-1 text-sm text-red-600">{{ form.errors.year_level_id }}</p>
                    </div>

                    <div>
                        <label class="mb-2 flex items-center gap-1 text-sm font-medium text-gray-900 dark:text-white">
                            <Layout class="h-4 w-4" /> Section
                        </label>
                        <select v-model="form.section_id" required class="input-select">
                            <option value="">Select a Section</option>
                            <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                        <p v-if="form.errors.section_id" class="mt-1 text-sm text-red-600">{{ form.errors.section_id }}</p>
                    </div>

                    <div>
                        <label class="mb-2 flex items-center gap-1 text-sm font-medium text-gray-900 dark:text-white">
                            <BookOpen class="h-4 w-4" /> Subject
                        </label>
                        <select v-model="form.subject_id" required class="input-select">
                            <option value="">Select a Subject</option>
                            <option v-for="s in filteredSubjects" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                        <p v-if="form.errors.subject_id" class="mt-1 text-sm text-red-600">{{ form.errors.subject_id }}</p>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 rounded-lg bg-gray-800 px-8 py-2.5 text-sm font-medium text-white hover:bg-gray-900 focus:ring-4 focus:ring-gray-300 focus:outline-none dark:bg-gray-800 dark:hover:bg-gray-700"
                    >
                        <Send class="h-4 w-4" /> Assign Teacher
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
