<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    assignment: {
        id: number;
        user_id: number;
        year_level_id: number;
        subject_id: number;
    };
    teachers: Array<{ id: number; name: string }>;
    yearLevels: Array<{ id: number; name: string }>;
    subjects: Array<{ id: number; name: string; year_level_id: number }>;
}>();

const form = useForm({
    user_id: props.assignment.user_id,
    year_level_id: props.assignment.year_level_id,
    subject_id: props.assignment.subject_id,
});

const filteredSubjects = computed(() => props.subjects.filter((subject) => subject.year_level_id === Number(form.year_level_id)));

function submit() {
    form.put(`/teacher-assignments/${props.assignment.id}`);
}
</script>

<template>
    <Head title="Edit Assignment" />
    <AppLayout>
        <div class="mx-auto max-w-xl space-y-6 p-6">
            <h1 class="text-xl font-bold">Edit Teacher Assignment</h1>

            <form @submit.prevent="submit" class="space-y-4">
                <!-- Teacher -->
                <div>
                    <label class="block font-medium">Teacher</label>
                    <select v-model="form.user_id" class="w-full rounded border px-3 py-2">
                        <option value="">Select Teacher</option>
                        <option v-for="t in props.teachers" :key="t.id" :value="t.id">
                            {{ t.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.user_id" class="text-sm text-red-600">
                        {{ form.errors.user_id }}
                    </div>
                </div>

                <!-- Year Level -->
                <div>
                    <label class="block font-medium">Year Level</label>
                    <select v-model="form.year_level_id" class="w-full rounded border px-3 py-2">
                        <option value="">Select Year Level</option>
                        <option v-for="y in props.yearLevels" :key="y.id" :value="y.id">
                            {{ y.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.year_level_id" class="text-sm text-red-600">
                        {{ form.errors.year_level_id }}
                    </div>
                </div>

                <!-- Subject (filtered by year level) -->
                <div>
                    <label class="block font-medium">Subject</label>
                    <select v-model="form.subject_id" class="w-full rounded border px-3 py-2">
                        <option value="">Select Subject</option>
                        <option v-for="s in filteredSubjects" :key="s.id" :value="s.id">
                            {{ s.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.subject_id" class="text-sm text-red-600">
                        {{ form.errors.subject_id }}
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" class="w-full rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700" :disabled="form.processing">
                    Update Assignment
                </button>
            </form>
        </div>
    </AppLayout>
</template>
