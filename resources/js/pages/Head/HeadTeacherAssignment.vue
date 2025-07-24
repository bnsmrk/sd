<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { ref, watch } from 'vue';

const props = defineProps<{
    teachers: {
        data: {
            id: number;
            name: string;
            email: string;
            subject_count: number;
            section_count: number;
            year_level_count: number;
        }[];
        links: any[];
    };
    filters: { search?: string };
}>();

const search = ref(props.filters?.search ?? '');

watch(
    search,
    debounce((val) => {
        router.get(route('head-teacher-assignments.index'), { search: val }, { preserveState: true, replace: true });
    }, 300),
);
</script>

<template>
    <Head title="Assign Teacher" />
    <AppLayout :breadcrumbs="[{ title: 'Assign Teacher', href: route('head-teacher-assignments.index') }]">
        <div class="min-h-screen bg-pink-50 px-6 py-8">
            <h1 class="mb-6 text-3xl font-bold text-[#01006c]">Assign Teacher to Sections & Subjects</h1>

            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search teachers..."
                    class="w-full max-w-xs rounded border px-4 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500"
                />
                <Link
                    :href="route('head-teacher-assignments.create')"
                    class="rounded bg-pink-600 px-4 py-2 text-sm font-medium text-white hover:bg-pink-700"
                >
                    Assign Teachers
                </Link>
            </div>

            <div class="rounded-xl bg-white p-6 shadow">
                <h2 class="mb-4 text-lg font-semibold text-indigo-800">All Teachers</h2>

                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto border border-gray-300">
                        <thead class="bg-indigo-100 text-indigo-800">
                            <tr>
                                <th class="px-4 py-2 text-left">#</th>
                                <th class="px-4 py-2 text-left">Name</th>
                                <th class="px-4 py-2 text-left">Email</th>
                                <th class="px-4 py-2 text-left">Year Levels</th>
                                <th class="px-4 py-2 text-left">Sections</th>
                                <th class="px-4 py-2 text-left">Subjects</th>
                                <th class="px-4 py-2 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(teacher, i) in props.teachers.data" :key="teacher.id" class="border-t">
                                <td class="px-4 py-2">{{ i + 1 }}</td>
                                <td class="px-4 py-2">{{ teacher.name }}</td>
                                <td class="px-4 py-2">{{ teacher.email }}</td>
                                <td class="px-4 py-2">{{ teacher.year_level_count }}</td>
                                <td class="px-4 py-2">{{ teacher.section_count }}</td>
                                <td class="px-4 py-2">{{ teacher.subject_count }}</td>
                                <td class="space-x-2 px-4 py-2">
                                    <Link
                                        :href="route('head-teacher-assignments.show', teacher.id)"
                                        class="rounded bg-indigo-600 px-3 py-1 text-sm text-white hover:bg-indigo-700"
                                    >
                                        View
                                    </Link>
                                    <Link
                                        :href="route('head-teacher-assignments.edit', teacher.id)"
                                        class="rounded bg-yellow-500 px-3 py-1 text-sm text-white hover:bg-yellow-600"
                                    >
                                        Edit
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <component
                        v-for="link in props.teachers.links"
                        :key="link.label"
                        :is="link.url ? Link : 'span'"
                        :href="link.url || undefined"
                        v-html="link.label"
                        :class="[
                            'rounded border px-3 py-1 text-sm',
                            link.active ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100',
                            !link.url && 'pointer-events-none text-gray-400',
                        ]"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
