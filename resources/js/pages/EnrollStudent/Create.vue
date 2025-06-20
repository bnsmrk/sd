<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface Student {
    id: string;
    name: string;
}
interface YearLevel {
    id: string;
    name: string;
}
interface Section {
    id: string;
    name: string;
    year_level_id: string;
}

const props = defineProps<{
    students: Student[];
    yearLevels: YearLevel[];
    sections: Section[];
}>();

const selectedStudent = ref('');
const selectedYearLevel = ref('');
const selectedSection = ref('');
const errors = ref({ student: '', yearLevel: '', section: '' });

const filteredSections = computed(() => props.sections.filter((section) => section.year_level_id === selectedYearLevel.value));

function saveForm() {
    errors.value = { student: '', yearLevel: '', section: '' };

    if (!selectedStudent.value) errors.value.student = 'Select a student.';
    if (!selectedYearLevel.value) errors.value.yearLevel = 'Select a year level.';
    if (!selectedSection.value) errors.value.section = 'Select a section.';

    if (!Object.values(errors.value).some(Boolean)) {
        router.post('/enroll', {
            user_id: selectedStudent.value,
            year_level_id: selectedYearLevel.value,
            section_id: selectedSection.value,
        });
    }
}
</script>

<template>
    <Head title="Enroll Student" />
    <AppLayout :breadcrumbs="[{ title: 'Enroll', href: '/enroll' }]">
        <div class="mx-auto max-w-xl space-y-6 p-6">
            <h2 class="text-xl font-bold">Enroll a Student</h2>

            <!-- Student -->
            <div>
                <label class="font-medium">Student</label>
                <select v-model="selectedStudent" class="w-full rounded border p-2">
                    <option value="" disabled>Select</option>
                    <option v-for="s in props.students" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>
                <p v-if="errors.student" class="text-sm text-red-500">{{ errors.student }}</p>
            </div>

            <!-- Year Level -->
            <div>
                <label class="font-medium">Year Level</label>
                <select v-model="selectedYearLevel" class="w-full rounded border p-2">
                    <option value="" disabled>Select</option>
                    <option v-for="y in props.yearLevels" :key="y.id" :value="y.id">{{ y.name }}</option>
                </select>
                <p v-if="errors.yearLevel" class="text-sm text-red-500">{{ errors.yearLevel }}</p>
            </div>

            <!-- Section -->
            <div v-if="filteredSections.length">
                <label class="font-medium">Section</label>
                <select v-model="selectedSection" class="w-full rounded border p-2">
                    <option value="" disabled>Select</option>
                    <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>
                <p v-if="errors.section" class="text-sm text-red-500">{{ errors.section }}</p>
            </div>

            <button class="w-full rounded bg-blue-600 py-2 text-white" @click="saveForm">Submit</button>
        </div>
    </AppLayout>
</template>
