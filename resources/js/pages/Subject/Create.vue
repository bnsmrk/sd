<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

interface YearLevel {
    id: number;
    name: string;
}

const { yearLevels } = defineProps<{ yearLevels: YearLevel[] }>();

const form = useForm({
    year_level_id: '',
    shared_subjects: [''],
    major_subjects: [''],
});

// Compute if selected year level is SHS (Grade 11 or 12)
const isSHS = computed(() => {
    const id = Number(form.year_level_id);
    return [5, 6].includes(id); // Update based on your actual Grade 11 and 12 IDs
});

// Reset subject fields when year level changes
watch(
    () => form.year_level_id,
    () => {
        form.shared_subjects = [''];
        form.major_subjects = [''];
    },
);

// Utility functions to add/remove fields
const addTo = (arr: string[]) => arr.push('');
const removeFrom = (arr: string[], index: number) => arr.splice(index, 1);
</script>

<template>
    <Head title="Create Subject" />
    <AppLayout>
        <div class="mx-auto max-w-2xl p-4">
            <h1 class="mb-6 text-2xl font-bold text-gray-900 dark:text-white">Add Subjects</h1>

            <form @submit.prevent="form.post('/subjects')">
                <!-- Year Level -->
                <div class="mb-4">
                    <label for="year_level" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> Select Year Level </label>
                    <select
                        id="year_level"
                        v-model="form.year_level_id"
                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                        :class="{ 'border-red-500': form.errors.year_level_id }"
                    >
                        <option value="">Select Year Level</option>
                        <option v-for="level in yearLevels" :key="level.id" :value="level.id">
                            {{ level.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.year_level_id" class="mt-1 text-sm text-red-500">
                        {{ form.errors.year_level_id }}
                    </div>
                </div>

                <!-- Shared Subjects -->
                <div class="mb-6">
                    <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Shared Subjects</label>
                    <div v-for="(subject, index) in form.shared_subjects" :key="'shared-' + index" class="mb-2 flex gap-2">
                        <input
                            v-model="form.shared_subjects[index]"
                            type="text"
                            placeholder="e.g. English"
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                        />
                        <button v-if="form.shared_subjects.length > 1" @click.prevent="removeFrom(form.shared_subjects, index)" class="text-red-500">
                            ✕
                        </button>
                    </div>
                    <button @click.prevent="addTo(form.shared_subjects)" class="text-sm text-blue-600 hover:underline dark:text-blue-400">
                        + Add Shared Subject
                    </button>
                </div>

                <!-- Major Subjects (only for SHS) -->
                <div v-if="isSHS" class="mb-6">
                    <label class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> Major/Strand-Specific Subjects </label>
                    <div v-for="(subject, index) in form.major_subjects" :key="'major-' + index" class="mb-2 flex gap-2">
                        <input
                            v-model="form.major_subjects[index]"
                            type="text"
                            placeholder="e.g. Computer Systems Servicing"
                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                        />
                        <button v-if="form.major_subjects.length > 1" @click.prevent="removeFrom(form.major_subjects, index)" class="text-red-500">
                            ✕
                        </button>
                    </div>
                    <button @click.prevent="addTo(form.major_subjects)" class="text-sm text-blue-600 hover:underline dark:text-blue-400">
                        + Add Major Subject
                    </button>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end gap-4">
                    <Link href="/subjects" class="text-gray-600 hover:underline dark:text-gray-300">Cancel</Link>
                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Saving...' : 'Save' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
