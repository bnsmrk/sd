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
interface Subject {
    id: string;
    name: string;
    year_level_id: string;
    section_id: string;
}

const props = defineProps<{
    students: Student[];
    yearLevels: YearLevel[];
    sections: Section[];
    subjects: Subject[];
}>();

const selectedStudent = ref('');
const selectedYearLevel = ref('');
const selectedSection = ref('');
const selectedSubject = ref('');

const errors = ref({ student: '', yearLevel: '', section: '', subject: '' });

const filteredSections = computed(() => {
    const level = props.yearLevels.find((lvl) => lvl.name === selectedYearLevel.value);
    return level ? props.sections.filter((s) => s.year_level_id === level.id) : [];
});

const filteredSubjects = computed(() => {
    const year = props.yearLevels.find((y) => y.name === selectedYearLevel.value);
    const section = props.sections.find((s) => s.name === selectedSection.value);
    if (year && section) {
        return props.subjects.filter((sub) => sub.year_level_id === year.id && sub.section_id === section.id);
    }
    return [];
});

function saveForm() {
    errors.value = { student: '', yearLevel: '', section: '', subject: '' };

    if (!selectedStudent.value) errors.value.student = 'Select a student.';
    if (!selectedYearLevel.value) errors.value.yearLevel = 'Select a year level.';
    if (!selectedSection.value) errors.value.section = 'Select a section.';
    if (!selectedSubject.value) errors.value.subject = 'Select a subject.';

    if (!Object.values(errors.value).some(Boolean)) {
        router.post('/enroll', {
            user_id: selectedStudent.value,
            year_level: selectedYearLevel.value,
            section: selectedSection.value,
            subject: selectedSubject.value,
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
                    <option v-for="s in props.students" :value="s.id" :key="s.id">{{ s.name }}</option>
                </select>
                <p v-if="errors.student" class="text-sm text-red-500">{{ errors.student }}</p>
            </div>

            <!-- Year Level -->
            <div>
                <label class="font-medium">Year Level</label>
                <select v-model="selectedYearLevel" class="w-full rounded border p-2">
                    <option value="" disabled>Select</option>
                    <option v-for="y in props.yearLevels" :value="y.name" :key="y.id">{{ y.name }}</option>
                </select>
                <p v-if="errors.yearLevel" class="text-sm text-red-500">{{ errors.yearLevel }}</p>
            </div>

            <!-- Section -->
            <div>
                <label class="font-medium">Section</label>
                <select v-model="selectedSection" class="w-full rounded border p-2">
                    <option value="" disabled>Select</option>
                    <option v-for="s in filteredSections" :value="s.name" :key="s.id">{{ s.name }}</option>
                </select>
                <p v-if="errors.section" class="text-sm text-red-500">{{ errors.section }}</p>
            </div>

            <!-- Subject -->
            <div>
                <label class="font-medium">Subject</label>
                <select v-model="selectedSubject" class="w-full rounded border p-2">
                    <option value="" disabled>Select</option>
                    <option v-for="s in filteredSubjects" :value="s.name" :key="s.id">{{ s.name }}</option>
                </select>
                <p v-if="errors.subject" class="text-sm text-red-500">{{ errors.subject }}</p>
            </div>

            <button class="w-full rounded bg-blue-600 py-2 text-white" @click="saveForm">Submit</button>
        </div>
    </AppLayout>
</template>
