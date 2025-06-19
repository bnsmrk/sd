<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface Module {
    id: number;
    name: string;
    year_level: { id: number; name: string };
    section: { id: number; name: string };
    subject: { id: number; name: string };
}

const props = defineProps<{
    modules: Module[];
}>();

const selectedModuleId = ref<number | null>(null);

const selectedModule = computed(() => props.modules.find((m) => m.id === selectedModuleId.value));

const form = useForm({
    title: '',
    module_id: null as number | null,
    file: null as File | null,
});

const errors = ref({
    title: '',
    module_id: '',
    file: '',
});

function submitForm() {
    errors.value = {
        title: '',
        module_id: '',
        file: '',
    };

    if (!form.title) errors.value.title = 'Title is required';
    if (!selectedModuleId.value) errors.value.module_id = 'Module is required';
    if (!form.file) errors.value.file = 'Please select a file';

    if (!Object.values(errors.value).some(Boolean)) {
        const data = new FormData();
        data.append('title', form.title);
        data.append('module_id', selectedModuleId.value!.toString());
        if (form.file) {
            data.append('file', form.file);
        }

        router.post('/materials', data);
    }
}
</script>

<template>
    <Head title="Upload Material" />
    <AppLayout>
        <div class="mx-auto max-w-xl space-y-6 p-6">
            <h2 class="text-xl font-bold">Upload Learning Material</h2>

            <!-- Module -->
            <div>
                <label class="block font-medium">Module</label>
                <select v-model="selectedModuleId" class="w-full rounded border p-2">
                    <option :value="null" disabled>Select Module</option>
                    <option v-for="m in props.modules" :key="m.id" :value="m.id">
                        {{ m.name }}
                    </option>
                </select>
                <p v-if="errors.module_id" class="text-sm text-red-500">{{ errors.module_id }}</p>
            </div>

            <!-- Year Level -->
            <div>
                <label class="block font-medium">Year Level</label>
                <input type="text" class="w-full rounded border bg-gray-100 p-2" :value="selectedModule?.year_level.name || ''" disabled />
            </div>

            <!-- Section -->
            <div>
                <label class="block font-medium">Section</label>
                <input type="text" class="w-full rounded border bg-gray-100 p-2" :value="selectedModule?.section.name || ''" disabled />
            </div>

            <!-- Subject -->
            <div>
                <label class="block font-medium">Subject</label>
                <input type="text" class="w-full rounded border bg-gray-100 p-2" :value="selectedModule?.subject.name || ''" disabled />
            </div>

            <!-- Title -->
            <div>
                <label class="block font-medium">Title</label>
                <input type="text" v-model="form.title" class="w-full rounded border p-2" placeholder="Enter material title" />
                <p v-if="errors.title" class="text-sm text-red-500">{{ errors.title }}</p>
            </div>

            <!-- File Upload -->
            <div>
                <label class="block font-medium">Upload File</label>
                <input
                    type="file"
                    class="w-full"
                    accept=".pdf,.doc,.docx,.ppt,.pptx"
                    @change="
                        (e) => {
                            const target = e.target as HTMLInputElement;
                            if (target?.files?.[0]) {
                                form.file = target.files[0];
                            }
                        }
                    "
                />
                <p v-if="errors.file" class="text-sm text-red-500">{{ errors.file }}</p>
            </div>

            <!-- Submit -->
            <button @click="submitForm" class="w-full rounded bg-blue-600 px-4 py-2 text-white">Upload Material</button>
        </div>
    </AppLayout>
</template>
