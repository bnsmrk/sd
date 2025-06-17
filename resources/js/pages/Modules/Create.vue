<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    yearLevels: Array<{ id: number; name: string }>;
    sections: Array<{ id: number; name: string; year_level_id: number }>;
    subjects: Array<{ id: number; name: string; year_level_id: number; section_id: number }>;
}>();

const form = useForm({
    name: '',
    year_level_id: '',
    section_id: '',
    subject_id: '',
});

const filteredSections = computed(() => props.sections.filter((section) => section.year_level_id === Number(form.year_level_id)));

const filteredSubjects = computed(() =>
    props.subjects.filter((subject) => subject.year_level_id === Number(form.year_level_id) && subject.section_id === Number(form.section_id)),
);
</script>

<template>
    <Head title="Create Module" />
    <AppLayout>
        <div class="mx-auto max-w-xl space-y-4 p-4">
            <h1 class="text-xl font-bold">Create Module</h1>

            <form @submit.prevent="form.post('/modules')">
                <!-- Module Name -->
                <div>
                    <label class="block font-medium">Module Name</label>
                    <input v-model="form.name" class="w-full rounded border px-3 py-2" placeholder="e.g., Introduction to Algebra" required />
                    <div v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</div>
                </div>

                <!-- Year Level -->
                <div>
                    <label class="block font-medium">Year Level</label>
                    <select v-model="form.year_level_id" class="w-full rounded border px-3 py-2" required>
                        <option value="">Select Year Level</option>
                        <option v-for="y in props.yearLevels" :key="y.id" :value="y.id">{{ y.name }}</option>
                    </select>
                    <div v-if="form.errors.year_level_id" class="text-sm text-red-600">{{ form.errors.year_level_id }}</div>
                </div>

                <!-- Section -->
                <div>
                    <label class="block font-medium">Section</label>
                    <select v-model="form.section_id" class="w-full rounded border px-3 py-2" required>
                        <option value="">Select Section</option>
                        <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                    <div v-if="form.errors.section_id" class="text-sm text-red-600">{{ form.errors.section_id }}</div>
                </div>

                <!-- Subject -->
                <div>
                    <label class="block font-medium">Subject</label>
                    <select v-model="form.subject_id" class="w-full rounded border px-3 py-2" required>
                        <option value="">Select Subject</option>
                        <option v-for="s in filteredSubjects" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                    <div v-if="form.errors.subject_id" class="text-sm text-red-600">{{ form.errors.subject_id }}</div>
                </div>

                <!-- Submit -->
                <div>
                    <button type="submit" class="mt-4 w-full rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700" :disabled="form.processing">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
