<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    module: any;
    yearLevels: Array<{ id: number; name: string }>;
    sections: Array<{ id: number; name: string; year_level_id: number }>;
    subjects: Array<{ id: number; name: string; year_level_id: number; section_id: number }>;
}>();

const form = useForm({
    name: props.module.name,
    year_level_id: props.module.year_level_id,
    section_id: props.module.section_id,
    subject_id: props.module.subject_id,
});

const filteredSections = computed(() => props.sections.filter((s) => s.year_level_id === Number(form.year_level_id)));

const filteredSubjects = computed(() =>
    props.subjects.filter((s) => s.year_level_id === Number(form.year_level_id) && s.section_id === Number(form.section_id)),
);
</script>

<template>
    <Head title="Edit Module" />
    <AppLayout>
        <div class="mx-auto max-w-xl space-y-4 p-4">
            <h1 class="text-xl font-bold">Edit Module</h1>
            <form @submit.prevent="form.put(`/modules/${props.module.id}`)">
                <div>
                    <label>Module Name</label>
                    <input v-model="form.name" class="w-full rounded border px-3 py-2" />
                </div>

                <div>
                    <label>Year Level</label>
                    <select v-model="form.year_level_id" class="w-full rounded border px-3 py-2">
                        <option value="">Select Year</option>
                        <option v-for="y in props.yearLevels" :value="y.id" :key="y.id">{{ y.name }}</option>
                    </select>
                </div>

                <div>
                    <label>Section</label>
                    <select v-model="form.section_id" class="w-full rounded border px-3 py-2">
                        <option value="">Select Section</option>
                        <option v-for="s in filteredSections" :value="s.id" :key="s.id">{{ s.name }}</option>
                    </select>
                </div>

                <div>
                    <label>Subject</label>
                    <select v-model="form.subject_id" class="w-full rounded border px-3 py-2">
                        <option value="">Select Subject</option>
                        <option v-for="s in filteredSubjects" :value="s.id" :key="s.id">{{ s.name }}</option>
                    </select>
                </div>

                <button class="mt-4 rounded bg-green-600 px-4 py-2 text-white">Update</button>
            </form>
        </div>
    </AppLayout>
</template>
