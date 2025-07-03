<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const props = defineProps<{
    module: {
        id: number;
        name: string;
        year_level_id: number;
        section_id: number;
        subject_id: number;
    };
    assignments: Array<{
        id: number; // year_level_id
        name: string;
        sections: Array<{ id: number; name: string }>;
        subjects: Array<{ id: number; name: string; section_ids: number[] }>;
    }>;
}>();

const form = useForm({
    name: props.module.name,
    year_level_id: props.module.year_level_id,
    section_id: props.module.section_id,
    subject_id: props.module.subject_id,
});

const selectedYearLevel = computed(() => props.assignments.find((a) => a.id === form.year_level_id));

const availableSections = computed(() => (selectedYearLevel.value ? selectedYearLevel.value.sections : []));

const filteredSubjects = computed(() => {
    if (!selectedYearLevel.value || !form.section_id) return [];
    return selectedYearLevel.value.subjects.filter((subject) => subject.section_ids.includes(form.section_id));
});

// Reset section and subject when year level changes
watch(
    () => form.year_level_id,
    () => {
        form.section_id = 0;
        form.subject_id = 0;
    },
);

// Reset subject when section changes
watch(
    () => form.section_id,
    () => {
        form.subject_id = 0;
    },
);
</script>

<template>
    <Head title="Edit Module" />
    <AppLayout>
        <div class="mx-auto max-w-xl space-y-4 p-4">
            <h1 class="text-xl font-bold">Edit Module</h1>

            <form @submit.prevent="form.put(`/modules/${props.module.id}`)">
                <!-- Module Name -->
                <div>
                    <label class="block font-medium">Module Name</label>
                    <input v-model="form.name" class="w-full rounded border p-2" required />
                    <div v-if="form.errors.name" class="text-sm text-red-600">
                        {{ form.errors.name }}
                    </div>
                </div>

                <!-- Year Level -->
                <div>
                    <label class="block font-medium">Year Level</label>
                    <select v-model.number="form.year_level_id" class="w-full rounded border p-2" required>
                        <option value="">Select Year Level</option>
                        <option v-for="yl in props.assignments" :key="yl.id" :value="yl.id">
                            {{ yl.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.year_level_id" class="text-sm text-red-600">
                        {{ form.errors.year_level_id }}
                    </div>
                </div>

                <!-- Section -->
                <div v-if="availableSections.length > 0">
                    <label class="block font-medium">Section</label>
                    <select v-model.number="form.section_id" class="w-full rounded border p-2" required>
                        <option value="">Select Section</option>
                        <option v-for="section in availableSections" :key="section.id" :value="section.id">
                            {{ section.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.section_id" class="text-sm text-red-600">
                        {{ form.errors.section_id }}
                    </div>
                </div>

                <!-- Subject -->
                <div v-if="filteredSubjects.length > 0">
                    <label class="block font-medium">Subject</label>
                    <select v-model.number="form.subject_id" class="w-full rounded border p-2" required>
                        <option value="">Select Subject</option>
                        <option v-for="subject in filteredSubjects" :key="subject.id" :value="subject.id">
                            {{ subject.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.subject_id" class="text-sm text-red-600">
                        {{ form.errors.subject_id }}
                    </div>
                </div>

                <!-- Submit -->
                <div>
                    <button class="mt-4 w-full rounded bg-green-600 py-2 text-white" :disabled="form.processing">Update</button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
