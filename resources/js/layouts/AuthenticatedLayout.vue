<script setup>
import { computed, ref, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import Dropdown from '@/components/ui/Dropdown.vue';
import navigation, { activeTrail } from '@/config/navigation';
import { useDarkMode } from '@/composables/useDarkMode';

const page = usePage();
const { isDark, toggleDark } = useDarkMode();

const user = computed(() => page.props.auth?.user);
const role = computed(() => page.props.auth?.role);
const permissions = computed(() => page.props.auth?.permissions ?? []);

const collapsed = ref(loadBoolean('siakad-sidebar', false));
const mobileOpen = ref(false);
const openMenus = ref(loadArray('siakad-menus', ['Master Data', 'Laporan Akademik']));

watch(collapsed, (value) => localStorage.setItem('siakad-sidebar', value ? '1' : '0'));
watch(openMenus, (value) => localStorage.setItem('siakad-menus', JSON.stringify(value)), { deep: true });

function loadBoolean(key, fallback) {
    if (typeof window === 'undefined') return fallback;
    return localStorage.getItem(key) === '1';
}

function loadArray(key, fallback) {
    if (typeof window === 'undefined') return fallback;
    try {
        return JSON.parse(localStorage.getItem(key)) ?? fallback;
    } catch {
        return fallback;
    }
}

function hasPermission(permission) {
    if (!permission) return true;
    return permissions.value.includes(permission);
}

const items = computed(() => {
    return (navigation[role.value] ?? [])
        .map((item) => {
            if (item.children) {
                const children = item.children.filter((child) => hasPermission(child.permission));
                return children.length ? { ...item, children } : null;
            }
            return hasPermission(item.permission) ? item : null;
        })
        .filter(Boolean);
});

const crumbs = computed(() => activeTrail(role.value, page.url) ?? []);

const initials = computed(() => {
    const name = user.value?.name ?? '';
    return name
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0].toUpperCase())
        .join('');
});

function isActive(item) {
    if (item.href === page.url) return true;
    return item.href !== '/' && page.url.startsWith(item.href + '/');
}

function isParentActive(item) {
    return item.children?.some((child) => isActive(child)) ?? false;
}

function toggleMenu(label) {
    const index = openMenus.value.indexOf(label);
    if (index >= 0) openMenus.value.splice(index, 1);
    else openMenus.value.push(label);
}

function logout() {
    router.post(route('logout'));
}
</script>

<template>
    <div class="min-h-screen bg-surface-muted">
        <div
            v-if="mobileOpen"
            class="fixed inset-0 z-30 bg-black/50 lg:hidden"
            @click="mobileOpen = false"
        ></div>

        <!-- Sidebar -->
        <aside
            :class="[
                collapsed ? 'lg:w-20' : 'lg:w-64',
                mobileOpen ? 'translate-x-0' : '-translate-x-full',
                'lg:translate-x-0',
            ]"
            class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-border bg-surface transition-all duration-200"
        >
            <!-- Brand -->
            <div class="flex h-16 items-center gap-3 border-b border-border px-4">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-sm font-bold text-white">
                    S
                </span>
                <span v-if="!collapsed" class="truncate text-lg font-semibold text-content">SIAKAD</span>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 space-y-1 overflow-y-auto p-3">
                <template v-for="item in items" :key="item.label">
                    <!-- Submenu -->
                    <div v-if="item.children">
                        <button
                            type="button"
                            class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition hover:bg-surface-muted"
                            :class="isParentActive(item) ? 'text-content' : 'text-muted'"
                            :title="collapsed ? item.label : undefined"
                            @click="toggleMenu(item.label)"
                        >
                            <span class="w-5 text-center text-base leading-none">{{ item.icon }}</span>
                            <span v-if="!collapsed" class="flex-1 text-left">{{ item.label }}</span>
                            <svg
                                v-if="!collapsed"
                                class="h-4 w-4 transition-transform"
                                :class="openMenus.includes(item.label) ? 'rotate-180' : ''"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div
                            v-if="!collapsed && openMenus.includes(item.label)"
                            class="mt-1 space-y-1 border-l border-border pl-6"
                        >
                            <Link
                                v-for="child in item.children"
                                :key="child.href"
                                :href="child.href"
                                class="block rounded-md px-3 py-2 text-sm transition hover:bg-surface-muted"
                                :class="isActive(child) ? 'bg-indigo-50 font-medium text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-300' : 'text-muted'"
                            >
                                {{ child.label }}
                            </Link>
                        </div>
                    </div>

                    <!-- Simple link -->
                    <Link
                        v-else
                        :href="item.href"
                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition hover:bg-surface-muted"
                        :class="isActive(item) ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-300' : 'text-muted'"
                        :title="collapsed ? item.label : undefined"
                    >
                        <span class="w-5 text-center text-base leading-none">{{ item.icon }}</span>
                        <span v-if="!collapsed">{{ item.label }}</span>
                    </Link>
                </template>
            </nav>

            <div v-if="!collapsed" class="border-t border-border p-3 text-xs text-muted">
                v0.1 · SIAKAD
            </div>
        </aside>

        <!-- Main -->
        <div :class="collapsed ? 'lg:pl-20' : 'lg:pl-64'" class="flex min-h-screen flex-col transition-all duration-200">
            <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-border bg-surface px-4">
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        class="flex h-9 w-9 items-center justify-center rounded-md text-content transition hover:bg-surface-muted lg:hidden"
                        @click="mobileOpen = !mobileOpen"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <button
                        type="button"
                        class="hidden h-9 w-9 items-center justify-center rounded-md text-content transition hover:bg-surface-muted lg:flex"
                        @click="collapsed = !collapsed"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h10M4 18h16"
                            />
                        </svg>
                    </button>

                    <nav class="hidden items-center gap-1 text-sm text-muted sm:flex">
                        <span>SIAKAD</span>
                        <template v-for="(crumb, i) in crumbs" :key="i">
                            <span class="text-slate-300 dark:text-slate-600">/</span>
                            <span :class="i === crumbs.length - 1 ? 'font-medium text-content' : ''">{{ crumb }}</span>
                        </template>
                    </nav>
                </div>

                <div class="flex items-center gap-1">
                    <!-- Dark mode toggle -->
                    <button
                        type="button"
                        class="flex h-9 w-9 items-center justify-center rounded-md text-content transition hover:bg-surface-muted"
                        :title="isDark ? 'Mode terang' : 'Mode gelap'"
                        @click="toggleDark"
                    >
                        <svg v-if="isDark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
                            />
                        </svg>
                        <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
                            />
                        </svg>
                    </button>

                    <!-- Notification dropdown -->
                    <Dropdown align="right">
                        <template #trigger>
                            <button
                                type="button"
                                class="relative flex h-9 w-9 items-center justify-center rounded-md text-content transition hover:bg-surface-muted"
                                title="Notifikasi"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                                    />
                                </svg>
                            </button>
                        </template>

                        <div class="px-4 py-3 text-center">
                            <p class="text-sm font-medium text-content">Belum ada notifikasi</p>
                            <p class="mt-1 text-xs text-muted">Notifikasi fitur akan muncul di sini.</p>
                        </div>
                    </Dropdown>

                    <!-- Avatar dropdown -->
                    <Dropdown align="right">
                        <template #trigger>
                            <button
                                type="button"
                                class="flex items-center gap-2 rounded-full p-1 transition hover:bg-surface-muted"
                            >
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-xs font-semibold text-white">
                                    {{ initials }}
                                </span>
                                <span class="hidden text-left md:block">
                                    <span class="block text-sm font-medium text-content">{{ user?.name }}</span>
                                    <span class="block text-xs text-muted">{{ role }}</span>
                                </span>
                            </button>
                        </template>

                        <div class="px-4 py-2">
                            <p class="text-sm font-medium text-content">{{ user?.name }}</p>
                            <p class="text-xs text-muted">{{ user?.email }}</p>
                        </div>

                        <div class="my-1 border-t border-border"></div>

                        <button
                            type="button"
                            class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-rose-600 transition hover:bg-rose-50 dark:hover:bg-rose-500/10"
                            @click="logout"
                        >
                            <span>Keluar</span>
                        </button>
                    </Dropdown>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
