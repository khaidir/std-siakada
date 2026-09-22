<script setup>
import { ref, computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/Button.vue';
import Badge from '@/components/ui/Badge.vue';
import EmptyState from '@/components/ui/EmptyState.vue';

const props = defineProps({
    roles: { type: Array, default: () => [] },
    permissions: { type: Array, default: () => [] },
});

const page = usePage();

const editingRoleId = ref(null);
const selectedPermissions = ref([]);

const permissionGroups = computed(() => {
    const groups = {};
    for (const perm of props.permissions) {
        const group = perm.split('.')[0] || 'other';
        if (!groups[group]) groups[group] = [];
        groups[group].push(perm);
    }
    return groups;
});

function startEdit(role) {
    editingRoleId.value = role.id;
    selectedPermissions.value = [...role.permissions];
}

function cancelEdit() {
    editingRoleId.value = null;
    selectedPermissions.value = [];
}

function togglePermission(perm) {
    const idx = selectedPermissions.value.indexOf(perm);
    if (idx === -1) {
        selectedPermissions.value.push(perm);
    } else {
        selectedPermissions.value.splice(idx, 1);
    }
}

function isSelected(perm) {
    return selectedPermissions.value.includes(perm);
}

function savePermissions(roleId) {
    router.post(route('admin.roles.update-permissions'), {
        role_id: roleId,
        permissions: selectedPermissions.value,
    }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => cancelEdit(),
    });
}

function roleBadgeVariant(role) {
    const map = {
        'super-admin': 'rose',
        'kaprodi': 'blue',
        'dosen': 'emerald',
        'mahasiswa': 'amber',
        'pimpinan': 'purple',
    };
    return map[role] ?? 'slate';
}

function groupLabel(group) {
    const map = {
        'dashboard': 'Dashboard',
        'master': 'Master Data',
        'users': 'Pengguna',
        'roles': 'Roles',
        'courses': 'Mata Kuliah',
        'offerings': 'Kelas & Jadwal',
        'offering': 'Kelas & Jadwal',
        'period': 'Periode Akademik',
        'krs': 'KRS',
        'grade': 'Nilai',
        'grades': 'Nilai',
        'attendance': 'Presensi',
        'material': 'Materi',
        'assignment': 'Tugas',
        'submission': 'Submission',
        'thesis': 'Skripsi',
        'internship': 'KP',
        'lecturer': 'Kehadiran Dosen',
        'lecturer-attendance': 'Kehadiran Dosen',
        'advisor': 'Pembimbing',
        'ai': 'AI Advisor',
        'ai-advisor': 'AI Advisor',
        'report': 'Laporan',
        'announcement': 'Pengumuman',
    };
    return map[group] ?? group;
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Roles & Permissions" />

        <PageHeader title="Roles & Permissions" subtitle="Kelola mapping role dan permission akses." />

        <div v-if="page.props.flash?.success" class="mb-4 rounded-md bg-success-subtle p-3 text-sm text-success-strong">
            {{ page.props.flash.success }}
        </div>

        <div v-if="roles.length === 0" class="py-8">
            <EmptyState title="Belum ada role" description="Belum ada role yang terdefinisi." />
        </div>

        <div v-else class="space-y-6">
            <Card v-for="role in roles" :key="role.id">
                <template #header>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <Badge :variant="roleBadgeVariant(role.name)" size="lg">
                                {{ role.name }}
                            </Badge>
                            <span class="text-sm text-muted">{{ role.permissions.length }} permission(s)</span>
                        </div>
                        <div>
                            <template v-if="editingRoleId === role.id">
                                <Button variant="primary" size="xs" class="mr-2" @click="savePermissions(role.id)">
                                    Simpan
                                </Button>
                                <Button variant="secondary" size="xs" @click="cancelEdit">
                                    Batal
                                </Button>
                            </template>
                            <Button v-else variant="secondary" size="xs" @click="startEdit(role)">
                                Edit Permissions
                            </Button>
                        </div>
                    </div>
                </template>

                <div v-if="editingRoleId === role.id">
                    <div v-for="(perms, group) in permissionGroups" :key="group" class="mb-4">
                        <h4 class="mb-2 text-sm font-medium text-content capitalize">
                            {{ groupLabel(group) }}
                        </h4>
                        <div class="flex flex-wrap gap-2">
                            <label
                                v-for="perm in perms"
                                :key="perm"
                                class="inline-flex cursor-pointer items-center gap-1.5 rounded-md border px-3 py-1.5 text-xs transition-colors"
                                :class="isSelected(perm)
                                    ? 'border-info bg-info-subtle text-info-strong'
                                    : 'border-border bg-white text-muted hover:border-border-strong'"
                                @click="togglePermission(perm)"
                            >
                                <input
                                    type="checkbox"
                                    :checked="isSelected(perm)"
                                    class="sr-only"
                                />
                                {{ perm }}
                            </label>
                        </div>
                    </div>
                </div>

                <div v-else>
                    <div class="flex flex-wrap gap-1.5">
                        <Badge
                            v-for="perm in role.permissions"
                            :key="perm"
                            variant="slate"
                            size="sm"
                        >
                            {{ perm }}
                        </Badge>
                    </div>
                </div>
            </Card>
        </div>
    </AuthenticatedLayout>
</template>
