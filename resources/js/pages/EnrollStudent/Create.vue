<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';
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
    section_id: string | null;
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
const errors = ref({ student: '', yearLevel: '', section: '' });

const filteredSections = computed(() => props.sections.filter((section) => section.year_level_id === selectedYearLevel.value));

const filteredSubjects = computed(() => props.subjects.filter((subject) => subject.year_level_id === selectedYearLevel.value));

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
        <div class="grid grid-cols-1 gap-8 p-6 md:grid-cols-2">
            <!-- Form Column -->
            <div class="space-y-6">
                <!-- Back Button -->
                <div>
                    <Link
                        href="/enroll"
                        class="inline-flex items-center gap-2 rounded border border-[#01006c] bg-white px-4 py-2 text-sm font-semibold text-[#01006c] shadow hover:bg-[#ffc60b]"
                    >
                        <ArrowLeft class="h-4 w-4" /> Back
                    </Link>
                </div>

                <!-- Title -->
                <h2 class="text-xl font-bold text-[#01006c]">📝 Enroll a Student</h2>

                <!-- Student -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-[#ff69b4]">👤 Student</label>
                    <select
                        v-model="selectedStudent"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                    >
                        <option value="" disabled>Select</option>
                        <option v-for="s in props.students" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                    <p v-if="errors.student" class="text-sm text-red-500">{{ errors.student }}</p>
                </div>

                <!-- Year Level -->
                <div>
                    <label class="mb-1 block text-sm font-medium text-[#ff69b4]">🎓 Year Level</label>
                    <select
                        v-model="selectedYearLevel"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                    >
                        <option value="" disabled>Select</option>
                        <option v-for="y in props.yearLevels" :key="y.id" :value="y.id">{{ y.name }}</option>
                    </select>
                    <p v-if="errors.yearLevel" class="text-sm text-red-500">{{ errors.yearLevel }}</p>
                </div>

                <!-- Section -->
                <div v-if="filteredSections.length">
                    <label class="mb-1 block text-sm font-medium text-[#ff69b4]">🏫 Section</label>
                    <select
                        v-model="selectedSection"
                        class="w-full rounded border border-[#01006c] bg-white px-3 py-2 focus:border-[#ffc60b] focus:outline-none"
                    >
                        <option value="" disabled>Select</option>
                        <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                    <p v-if="errors.section" class="text-sm text-red-500">{{ errors.section }}</p>
                </div>

                <!-- Submit Button -->
                <button
                    class="inline-flex w-full items-center justify-center gap-2 rounded bg-[#01006c] py-2 text-white transition hover:bg-[#0d1282]"
                    @click="saveForm"
                >
                    <Save class="h-4 w-4" /> Submit
                </button>
            </div>

            <!-- Subjects Column -->
            <div v-if="filteredSubjects.length" class="space-y-3 rounded border border-[#01006c] bg-white p-4 shadow">
                <h3 class="text-lg font-semibold text-[#01006c]">📚 Subjects in Selected Year Level</h3>
                <ul class="ml-4 list-disc text-sm text-gray-700">
                    <li v-for="subject in filteredSubjects" :key="subject.id">{{ subject.name }}</li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>
