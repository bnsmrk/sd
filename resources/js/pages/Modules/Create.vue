<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Book, GraduationCap, Layers, Users } from 'lucide-vue-next';
import { computed, watch } from 'vue';

const props = defineProps<{
    assignments: Array<{
        id: number;
        name: string;
        sections: Array<{ id: number; name: string }>;
        subjects: Array<{ id: number; name: string; section_ids: number[] }>;
    }>;
}>();

const form = useForm({
    name: '',
    year_level_id: '',
    section_id: '',
    subject_id: '',
});

const selectedYearLevel = computed(() => props.assignments.find((a) => a.id === Number(form.year_level_id)));

const availableSections = computed(() => (selectedYearLevel.value ? selectedYearLevel.value.sections : []));

const filteredSubjects = computed(() => {
    if (!selectedYearLevel.value || !form.section_id) return [];

    return selectedYearLevel.value.subjects.filter((subject) => subject.section_ids.includes(Number(form.section_id)));
});

watch(
    () => form.year_level_id,
    () => {
        form.section_id = '';
        form.subject_id = '';
    },
);

watch(
    () => form.section_id,
    () => {
        form.subject_id = '';
    },
);
</script>

<template>
    <Head title="Create Module" />
    <AppLayout>
        <div class="mx-auto w-full max-w-4xl space-y-6 px-6 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-800">Create Module</h1>
                <Link href="/modules" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600 hover:underline">
                    <ArrowLeft class="h-4 w-4" /> Back to Modules
                </Link>
            </div>

            <form @submit.prevent="form.post('/modules')" class="space-y-6">
                <div>
                    <label class="flex items-center gap-1 text-sm font-medium text-gray-700"> <Layers class="h-4 w-4" /> Module Name </label>
                    <input v-model="form.name" class="w-full rounded border px-3 py-2" required />
                    <div v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</div>
                </div>

                <div>
                    <label class="flex items-center gap-1 text-sm font-medium text-gray-700"> <GraduationCap class="h-4 w-4" /> Year Level </label>
                    <select v-model="form.year_level_id" class="w-full rounded border px-3 py-2" required>
                        <option value="">Select Year Level</option>
                        <option v-for="yl in props.assignments" :key="yl.id" :value="yl.id">
                            {{ yl.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.year_level_id" class="text-sm text-red-600">{{ form.errors.year_level_id }}</div>
                </div>

                <div v-if="availableSections.length > 0">
                    <label class="flex items-center gap-1 text-sm font-medium text-gray-700"> <Users class="h-4 w-4" /> Section </label>
                    <select v-model="form.section_id" class="w-full rounded border px-3 py-2" required>
                        <option value="">Select Section</option>
                        <option v-for="section in availableSections" :key="section.id" :value="section.id">
                            {{ section.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.section_id" class="text-sm text-red-600">{{ form.errors.section_id }}</div>
                </div>

                <div v-if="filteredSubjects.length > 0">
                    <label class="flex items-center gap-1 text-sm font-medium text-gray-700"> <Book class="h-4 w-4" /> Subject </label>
                    <select v-model="form.subject_id" class="w-full rounded border px-3 py-2" required>
                        <option value="">Select Subject</option>
                        <option v-for="subject in filteredSubjects" :key="subject.id" :value="subject.id">
                            {{ subject.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.subject_id" class="text-sm text-red-600">{{ form.errors.subject_id }}</div>
                </div>

                <div>
                    <button class="w-full rounded bg-blue-600 py-2 text-white hover:bg-blue-700" :disabled="form.processing">Save</button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
