<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
    students: {
        data: {
            id: number;
            name: string;
            section: string | null;
            year_level: string | null;
            subjects: string[];
        }[];
        links: {
            url: string | null;
            label: string;
            active: boolean;
        }[];
    };
    filters: {
        search?: string;
    };
}>();

const search = ref(props.filters.search || '');
watch(
    search,
    debounce((val) => {
        router.get('/class-lists', { search: val }, { preserveState: true, replace: true });
    }, 300),
);

type SortKey = 'name' | 'section' | 'year_level';
const sortKey = ref<SortKey>('name');
const sortAsc = ref(true);

const toggleSort = (key: SortKey) => {
    if (sortKey.value === key) {
        sortAsc.value = !sortAsc.value;
    } else {
        sortKey.value = key;
        sortAsc.value = true;
    }
};

const sortedStudents = computed(() => {
    return [...props.students.data].sort((a, b) => {
        let aVal = a[sortKey.value]?.toLowerCase() ?? '';
        let bVal = b[sortKey.value]?.toLowerCase() ?? '';
        return aVal < bVal ? (sortAsc.value ? -1 : 1) : aVal > bVal ? (sortAsc.value ? 1 : -1) : 0;
    });
});
</script>

<template>
    <Head title="Class List" />
    <AppLayout>
        <div class="p-6">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <h1 class="text-2xl font-bold text-[#01006c]">📚 My Class List</h1>
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search student name..."
                    class="rounded border border-[#01006c] px-3 py-2 text-sm shadow-sm focus:border-[#ffc60b] focus:outline-none"
                />
            </div>

            <div class="overflow-x-auto rounded border border-[#01006c] bg-white shadow">
                <table class="min-w-full divide-y divide-[#01006c] text-sm">
                    <thead class="bg-[#01006c] text-white">
                        <tr>
                            <th @click="toggleSort('name')" class="cursor-pointer px-4 py-3 text-left">
                                Name <span v-if="sortKey === 'name'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th @click="toggleSort('year_level')" class="cursor-pointer px-4 py-3 text-left">
                                Year Level <span v-if="sortKey === 'year_level'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th @click="toggleSort('section')" class="cursor-pointer px-4 py-3 text-left">
                                Section <span v-if="sortKey === 'section'">{{ sortAsc ? '↑' : '↓' }}</span>
                            </th>
                            <th class="px-4 py-3 text-left">Subjects</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#01006c]">
                        <tr v-for="student in sortedStudents" :key="student.id" class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-[#01006c]">{{ student.name }}</td>
                            <td class="px-4 py-2 text-[#01006c]">{{ student.year_level }}</td>
                            <td class="px-4 py-2 text-[#01006c]">{{ student.section }}</td>
                            <td class="px-4 py-2 text-[#01006c]">
                                <span v-if="student.subjects.length">{{ student.subjects.join(', ') }}</span>
                                <span v-else class="text-gray-400 italic">None</span>
                            </td>
                        </tr>
                        <tr v-if="!sortedStudents.length">
                            <td colspan="4" class="px-4 py-4 text-center text-gray-500">No students found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-center gap-2">
                <template v-for="(link, i) in props.students.links" :key="i">
                    <span v-if="!link.url" class="px-3 py-1 text-sm text-gray-400" v-html="link.label" />
                    <Link
                        v-else
                        :href="link.url"
                        class="inline-flex items-center justify-center rounded-md border px-3 py-1 text-sm transition"
                        :class="{
                            'border-blue-600 bg-blue-600 text-white': link.active,
                            'border-gray-300 text-gray-700 hover:bg-gray-100': !link.active,
                        }"
                    >
                        <span v-html="link.label" />
                    </Link>
                </template>
            </div>
        </div>
    </AppLayout>
</template>
