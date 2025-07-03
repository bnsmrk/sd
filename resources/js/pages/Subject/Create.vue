<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

interface YearLevel {
    id: number;
    name: string;
}
interface Section {
    id: number;
    name: string;
    year_level_id: number;
}

const props = defineProps<{
    yearLevels: YearLevel[];
    sections: Section[];
}>();

const form = useForm({
    year_level_id: '',
    shared_subjects: [''],
    major_subjects: {} as Record<number, string[]>, // section_id => [subjects]
});

// Filter sections based on selected year level
const filteredSections = computed(() =>
    props.sections.filter(s => Number(s.year_level_id) === Number(form.year_level_id))
);

// Check if selected year level is SHS (Grade 11 or 12)
const isSHS = computed(() => [5, 6].includes(Number(form.year_level_id)));

// Reset fields when year level changes
watch(
    () => form.year_level_id,
    () => {
        form.shared_subjects = [''];
        form.major_subjects = {};
        for (const section of filteredSections.value) {
            form.major_subjects[section.id] = [''];
        }
    }
);

// Add/remove functions
const addMajor = (sectionId: number) => form.major_subjects[sectionId].push('');
const removeMajor = (sectionId: number, index: number) => form.major_subjects[sectionId].splice(index, 1);
</script>

<template>
    <Head title="Create Subject" />
    <AppLayout>
        <div class="mx-auto max-w-3xl p-6">
            <h1 class="mb-6 text-2xl font-bold text-gray-900 dark:text-white">Add Subjects</h1>

            <form @submit.prevent="form.post('/subjects')">
                <!-- Year Level -->
                <div class="mb-4">
                    <label for="year_level" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select Year Level</label>
                    <select
                        id="year_level"
                        v-model="form.year_level_id"
                        class="block w-full rounded border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    >
                        <option value="">Select Year Level</option>
                        <option v-for="level in props.yearLevels" :key="level.id" :value="level.id">{{ level.name }}</option>
                    </select>
                    <div v-if="form.errors.year_level_id" class="mt-1 text-sm text-red-500">
                        {{ form.errors.year_level_id }}
                    </div>
                </div>

                <!-- Major Subjects per Section -->
                <div v-if="isSHS" class="space-y-6">
                    <div v-for="section in filteredSections" :key="section.id">
                        <label class="block mb-2 text-sm font-bold text-gray-700 dark:text-white">Major Subjects - {{ section.name }}</label>
                        <div v-for="(subject, index) in form.major_subjects[section.id]" :key="'major-' + section.id + '-' + index" class="mb-2 flex gap-2">
                            <input
                                v-model="form.major_subjects[section.id][index]"
                                type="text"
                                placeholder="e.g. CSS, HUMSS"
                                class="w-full rounded border p-2 text-sm dark:bg-gray-700 dark:text-white"
                            />
                            <button v-if="form.major_subjects[section.id].length > 1" @click.prevent="removeMajor(section.id, index)" class="text-red-500">✕</button>
                        </div>
                        <button @click.prevent="addMajor(section.id)" class="text-sm text-blue-600 hover:underline dark:text-blue-400">
                            + Add Major Subject for {{ section.name }}
                        </button>
                    </div>
                </div>

                <!-- Shared Subjects (shown once if SHS) -->
                <div v-if="isSHS" class="mt-8 mb-6">
                    <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Shared Subjects</label>
                    <div v-for="(subject, index) in form.shared_subjects" :key="'shared-' + index" class="mb-2 flex gap-2">
                        <input
                            v-model="form.shared_subjects[index]"
                            type="text"
                            placeholder="e.g. English"
                            class="w-full rounded border p-2 text-sm dark:bg-gray-700 dark:text-white"
                        />
                        <button v-if="form.shared_subjects.length > 1" @click.prevent="form.shared_subjects.splice(index, 1)" class="text-red-500">✕</button>
                    </div>
                    <button @click.prevent="form.shared_subjects.push('')" class="text-sm text-blue-600 hover:underline dark:text-blue-400">
                        + Add Shared Subject
                    </button>
                </div>

                <!-- Submit -->
                <div class="mt-6 flex justify-end gap-4">
                    <Link href="/subjects" class="text-gray-600 hover:underline dark:text-gray-300">Cancel</Link>
                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Saving...' : 'Save' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
