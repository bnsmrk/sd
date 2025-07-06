<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm,Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { ArrowLeft, User, Layers, Layout, BookOpen, Save } from 'lucide-vue-next';
const props = defineProps<{
    assignment: {
        id: number;
        user_id: number;
        year_level_id: number;
        section_id: number;
        subject_id: number;
    };
    teachers: Array<{ id: number; name: string }>;
    yearLevels: Array<{ id: number; name: string }>;
    sections: Array<{ id: number; name: string; year_level_id: number }>;
    subjects: Array<{ id: number; name: string; year_level_id: number; section_id: number | null }>;
}>();

const form = useForm({
    user_id: props.assignment.user_id,
    year_level_id: props.assignment.year_level_id,
    section_id: props.assignment.section_id,
    subject_id: props.assignment.subject_id,
});

const filteredSections = computed(() => props.sections.filter((section) => section.year_level_id === Number(form.year_level_id)));

const filteredSubjects = computed(() => {
    const ylId = Number(form.year_level_id);
    const secId = Number(form.section_id);
    return props.subjects.filter((subject) => subject.year_level_id === ylId && (subject.section_id === null || subject.section_id === secId));
});

function submitForm() {
    form.put(`/teacher-assignments/${props.assignment.id}`);
}
</script>

  <template>
    <Head title="Edit Teacher Assignment" />
    <AppLayout>
      <div class="max-w-3xl mx-auto mt-10 bg-white p-8 rounded-lg shadow dark:bg-gray-800">
        <!-- Back Button -->
        <div class="mb-6">
          <Link
            href="/teacher-assignments"
            class="inline-flex items-center gap-2 rounded border border-[#01006c] bg-white px-4 py-2 text-sm font-semibold text-[#01006c] shadow hover:bg-[#ffc60b]"
          >
            <ArrowLeft class="w-4 h-4" /> Back
          </Link>
        </div>

        <!-- Title -->
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Edit Teacher Assignment</h1>

        <!-- Form -->
        <form @submit.prevent="submitForm" class="space-y-6">
          <div class="grid grid-cols-4 gap-4">
            <!-- Teacher -->
            <div>
              <label class="flex items-center gap-1 mb-2 text-sm font-medium text-gray-900 dark:text-white">
                <User class="w-4 h-4" /> Teacher
              </label>
              <select v-model="form.user_id" required class="input-select">
                <option value="">Select a Teacher</option>
                <option v-for="t in props.teachers" :key="t.id" :value="t.id">{{ t.name }}</option>
              </select>
              <p v-if="form.errors.user_id" class="text-sm text-red-600 mt-1">{{ form.errors.user_id }}</p>
            </div>

            <!-- Year Level -->
            <div>
              <label class="flex items-center gap-1 mb-2 text-sm font-medium text-gray-900 dark:text-white">
                <Layers class="w-4 h-4" /> Year Level
              </label>
              <select v-model="form.year_level_id" required class="input-select">
                <option value="">Select a Year Level</option>
                <option v-for="y in props.yearLevels" :key="y.id" :value="y.id">{{ y.name }}</option>
              </select>
              <p v-if="form.errors.year_level_id" class="text-sm text-red-600 mt-1">{{ form.errors.year_level_id }}</p>
            </div>

            <!-- Section -->
            <div>
              <label class="flex items-center gap-1 mb-2 text-sm font-medium text-gray-900 dark:text-white">
                <Layout class="w-4 h-4" /> Section
              </label>
              <select v-model="form.section_id" required class="input-select">
                <option value="">Select a Section</option>
                <option v-for="s in filteredSections" :key="s.id" :value="s.id">{{ s.name }}</option>
              </select>
              <p v-if="form.errors.section_id" class="text-sm text-red-600 mt-1">{{ form.errors.section_id }}</p>
            </div>

            <!-- Subject -->
            <div>
              <label class="flex items-center gap-1 mb-2 text-sm font-medium text-gray-900 dark:text-white">
                <BookOpen class="w-4 h-4" /> Subject
              </label>
              <select v-model="form.subject_id" required class="input-select">
                <option value="">Select a Subject</option>
                <option v-for="s in filteredSubjects" :key="s.id" :value="s.id">{{ s.name }}</option>
              </select>
              <p v-if="form.errors.subject_id" class="text-sm text-red-600 mt-1">{{ form.errors.subject_id }}</p>
            </div>
          </div>

          <!-- Submit -->
          <div class="flex justify-end pt-4">
            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex items-center gap-2 text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-8 py-2.5 dark:bg-gray-800 dark:hover:bg-gray-700"
            >
              <Save class="w-4 h-4" /> Update Assignment
            </button>
          </div>
        </form>
      </div>
    </AppLayout>
  </template>

