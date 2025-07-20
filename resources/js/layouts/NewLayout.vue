<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Briefcase, Columns, Layers, LogOut, Menu, TrendingUp, UserCheck, UserCircle2, UserCog } from 'lucide-vue-next';
import { ref } from 'vue';

const sidebarOpen = ref(true);
const { props } = usePage();

const navLinks = [
    { title: 'Dashboard', href: '/dashboard', icon: Briefcase },
    { title: 'Year Levels', href: '/year-levels', icon: TrendingUp },
    { title: 'Sections', href: '/sections', icon: Columns },
    { title: 'Subjects', href: '/subjects', icon: Layers },
    { title: 'Enroll Students', href: '/enroll', icon: UserCircle2 },
    { title: 'Register User', href: '/users', icon: UserCheck },
    { title: 'Teacher Assign', href: '/teacher-assignments', icon: UserCog },
];
</script>

<template>
    <div class="flex h-screen bg-gray-100 text-gray-800">
        <aside :class="['bg-white shadow-md transition-all duration-300 ease-in-out', sidebarOpen ? 'w-64' : 'w-16']" class="flex h-full flex-col">
            <div class="flex items-center justify-between border-b px-4 py-4">
                <span v-if="sidebarOpen" class="text-lg font-bold">Admin Panel</span>
                <button @click="sidebarOpen = !sidebarOpen">
                    <Menu class="h-6 w-6" />
                </button>
            </div>
            <nav class="flex-1 space-y-2 px-2 py-4">
                <Link
                    v-for="link in navLinks"
                    :key="link.href"
                    :href="link.href"
                    class="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100"
                    :class="{ 'justify-center': !sidebarOpen }"
                >
                    <component :is="link.icon" class="h-5 w-5" />
                    <span v-if="sidebarOpen">{{ link.title }}</span>
                </Link>
            </nav>
            <div class="border-t p-2">
                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="flex items-center gap-3 rounded px-3 py-2 text-red-500 hover:bg-red-100"
                    :class="{ 'justify-center': !sidebarOpen }"
                >
                    <LogOut class="h-5 w-5" />
                    <span v-if="sidebarOpen">Logout</span>
                </Link>
            </div>
        </aside>

        <div class="flex flex-1 flex-col">
            <header class="flex items-center justify-between bg-white px-6 py-4 shadow">
                <h1 class="text-lg font-semibold">
                    {{ $page.component.split('/').pop() || 'Dashboard' }}
                </h1>
                <div class="text-sm text-gray-600">Welcome, {{ props.auth?.user?.name || 'Admin' }}</div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped></style>
