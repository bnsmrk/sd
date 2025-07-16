<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const isLoading = computed(() => isCreating.value || isUpdating.value || isDeleting.value);
const isCreating = ref(false);
const isUpdating = ref(false);
const isDeleting = ref(false);

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
        isCreating.value = true;
        router.post(
            '/enroll',
            {
                user_id: selectedStudent.value,
                year_level_id: selectedYearLevel.value,
                section_id: selectedSection.value,
            },
            {
                onError: (serverErrors: any) => {
                    errors.value.student = serverErrors.user_id || '';
                    errors.value.yearLevel = serverErrors.year_level_id || '';
                    errors.value.section = serverErrors.section_id || '';
                },
                onFinish: () => {
                    setTimeout(() => {
                        isCreating.value = false;
                    }, 800);
                },
            },
        );
    }
}
</script>

<template>
    <Head title="Enroll Student" />
    <AppLayout :breadcrumbs="[{ title: 'Enroll', href: '/enroll' }]">
        <div v-if="isLoading" class="fixed inset-0 z-50 flex items-center justify-center bg-white/30 backdrop-blur-sm">
            <div class="flex flex-col items-center gap-4">
                <div class="relative h-16 w-16">
                    <div class="animate-spin-slow-cw absolute inset-0 rounded-full border-4 border-blue-600 border-t-transparent"></div>

                    <div class="animate-spin-slow-ccw absolute inset-2 rounded-full border-4 border-yellow-400 border-t-transparent"></div>

                    <div class="animate-spin-fast-cw absolute inset-4 rounded-full border-4 border-pink-500 border-t-transparent"></div>
                </div>

                <div class="text-center">
                    <span class="block animate-pulse text-base font-semibold text-[#01006c]">Processing Request...</span>
                    <span class="text-xs text-[#01006c]/70">This may take a moment</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 p-6 md:grid-cols-2">
            <div class="space-y-6">
                <div>
                    <Link
                        href="/enroll"
                        class="inline-flex items-center gap-2 rounded border border-[#01006c] bg-white px-4 py-2 text-sm font-semibold text-[#01006c] shadow hover:bg-[#ffc60b]"
                    >
                        <ArrowLeft class="h-4 w-4" /> Back
                    </Link>
                </div>

                <h2 class="text-xl font-bold text-[#01006c]">📝 Enroll a Student</h2>

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

                <button
                    class="inline-flex w-full items-center justify-center gap-2 rounded bg-[#01006c] py-2 text-white transition hover:bg-[#0d1282]"
                    @click="saveForm"
                >
                    <Save class="h-4 w-4" /> Submit
                </button>
            </div>

            <div v-if="filteredSubjects.length" class="space-y-3 rounded border border-[#01006c] bg-white p-4 shadow">
                <h3 class="text-lg font-semibold text-[#01006c]">📚 Subjects in Selected Year Level</h3>
                <ul class="ml-4 list-disc text-sm text-gray-700">
                    <li v-for="subject in filteredSubjects" :key="subject.id">{{ subject.name }}</li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>
<style scoped>
@keyframes spin-cw {
    to {
        transform: rotate(360deg);
    }
}

@keyframes spin-ccw {
    to {
        transform: rotate(-360deg);
    }
}

.animate-spin-slow-cw {
    animation: spin-cw 2s linear infinite;
}

.animate-spin-slow-ccw {
    animation: spin-ccw 3s linear infinite;
}

.animate-spin-fast-cw {
    animation: spin-cw 1s linear infinite;
}
</style>
