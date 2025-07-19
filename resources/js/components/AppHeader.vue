<script setup lang="ts">
import type { BreadcrumbItem, NavItem } from '@/types';
import { Link, router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { Bell, ClipboardCheck, LayoutDashboard, Menu } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

import AppLogo from '@/components/AppLogo.vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { NavigationMenu, NavigationMenuItem, NavigationMenuList, navigationMenuTriggerStyle } from '@/components/ui/navigation-menu';
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { getInitials } from '@/composables/useInitials';

interface Props {
    breadcrumbs?: BreadcrumbItem[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage();
const auth = page.props.auth as any;
const role = auth.user?.role ?? '';

const isCurrentRoute = computed(() => (url: string) => page.url === url);
const activeItemStyles = computed(
    () => (url: string) => (isCurrentRoute.value(url) ? 'text-neutral-900 dark:bg-neutral-800 dark:text-neutral-100' : ''),
);

const groupedNavItems: Record<string, NavItem[]> = {
    common: [],
    student: [
        { title: 'Dashboard', href: '/student-dashboard', icon: LayoutDashboard },
        { title: 'My Subjects', href: '/my-subjects', icon: ClipboardCheck },
    ],
};

const mainNavItems: NavItem[] = [...groupedNavItems.common, ...(role && groupedNavItems[role] ? groupedNavItems[role] : [])];

// 🔔 Notifications
interface NotificationData {
    id: string;
    data: {
        title: string;
        body: string;
        url: string;
    };
    read_at: string | null;
}

const notifications = ref<NotificationData[]>([]);
const unreadCount = computed(() => notifications.value.filter((n) => !n.read_at).length);

const fetchNotifications = async () => {
    try {
        const res = await axios.get('/notifications');
        notifications.value = res.data;
    } catch (err) {
        console.error('Notification fetch failed:', err);
    }
};

const markAsRead = async (id: string) => {
    try {
        await axios.post('/notifications/mark-as-read', { id });
        const notif = notifications.value.find((n) => n.id === id);
        if (notif) notif.read_at = new Date().toISOString(); // update locally
    } catch (err) {
        console.error('Failed to mark notification as read:', err);
    }
};

onMounted(fetchNotifications);
</script>

<template>
    <div>
        <div class="border-b border-sidebar-border/80">
            <div class="mx-auto flex h-16 items-center px-4 md:max-w-7xl">
                <!-- Mobile -->
                <div class="lg:hidden">
                    <Sheet>
                        <SheetTrigger :as-child="true">
                            <Button variant="ghost" size="icon" class="mr-2 h-9 w-9">
                                <Menu class="h-5 w-5" />
                            </Button>
                        </SheetTrigger>
                        <SheetContent side="left" class="w-[300px] p-6">
                            <SheetTitle class="sr-only">Navigation Menu</SheetTitle>
                            <SheetHeader class="flex justify-start text-left">
                                <AppLogoIcon class="size-6 fill-current text-black dark:text-white" />
                            </SheetHeader>
                            <div class="flex h-full flex-1 flex-col justify-between space-y-4 py-6">
                                <nav class="-mx-3 space-y-1">
                                    <Link
                                        v-for="item in mainNavItems"
                                        :key="item.title"
                                        :href="item.href"
                                        class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-accent"
                                        :class="activeItemStyles(item.href)"
                                    >
                                        <component v-if="item.icon" :is="item.icon" class="h-5 w-5" />
                                        {{ item.title }}
                                    </Link>
                                </nav>
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>

                <!-- Logo -->
                <div class="flex items-center gap-x-2">
                    <AppLogo />
                </div>

                <!-- Desktop Menu -->
                <div class="hidden h-full lg:flex lg:flex-1">
                    <NavigationMenu class="ml-10 flex h-full items-stretch">
                        <NavigationMenuList class="flex h-full items-stretch space-x-2">
                            <NavigationMenuItem v-for="(item, index) in mainNavItems" :key="index" class="relative flex h-full items-center">
                                <Link
                                    :class="[navigationMenuTriggerStyle(), activeItemStyles(item.href), 'h-9 cursor-pointer px-3']"
                                    :href="item.href"
                                >
                                    <component v-if="item.icon" :is="item.icon" class="mr-2 h-4 w-4" />
                                    {{ item.title }}
                                </Link>
                                <div
                                    v-if="isCurrentRoute(item.href)"
                                    class="absolute bottom-0 left-0 h-0.5 w-full translate-y-px bg-black dark:bg-white"
                                ></div>
                            </NavigationMenuItem>
                        </NavigationMenuList>
                    </NavigationMenu>
                </div>

                <!-- Right side: Notifications & Avatar -->
                <div class="ml-auto flex items-center space-x-2">
                    <!-- 🔔 Notifications -->
                    <div class="relative flex items-center space-x-1">
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" size="icon" class="group relative h-9 w-9 cursor-pointer">
                                    <Bell class="size-5 opacity-80 group-hover:opacity-100" />
                                    <span v-if="unreadCount > 0" class="absolute -top-0.5 -right-0.5 inline-flex h-2 w-2 rounded-full bg-red-500" />
                                </Button>
                            </DropdownMenuTrigger>

                            <DropdownMenuContent align="end" class="w-80">
                                <div class="p-2 text-sm font-medium text-gray-700">Notifications</div>
                                <div class="max-h-60 divide-y overflow-y-auto">
                                    <div
                                        v-for="n in notifications"
                                        :key="n.id"
                                        class="cursor-pointer p-2 hover:bg-gray-100 dark:hover:bg-gray-800"
                                        @click="
                                            () => {
                                                console.log('Navigating to:', n.data.url);
                                                markAsRead(n.id);
                                                router.visit(n.data.url);
                                            }
                                        "
                                    >
                                        <div class="flex items-center justify-between">
                                            <div class="font-semibold" :class="n.read_at ? 'text-gray-500' : 'text-black dark:text-white'">
                                                {{ n.data.title }}
                                            </div>
                                            <span
                                                v-if="n.read_at"
                                                class="ml-2 rounded bg-gray-200 px-2 py-0.5 text-xs text-gray-700 dark:bg-gray-700 dark:text-gray-300"
                                            >
                                                Read
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-500">{{ n.data.body }}</div>
                                    </div>
                                </div>
                                <div v-if="notifications.length === 0" class="p-4 text-center text-sm text-gray-500">No new notifications</div>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>

                    <!-- 👤 Avatar -->
                    <DropdownMenu>
                        <DropdownMenuTrigger :as-child="true">
                            <Button
                                variant="ghost"
                                size="icon"
                                class="relative size-10 w-auto rounded-full p-1 focus-within:ring-2 focus-within:ring-primary"
                            >
                                <Avatar class="size-8 overflow-hidden rounded-full">
                                    <AvatarImage v-if="auth.user.avatar" :src="auth.user.avatar" :alt="auth.user.name" />
                                    <AvatarFallback class="rounded-lg bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white">
                                        {{ getInitials(auth.user?.name) }}
                                    </AvatarFallback>
                                </Avatar>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-56">
                            <UserMenuContent :user="auth.user" />
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>
        </div>

        <!-- Breadcrumb -->
        <div v-if="props.breadcrumbs.length > 1" class="flex w-full border-b border-sidebar-border/70">
            <div class="mx-auto flex h-12 w-full items-center justify-start px-4 text-neutral-500 md:max-w-7xl">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>
        </div>
    </div>
</template>
