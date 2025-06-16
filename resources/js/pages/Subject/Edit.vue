<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    subject: any;
    yearLevels: Array<{ id: number; name: string }>;
    sections: Array<{ id: number; name: string; year_level_id: number }>;
}>();

const form = useForm({
    name: props.subject.name,
    year_level_id: props.subject.year_level_id,
    section_id: props.subject.section_id,
});

// Filtered sections based on selected year_level_id
const filteredSections = computed(() => {
    return props.sections.filter((s) => s.year_level_id === form.year_level_id);
});
</script>

<template>
    <Head title="Edit Subject" />
    <AppLayout>
        <div class="p-4">
            <h2 class="mb-4 text-xl font-bold">Edit Subject</h2>

            <form @submit.prevent="form.put(`/subjects/${props.subject.id}`)">
                <!-- Subject Name -->
                <div class="mb-4">
                    <label class="mb-1 block">Subject Name</label>
                    <input v-model="form.name" type="text" class="w-full rounded border px-4 py-2" />
                </div>

                <!-- Year Level -->
                <div class="mb-4">
                    <label class="mb-1 block">Year Level</label>
                    <select v-model="form.year_level_id" class="w-full rounded border px-4 py-2">
                        <option value="">Select Year Level</option>
                        <option v-for="y in props.yearLevels" :key="y.id" :value="y.id">{{ y.name }}</option>
                    </select>
                </div>

                <!-- Section -->
                <div class="mb-4">
                    <label class="mb-1 block">Section</label>
                    <select v-model="form.section_id" class="w-full rounded border px-4 py-2">
                        <option value="">Select Section</option>
                        <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                </div>

                <button type="submit" class="rounded bg-green-600 px-4 py-2 text-white">Update</button>
            </form>
        </div>
    </AppLayout>
</template>
