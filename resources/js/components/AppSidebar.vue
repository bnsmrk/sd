<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Briefcase, LayoutGrid, Users } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const { auth } = usePage().props;
const role = (auth.user as any)?.role || '';

const groupedNavItems: Record<string, NavItem[]> = {
    common: [
        {
            title: 'Dashboard',
            href: '/dashboard',
            icon: LayoutGrid,
        },
    ],
    admin: [
        {
            title: 'Year Levels',
            href: '/year-levels',
            icon: BookOpen,
        },
        {
            title: 'Sections',
            href: '/sections',
            icon: LayoutGrid,
        },
        {
            title: 'Subjects',
            href: '/subjects',
            icon: BookOpen,
        },
        {
            title: 'Enroll Students',
            href: '/enroll',
            icon: Users,
        },
        {
            title: 'Register User',
            href: '/users',
            icon: Users,
        },
    ],
    teacher: [
        {
            title: 'Upload Materials',
            href: '/materials',
            icon: Briefcase,
        },
        {
            title: 'Activities',
            href: '/activities',
            icon: Briefcase,
        },
        {
            title: 'My Modules',
            href: '/modules',
            icon: Briefcase,
        },
    ],
    student: [
        {
            title: 'My Subjects',
            href: '/my-subjects',
            icon: BookOpen,
        },
        // {
        //     title: 'My Activities',
        //     href: '/my-activities',
        //     icon: Briefcase,
        // },
    ],
};

const mainNavItems: NavItem[] = [...groupedNavItems.common, ...(role && groupedNavItems[role] ? groupedNavItems[role] : [])];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>

    <slot />
</template>
