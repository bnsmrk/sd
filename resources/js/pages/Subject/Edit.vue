<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    subject: {
        id: number;
        name: string;
        year_level_id: number;
        section_id?: number | null;
        year_level?: { id: number; name: string };
        section?: { id: number; name: string } | null;
    };
    yearLevels: Array<{ id: number; name: string }>;
}>();

const form = useForm({
    name: props.subject.name,
    year_level_id: props.subject.year_level_id,
});

const isSHS = computed(() => {
    const selected = props.yearLevels.find((yl) => yl.id === form.year_level_id);
    return selected?.name === 'Grade 11' || selected?.name === 'Grade 12';
});
</script>

<template>
    <Head title="Edit Subject" />
    <AppLayout>
        <div class="mx-auto max-w-xl space-y-6 p-4">
            <h1 class="text-xl font-bold">Edit Subject</h1>

            <form @submit.prevent="form.put(`/subjects/${props.subject.id}`)">
                <div>
                    <label class="block font-medium">Subject Name</label>
                    <input v-model="form.name" class="w-full rounded border p-2" required />
                    <div v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</div>
                </div>

                <div>
                    <label class="block font-medium">Year Level</label>
                    <select v-model="form.year_level_id" class="w-full rounded border p-2" required>
                        <option value="">Select Year Level</option>
                        <option v-for="yl in props.yearLevels" :key="yl.id" :value="yl.id">{{ yl.name }}</option>
                    </select>
                    <div v-if="form.errors.year_level_id" class="text-sm text-red-600">{{ form.errors.year_level_id }}</div>
                </div>

                <div v-if="isSHS && props.subject.section">
                    <label class="block font-medium">Section</label>
                    <div class="rounded border bg-gray-100 px-3 py-2 text-sm text-gray-700">
                        {{ props.subject.section.name }}
                    </div>
                </div>

                <div>
                    <button class="mt-4 w-full rounded bg-blue-600 py-2 text-white" :disabled="form.processing">Update Subject</button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
