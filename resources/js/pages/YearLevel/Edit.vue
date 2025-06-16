<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    yearLevel: { id: number; name: string };
}>();

const form = useForm({
    name: props.yearLevel.name,
});

const submit = () => {
    form.put(`/year-levels/${props.yearLevel.id}`);
};
</script>

<template>
    <Head title="Edit Year Level" />
    <AppLayout>
        <div class="mx-auto max-w-md p-4">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold">Edit Year Level</h2>
                <Link href="/year-levels" class="text-blue-600 hover:underline">Back</Link>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="mb-1 block font-semibold">Name</label>
                    <input v-model="form.name" type="text" class="w-full rounded border p-2" />
                    <div v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</div>
                </div>

                <button type="submit" class="rounded bg-green-600 px-4 py-2 text-white">Update</button>
            </form>
        </div>
    </AppLayout>
</template>
