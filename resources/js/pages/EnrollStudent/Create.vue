<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Student {
    id: string;
    name: string;
}
interface YearLevel {
    id: string;
    name: string;
}

const props = defineProps<{
    students: Student[];
    yearLevels: YearLevel[];
}>();

const selectedStudent = ref('');
const selectedYearLevel = ref('');
const errors = ref({ student: '', yearLevel: '' });

function saveForm() {
    errors.value = { student: '', yearLevel: '' };

    if (!selectedStudent.value) errors.value.student = 'Select a student.';
    if (!selectedYearLevel.value) errors.value.yearLevel = 'Select a year level.';

    if (!Object.values(errors.value).some(Boolean)) {
        router.post('/enroll', {
            user_id: selectedStudent.value,
            year_level: selectedYearLevel.value,
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

            <button class="w-full rounded bg-blue-600 py-2 text-white" @click="saveForm">Submit</button>
        </div>
    </AppLayout>
</template>
