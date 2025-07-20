<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import {
    ActivityIcon,
    ActivitySquareIcon,
    Briefcase,
    ClipboardListIcon,
    Columns,
    FileArchiveIcon,
    FileUpIcon,
    Layers,
    LayoutDashboardIcon,
    ListCheck,
    LucideFileChartColumnIncreasing,
    LucideTestTube2,
    PuzzleIcon,
    TestTubesIcon,
    TrendingUp,
    UserCheck,
    UserCircle2Icon,
    UserCog,
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const { auth } = usePage().props;
const role = (auth.user as any)?.role || '';

const adminNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: Briefcase,
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
];

const adminNavItemsWithoutDashboard = adminNavItems.filter((item) => item.href !== '/dashboard');

const groupedNavItems: Record<string, NavItem[]> = {
    common: [],
    admin: adminNavItems,
    ict: [
        {
            title: 'ICT Dashboard',
            href: '/ict-dashboard',
            icon: Briefcase,
        },
        ...adminNavItemsWithoutDashboard,
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
            icon: FileUpIcon,
        },
        {
            title: 'Activities',
            href: '/activities',
            icon: ActivityIcon,
        },
        {
            title: 'My Modules',
            href: '/modules',
            icon: PuzzleIcon,
        },
        {
            title: 'Student Activities Results',
            href: '/students-proficiency',
            icon: ActivitySquareIcon,
        },
        {
            title: 'Class List',
            href: '/class-lists',
            icon: ListCheck,
        },
    ],
    principal: [
        {
            title: 'Dashboard',
            href: '/principal-dashboard',
            icon: Briefcase,
        },
        {
            title: 'Student Activities Report',
            href: '/principal-students-proficiency',
            icon: FileArchiveIcon,
        },
        {
            title: 'Teachers Lesson Plans',
            href: '/principal-teachers-lesson-plans',
            icon: LucideFileChartColumnIncreasing,
        },
        {
            title: 'Proficiency Test Result',
            href: '/proficiency-result',
            icon: LucideTestTube2,
        },
    ],
    student: [
        {
            title: 'Dashboard',
            href: '/student-dashboard',
            icon: LayoutDashboardIcon,
        },
        {
            title: 'My Subjects',
            href: '/my-subjects',
            icon: ClipboardListIcon,
        },
    ],
    head: [
        {
            title: 'Dashboard',
            href: '/head-dashboard',
            icon: LayoutDashboardIcon,
        },
        {
            title: 'Teacher Assign',
            href: '/teacher-assignments',
            icon: UserCog,
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
