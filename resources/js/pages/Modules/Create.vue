<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    assignments: Array<{
        id: number;
        name: string;
        sections: Array<{ id: number; name: string }>;
        subjects: Array<{ id: number; name: string }>;
    }>;
}>();

const form = useForm({
    name: '',
    year_level_id: '',
    subject_id: '',
});

const selectedYearLevel = computed(() => props.assignments.find((a) => a.id === Number(form.year_level_id)));

const availableSubjects = computed(() => (selectedYearLevel.value ? selectedYearLevel.value.subjects : []));

const availableSections = computed(() => (selectedYearLevel.value ? selectedYearLevel.value.sections : []));
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
                    <input v-model="form.name" class="w-full rounded border p-2" required />
                    <div v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</div>
                </div>

                <!-- Year Level -->
                <div>
                    <label class="block font-medium">Year Level</label>
                    <select v-model="form.year_level_id" class="w-full rounded border p-2" required>
                        <option value="">Select Year Level</option>
                        <option v-for="yl in props.assignments" :key="yl.id" :value="yl.id">
                            {{ yl.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.year_level_id" class="text-sm text-red-600">{{ form.errors.year_level_id }}</div>
                </div>

                <!-- Sections (display only) -->
                <div v-if="availableSections.length > 0">
                    <label class="block font-medium">Assigned Sections</label>
                    <ul class="list-inside list-disc text-sm text-gray-700">
                        <li v-for="section in availableSections" :key="section.id">{{ section.name }}</li>
                    </ul>
                </div>

                <!-- Subject -->
                <div>
                    <label class="block font-medium">Subject</label>
                    <select v-model="form.subject_id" class="w-full rounded border p-2" required>
                        <option value="">Select Subject</option>
                        <option v-for="subject in availableSubjects" :key="subject.id" :value="subject.id">
                            {{ subject.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.subject_id" class="text-sm text-red-600">{{ form.errors.subject_id }}</div>
                </div>

                <!-- Submit -->
                <div>
                    <button class="mt-4 w-full rounded bg-blue-600 py-2 text-white" :disabled="form.processing">Save</button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
