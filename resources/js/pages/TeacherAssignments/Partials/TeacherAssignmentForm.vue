<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    assignment: any | null;
    teachers: any[];
    yearLevels: any[];
    sections: any[];
    subjects: any[];
    isEdit: boolean;
}>();

const emit = defineEmits(['close']);

const form = useForm({
    user_id: props.assignment?.user_id ?? '',
    year_level_id: props.assignment?.year_level_id ?? '',
    section_id: props.assignment?.section_id ?? '',
    subject_id: props.assignment?.subject_id ?? '',
});

const filteredSections = computed(() =>
    props.sections.filter((s) => s.year_level_id == form.year_level_id)
);

const filteredSubjects = computed(() =>
    props.subjects.filter((s) =>
        s.year_level_id == form.year_level_id &&
        (s.section_id === null || s.section_id == form.section_id)
    )
);

function submit() {
    const url = props.isEdit ? `/teacher-assignments/${props.assignment.id}` : '/teacher-assignments';
    const method = props.isEdit ? form.put : form.post;

    method(url, {
        onSuccess: () => emit('close'),
    });
}
</script>

<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="w-full max-w-xl rounded bg-white p-6 shadow-lg">
            <h2 class="text-lg font-semibold mb-4">{{ isEdit ? 'Edit Assignment' : 'Assign Teacher' }}</h2>
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label>Teacher</label>
                    <select v-model="form.user_id" class="w-full border rounded p-2">
                        <option v-for="t in teachers" :key="t.id" :value="t.id">{{ t.name }}</option>
                    </select>
                </div>
                <div>
                    <label>Year Level</label>
                    <select v-model="form.year_level_id" class="w-full border rounded p-2">
                        <option v-for="y in yearLevels" :key="y.id" :value="y.id">{{ y.name }}</option>
                    </select>
                </div>
                <div>
                    <label>Section</label>
                    <select v-model="form.section_id" class="w-full border rounded p-2">
                        <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                </div>
                <div>
                    <label>Subject</label>
                    <select v-model="form.subject_id" class="w-full border rounded p-2">
                        <option v-for="s in filteredSubjects" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="$emit('close')" class="text-sm text-gray-500">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        {{ isEdit ? 'Update' : 'Assign' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
