<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, BookOpen, CalendarClock, CalendarDays, FileText, ListChecks, Send } from 'lucide-vue-next';

const props = defineProps<{
    test: {
        id: number;
        title: string;
        type: string;
        year_level_id: number;
        scheduled_at: string;
        due_date: string;
        description: string | null;
    };
    yearLevels: Array<{ id: number; name: string }>;
}>();

const form = useForm({
    title: props.test.title,
    type: props.test.type,
    year_level_id: props.test.year_level_id,
    scheduled_at: props.test.scheduled_at,
    due_date: props.test.due_date,
    description: props.test.description ?? '',
});
function cancelEdit() {
    router.get('/proficiency-test');
}
</script>

<template>
    <Head title="Edit Proficiency Test" />
    <AppLayout>
        <div class="mx-auto w-full max-w-4xl space-y-6 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-[#01006c]">Edit Proficiency Test</h1>
                <button
                    @click.prevent="cancelEdit"
                    class="inline-flex items-center gap-2 rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-300 hover:text-gray-900"
                >
                    <ArrowLeft class="h-4 w-4 text-gray-600" />
                    Back
                </button>
            </div>

            <form @submit.prevent="form.put(`/proficiency-test/${props.test.id}`)" class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="col-span-1">
                    <label class="mb-1 block flex items-center gap-2 text-sm font-medium text-gray-700">
                        <FileText class="h-4 w-4 text-gray-500" />
                        Title
                    </label>
                    <input
                        v-model="form.title"
                        type="text"
                        class="w-full rounded border border-gray-300 px-3 py-2 focus:border-transparent focus:ring-2 focus:ring-[#01006c] focus:outline-none"
                        required
                    />
                </div>

                <div class="col-span-1">
                    <label class="mb-1 block flex items-center gap-2 text-sm font-medium text-gray-700">
                        <ListChecks class="h-4 w-4 text-gray-500" />
                        Type
                    </label>
                    <select
                        v-model="form.type"
                        class="w-full rounded border border-gray-300 px-3 py-2 focus:border-transparent focus:ring-2 focus:ring-[#01006c] focus:outline-none"
                        required
                    >
                        <option value="reading">Reading</option>
                        <option value="numerical">Numerical</option>
                    </select>
                </div>

                <div class="col-span-1">
                    <label class="mb-1 block flex items-center gap-2 text-sm font-medium text-gray-700">
                        <BookOpen class="h-4 w-4 text-gray-500" />
                        Year Level
                    </label>
                    <select
                        v-model="form.year_level_id"
                        class="w-full rounded border border-gray-300 px-3 py-2 focus:border-transparent focus:ring-2 focus:ring-[#01006c] focus:outline-none"
                        required
                    >
                        <option disabled value="">-- Choose Year Level --</option>
                        <option v-for="yl in props.yearLevels" :key="yl.id" :value="yl.id">{{ yl.name }}</option>
                    </select>
                </div>

                <div class="col-span-1">
                    <label class="mb-1 block flex items-center gap-2 text-sm font-medium text-gray-700">
                        <CalendarClock class="h-4 w-4 text-gray-500" />
                        Scheduled At
                    </label>
                    <input
                        v-model="form.scheduled_at"
                        type="datetime-local"
                        class="w-full rounded border border-gray-300 px-3 py-2 focus:border-transparent focus:ring-2 focus:ring-[#01006c] focus:outline-none"
                        required
                    />
                </div>

                <div class="col-span-1">
                    <label class="mb-1 block flex items-center gap-2 text-sm font-medium text-gray-700">
                        <CalendarDays class="h-4 w-4 text-gray-500" />
                        Due Date
                    </label>
                    <input
                        v-model="form.due_date"
                        type="datetime-local"
                        class="w-full rounded border border-gray-300 px-3 py-2 focus:border-transparent focus:ring-2 focus:ring-[#01006c] focus:outline-none"
                    />
                </div>

                <div class="col-span-full">
                    <label class="mb-1 block flex items-center gap-2 text-sm font-medium text-gray-700">
                        <FileText class="h-4 w-4 text-gray-500" />
                        Description
                    </label>
                    <textarea
                        v-model="form.description"
                        rows="4"
                        class="w-full rounded border border-gray-300 px-3 py-2 focus:border-transparent focus:ring-2 focus:ring-[#01006c] focus:outline-none"
                    ></textarea>
                </div>

                <div class="col-span-full flex justify-end gap-4 pt-4">
                    <button
                        type="button"
                        @click.prevent="cancelEdit"
                        class="inline-flex items-center gap-2 rounded bg-gray-500 px-4 py-2 text-sm text-white hover:bg-gray-700"
                    >
                        Cancel
                    </button>
                    <button type="submit" class="inline-flex items-center gap-2 rounded bg-[#01006c] px-4 py-2 text-sm text-white hover:bg-[#0d1282]">
                        <Send class="h-4 w-4" />
                        Update
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
.floating-input {
    width: 100%;
    border: 2px solid #01006c;
    border-radius: 0.5rem;
    padding: 0.5rem 0.75rem;
    margin-top: 0.25rem;
}
</style>
