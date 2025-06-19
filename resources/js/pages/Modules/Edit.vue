<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    module: {
        id: number;
        name: string;
        year_level_id: number;
        subject_id: number;
    };
    yearLevels: Array<{ id: number; name: string }>;
    subjects: Array<{ id: number; name: string; year_level_id: number }>;
}>();

const form = useForm({
    name: props.module.name,
    year_level_id: props.module.year_level_id,
    subject_id: props.module.subject_id,
});

const filteredSubjects = computed(() => props.subjects.filter((s) => s.year_level_id === Number(form.year_level_id)));
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
                    <input v-model="form.name" class="w-full rounded border px-3 py-2" placeholder="e.g., Algebra Basics" required />
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
                    <button class="mt-4 w-full rounded bg-green-600 px-4 py-2 text-white hover:bg-green-700" :disabled="form.processing">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
