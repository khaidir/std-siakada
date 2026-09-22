<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
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
const query = ref('');
const hamburger = ref(null);
const sidebar = ref(null);
const isDesktop = ref(true);
let mediaQuery = null;

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

/*
 * Pencarian memakai config navigasi yang sudah ada, jadi kotak ini benar-benar
 * mengantar ke halaman alih-alih sekadar hiasan.
 */
const searchable = computed(() =>
    items.value.flatMap((item) =>
        item.children
            ? item.children.map((child) => ({ label: child.label, parent: item.label, href: child.href }))
            : [{ label: item.label, parent: null, href: item.href }],
    ),
);

const results = computed(() => {
    const q = query.value.trim().toLowerCase();
    if (!q) return [];
    return searchable.value.filter((entry) => entry.label.toLowerCase().includes(q)).slice(0, 6);
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

function closeMobile() {
    if (!mobileOpen.value) return;
    mobileOpen.value = false;
    nextTick(() => hamburger.value?.focus());
}

function goTo(href) {
    query.value = '';
    router.visit(href);
}

function onKeydown(event) {
    if (event.key === 'Escape' && mobileOpen.value) closeMobile();
}

// Kunci scroll badan halaman selama drawer mobile terbuka.
watch(mobileOpen, (open) => {
    document.body.style.overflow = open ? 'hidden' : '';
    if (open) nextTick(() => sidebar.value?.querySelector('a, button')?.focus());
});

function syncViewport(event) {
    isDesktop.value = event.matches;
    // Drawer mobile tidak boleh tertinggal terbuka saat layar melebar.
    if (event.matches) mobileOpen.value = false;
}

onMounted(() => {
    document.addEventListener('keydown', onKeydown);
    mediaQuery = window.matchMedia('(min-width: 1024px)');
    isDesktop.value = mediaQuery.matches;
    mediaQuery.addEventListener('change', syncViewport);
});
onBeforeUnmount(() => {
    document.removeEventListener('keydown', onKeydown);
    mediaQuery?.removeEventListener('change', syncViewport);
    document.body.style.overflow = '';
});

function logout() {
    router.post(route('logout'));
}
</script>

<template>
    <div class="min-h-screen bg-surface-muted">
        <div v-if="mobileOpen" class="fixed inset-0 z-30 bg-black/50 lg:hidden" @click="closeMobile"></div>

        <!-- Sidebar -->
        <aside
            ref="sidebar"
            :inert="!isDesktop && !mobileOpen ? true : undefined"
            :class="[
                collapsed ? 'lg:w-20' : 'lg:w-64',
                mobileOpen ? 'translate-x-0' : '-translate-x-full',
                'lg:translate-x-0',
            ]"
            class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-border bg-surface transition-all duration-200"
        >
            <!-- Brand -->
            <div class="flex h-16 items-center gap-3 border-b border-border px-4">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary text-sm font-bold text-primary-fg">
                    S
                </span>
                <span v-if="!collapsed" class="min-w-0">
                    <span class="block truncate text-lg font-semibold leading-tight text-content">SIAKAD</span>
                    <span class="block truncate text-xs capitalize text-muted">{{ role }}</span>
                </span>
            </div>

            <!-- Navigasi -->
            <nav class="flex-1 space-y-1 overflow-y-auto p-3">
                <template v-for="item in items" :key="item.label">
                    <div v-if="item.children">
                        <button
                            type="button"
                            class="group relative flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition hover:bg-surface-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus"
                            :class="isParentActive(item) ? 'text-content' : 'text-muted'"
                            :aria-expanded="openMenus.includes(item.label)"
                            @click="toggleMenu(item.label)"
                        >
                            <span class="w-5 shrink-0 text-center text-base leading-none">{{ item.icon }}</span>
                            <span v-if="!collapsed" class="flex-1 text-left">{{ item.label }}</span>
                            <svg
                                v-if="!collapsed"
                                class="h-4 w-4 shrink-0 transition-transform"
                                :class="openMenus.includes(item.label) ? 'rotate-180' : ''"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                aria-hidden="true"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>

                            <span
                                v-if="collapsed"
                                class="pointer-events-none absolute left-full z-50 ml-2 hidden whitespace-nowrap rounded-md bg-content px-2 py-1 text-xs text-surface shadow-lg group-hover:block group-focus-visible:block"
                            >
                                {{ item.label }}
                            </span>
                        </button>

                        <div
                            v-if="!collapsed && openMenus.includes(item.label)"
                            class="mt-1 space-y-1 border-l border-border pl-6"
                        >
                            <Link
                                v-for="child in item.children"
                                :key="child.href"
                                :href="child.href"
                                class="block rounded-lg px-3 py-2 text-sm transition hover:bg-surface-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus"
                                :class="isActive(child) ? 'bg-primary-subtle font-medium text-primary-strong' : 'text-muted'"
                                :aria-current="isActive(child) ? 'page' : undefined"
                                @click="closeMobile"
                            >
                                {{ child.label }}
                            </Link>
                        </div>
                    </div>

                    <Link
                        v-else
                        :href="item.href"
                        class="group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition hover:bg-surface-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus"
                        :class="isActive(item) ? 'bg-primary-subtle text-primary-strong' : 'text-muted'"
                        :aria-current="isActive(item) ? 'page' : undefined"
                        @click="closeMobile"
                    >
                        <span class="w-5 shrink-0 text-center text-base leading-none">{{ item.icon }}</span>
                        <span v-if="!collapsed">{{ item.label }}</span>

                        <span
                            v-if="collapsed"
                            class="pointer-events-none absolute left-full z-50 ml-2 hidden whitespace-nowrap rounded-md bg-content px-2 py-1 text-xs text-surface shadow-lg group-hover:block group-focus-visible:block"
                        >
                            {{ item.label }}
                        </span>
                    </Link>
                </template>
            </nav>

            <div v-if="!collapsed" class="border-t border-border p-3 text-xs text-muted">v0.1 · SIAKAD</div>
        </aside>

        <!-- Utama -->
        <div :class="collapsed ? 'lg:pl-20' : 'lg:pl-64'" class="flex min-h-screen flex-col transition-all duration-200">
            <header class="sticky top-0 z-20 flex h-16 items-center gap-2 border-b border-border bg-surface px-3 sm:px-4">
                <button
                    ref="hamburger"
                    type="button"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-content transition hover:bg-surface-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus lg:hidden"
                    :aria-expanded="mobileOpen"
                    aria-label="Buka menu navigasi"
                    @click="mobileOpen = !mobileOpen"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <button
                    type="button"
                    class="hidden h-10 w-10 shrink-0 items-center justify-center rounded-lg text-content transition hover:bg-surface-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus lg:flex"
                    :aria-label="collapsed ? 'Lebarkan sidebar' : 'Ciutkan sidebar'"
                    @click="collapsed = !collapsed"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h16" />
                    </svg>
                </button>

                <nav class="hidden min-w-0 items-center gap-1 text-sm text-muted md:flex" aria-label="Breadcrumb">
                    <span>SIAKAD</span>
                    <template v-for="(crumb, i) in crumbs" :key="i">
                        <span class="text-border-strong" aria-hidden="true">/</span>
                        <span
                            class="truncate"
                            :class="i === crumbs.length - 1 ? 'font-medium text-content' : ''"
                            :aria-current="i === crumbs.length - 1 ? 'page' : undefined"
                        >
                            {{ crumb }}
                        </span>
                    </template>
                </nav>

                <!-- Pencarian menu -->
                <div class="relative mx-auto w-full max-w-sm">
                    <label for="menu-search" class="sr-only">Cari menu</label>
                    <svg
                        class="pointer-events-none absolute inset-y-0 left-3 my-auto h-4 w-4 text-muted"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        aria-hidden="true"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                    </svg>
                    <input
                        id="menu-search"
                        v-model="query"
                        type="search"
                        placeholder="Cari menu…"
                        class="block h-10 w-full rounded-lg bg-surface-muted py-2 pl-9 pr-3 text-sm text-content ring-1 ring-inset ring-border transition placeholder:text-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus"
                        @keydown.esc="query = ''"
                    />

                    <ul
                        v-if="results.length"
                        class="absolute inset-x-0 top-full z-40 mt-1 overflow-hidden rounded-lg border border-border bg-surface py-1 shadow-lg"
                    >
                        <li v-for="entry in results" :key="entry.href">
                            <button
                                type="button"
                                class="flex w-full items-baseline gap-2 px-3 py-2 text-left text-sm text-content transition hover:bg-surface-muted focus-visible:outline-none focus-visible:bg-surface-muted"
                                @click="goTo(entry.href)"
                            >
                                <span class="truncate">{{ entry.label }}</span>
                                <span v-if="entry.parent" class="ml-auto shrink-0 text-xs text-muted">{{ entry.parent }}</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="flex shrink-0 items-center gap-1">
                    <button
                        type="button"
                        class="flex h-10 w-10 items-center justify-center rounded-lg text-content transition hover:bg-surface-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus"
                        :aria-label="isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap'"
                        @click="toggleDark"
                    >
                        <svg v-if="isDark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
                            />
                        </svg>
                        <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
                            />
                        </svg>
                    </button>

                    <Dropdown align="right">
                        <template #trigger>
                            <button
                                type="button"
                                class="hidden h-10 w-10 items-center justify-center rounded-lg text-content transition hover:bg-surface-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus sm:flex"
                                aria-label="Notifikasi"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
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

                    <Dropdown align="right">
                        <template #trigger>
                            <button
                                type="button"
                                class="flex items-center gap-2 rounded-full p-1 transition hover:bg-surface-muted focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus"
                                aria-label="Menu profil"
                            >
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-xs font-semibold text-primary-fg">
                                    {{ initials }}
                                </span>
                                <span class="hidden text-left md:block">
                                    <span class="block text-sm font-medium text-content">{{ user?.name }}</span>
                                    <span class="block text-xs capitalize text-muted">{{ role }}</span>
                                </span>
                            </button>
                        </template>

                        <div class="px-4 py-2">
                            <p class="truncate text-sm font-medium text-content">{{ user?.name }}</p>
                            <p class="truncate text-xs text-muted">{{ user?.email }}</p>
                        </div>

                        <div class="my-1 border-t border-border"></div>

                        <slot name="profile-menu" />

                        <button
                            type="button"
                            class="flex w-full items-center gap-2 px-4 py-2.5 text-left text-sm text-danger-strong transition hover:bg-danger-subtle focus-visible:outline-none focus-visible:bg-danger-subtle"
                            @click="logout"
                        >
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>Keluar</span>
                        </button>
                    </Dropdown>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6">
                <!-- Panel kanan opsional: grid 8/4 di xl, menumpuk di bawahnya. -->
                <div v-if="$slots.aside" class="grid grid-cols-1 gap-5 xl:grid-cols-12">
                    <div class="min-w-0 xl:col-span-8">
                        <slot />
                    </div>
                    <aside class="min-w-0 space-y-5 xl:col-span-4">
                        <slot name="aside" />
                    </aside>
                </div>

                <slot v-else />
            </main>
        </div>
    </div>
</template>
