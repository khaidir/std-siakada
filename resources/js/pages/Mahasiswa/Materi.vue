<script setup>
import { computed, ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import Card from '@/components/ui/Card.vue';
import Select from '@/components/ui/Select.vue';
import Badge from '@/components/ui/Badge.vue';
import EmptyState from '@/components/ui/EmptyState.vue';

const props = defineProps({
    offerings: { type: Array, required: true },
    materials: { type: Array, required: true },
    selected_offering_id: { type: [Number, String], default: null },
});

const selectedOffering = ref(props.selected_offering_id ?? (props.offerings[0]?.id ?? ''));

const offeringOptions = computed(() =>
    props.offerings.map((o) => ({ value: o.id, label: o.label })),
);

watch(selectedOffering, (value) => {
    if (value === '' || value === null) return;
    router.get(
        route('mahasiswa.materi.index'),
        { offering: value },
        { preserveState: true, preserveScroll: true, replace: true },
    );
});

function formatDate(value) {
    if (!value) return '—';
    const [y, m, d] = value.split('-');
    return `${d}/${m}/${y}`;
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Materi Kuliah" />

        <PageHeader title="Materi Kuliah" subtitle="Akses materi perkuliahan dari kelas yang Anda ikuti." />

        <Card class="mb-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <Select
                    v-model="selectedOffering"
                    label="Kelas"
                    :options="offeringOptions"
                    placeholder="Pilih kelas…"
                />
            </div>
        </Card>

        <Card>
            <template #header>
                <div>
                    <h2 class="font-semibold text-content">Daftar Materi</h2>
                    <p class="text-sm text-muted">Materi kuliah yang telah diunggah oleh dosen.</p>
                </div>
            </template>

            <div v-if="materials.length === 0" class="py-8">
                <EmptyState title="Belum ada materi" description="Belum ada materi yang diunggah untuk kelas ini." />
            </div>

            <div v-else class="divide-y divide-border">
                <div v-for="material in materials" :key="material.id" class="flex items-start justify-between gap-4 px-3 py-4">
                    <div class="min-w-0 flex-1">
                        <h3 class="font-medium text-content">{{ material.title }}</h3>
                        <p v-if="material.description" class="mt-1 text-sm text-muted line-clamp-2">{{ material.description }}</p>
                        <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-muted">
                            <span>📅 {{ formatDate(material.created_at) }}</span>
                            <span>👤 {{ material.uploaded_by }}</span>
                        </div>
                    </div>
                    <div class="flex shrink-0 items-center gap-2">
                        <a
                            v-if="material.file_url"
                            :href="material.file_url"
                            target="_blank"
                            class="inline-flex items-center gap-1 rounded-md bg-primary-subtle px-3 py-1.5 text-sm font-medium text-primary-strong hover:bg-primary-subtle"
                        >
                            📎 Unduh
                        </a>
                        <Badge v-else variant="slate">Tidak ada file</Badge>
                    </div>
                </div>
            </div>
        </Card>
    </AuthenticatedLayout>
</template>
