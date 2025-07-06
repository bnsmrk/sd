<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen,  LayoutDashboardIcon,UserCircle2Icon, TrendingUp,Award,Columns,Layers,
    LucideFileChartColumnIncreasing,FileArchiveIcon,BookOpenCheck,Briefcase, LayoutGrid, UserCheck, TestTubesIcon,LucideTestTube2, UserCog , User2} 
from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const { auth } = usePage().props;
const role = (auth.user as any)?.role || '';

const groupedNavItems: Record<string, NavItem[]> = {
    common: [
        // {
        //     title: 'Dashboard',
        //     href: '/dashboard', 
        //     icon: LayoutGrid,
        // },
    ],
    admin: [
        {
            title: 'Dashboard',
            href: '/dashboard',
            icon: LayoutDashboardIcon,
        },
        {
            title: 'Year Levels',
            href: '/year-levels',
            icon: TrendingUp,
        },
        {
            title: 'Sections',
            href: '/sections',
            icon: Columns,
        },
        {
            title: 'Subjects',
            href: '/subjects',
            icon: Layers,
        },
        {
            title: 'Enroll Students',
            href: '/enroll',
            icon: UserCircle2Icon,
        },
        {
            title: 'Register User',
            href: '/users',
            icon: UserCheck,
        },
        {
            title: 'Teacher Assign',
            href: '/teacher-assignments',
            icon: UserCog,
        },
        {
            title: 'Student Proficiency Report',
            href: '/principal-students-proficiency',
            icon: FileArchiveIcon,
        },
        {
            title: 'Teachers Lesson Plans',
            href: '/principal-teachers-lesson-plans',
            icon: LucideFileChartColumnIncreasing,
        },
    ],
    teacher: [
        {
            title: 'Dashboard',
            href: '/teacher-dashboard',
            icon: Briefcase,
        },
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
        {
            title: 'Student Proficiency',
            href: '/students-proficiency',
            icon: Briefcase,
        },
    ],
    principal: [
        {
            title: 'Dashboard',
            href: '/principal-dashboard',
            icon: Briefcase,
        },
    ],
    student: [
        {
            title: 'Dashboard',
            href: '/student-dashboard',
            icon: Briefcase,
        },
        {
            title: 'My Subjects',
            href: '/my-subjects',
            icon: BookOpen,
        },
    ],
    head: [
        {
            title: 'Dashboard',
            href: '/head-dashboard',
            icon: LayoutDashboardIcon,
        },
        {
            title: 'Proficiency Test',
            href: '/proficiency-test',
            icon: TestTubesIcon,
        },
        {
            title: 'Proficiency Test Result',
            href: '/proficiency-result',
            icon: LucideTestTube2,
        },
    ],
    ict: [
        {
            title: 'ICT Dashboard',
            href: '/ict-dashboard',
            icon: Briefcase,
        },
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
