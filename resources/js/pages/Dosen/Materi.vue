<script setup>
import { computed, ref, watch } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import Card from '@/components/ui/Card.vue';
import Button from '@/components/ui/Button.vue';
import Select from '@/components/ui/Select.vue';
import Input from '@/components/ui/Input.vue';
import Textarea from '@/components/ui/Textarea.vue';
import Badge from '@/components/ui/Badge.vue';
import Modal from '@/components/ui/Modal.vue';
import ConfirmDialog from '@/components/shared/ConfirmDialog.vue';
import EmptyState from '@/components/ui/EmptyState.vue';

const props = defineProps({
    offerings: { type: Array, required: true },
    materials: { type: Array, required: true },
    selected_offering_id: { type: [Number, String], default: null },
});

const page = usePage();

const selectedOffering = ref(props.selected_offering_id ?? (props.offerings[0]?.id ?? ''));
const showModal = ref(false);
const editMode = ref(false);
const editingId = ref(null);
const deleteConfirmOpen = ref(false);
const deletingId = ref(null);

const offeringOptions = computed(() =>
    props.offerings.map((o) => ({ value: o.id, label: o.label })),
);

watch(selectedOffering, (value) => {
    if (value === '' || value === null) return;
    router.get(
        route('dosen.materi.index'),
        { offering: value },
        { preserveState: true, preserveScroll: true, replace: true },
    );
});

const form = useForm({
    course_offering_id: null,
    title: '',
    description: '',
    file: null,
});

function openCreate() {
    editMode.value = false;
    editingId.value = null;
    form.course_offering_id = Number(selectedOffering.value);
    form.title = '';
    form.description = '';
    form.file = null;
    showModal.value = true;
}

function openEdit(material) {
    editMode.value = true;
    editingId.value = material.id;
    form.course_offering_id = Number(selectedOffering.value);
    form.title = material.title;
    form.description = material.description ?? '';
    form.file = null;
    showModal.value = true;
}

function confirmDelete(id) {
    deletingId.value = id;
    deleteConfirmOpen.value = true;
}

function save() {
    if (editMode.value) {
        form.post(route('dosen.materi.update', { id: editingId.value }), {
            _method: 'PUT',
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
            },
        });
    } else {
        form.post(route('dosen.materi.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
            },
        });
    }
}

function destroy() {
    router.delete(route('dosen.materi.destroy', { id: deletingId.value }), {
        preserveScroll: true,
        onSuccess: () => {
            deleteConfirmOpen.value = false;
            deletingId.value = null;
        },
    });
}

function handleFileChange(event) {
    form.file = event.target.files[0] ?? null;
}

function formatDate(value) {
    if (!value) return '—';
    const [y, m, d] = value.split('-');
    return `${d}/${m}/${y}`;
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Materi Kuliah" />

        <PageHeader title="Materi Kuliah" subtitle="Kelola materi perkuliahan untuk setiap kelas." />

        <div v-if="page.props.flash?.success" class="mb-4 rounded-md bg-emerald-50 p-3 text-sm text-emerald-700">
            {{ page.props.flash.success }}
        </div>

        <div v-if="form.hasErrors" class="mb-4 rounded-md bg-rose-50 p-3 text-sm text-rose-700">
            <p v-for="(err, key) in form.errors" :key="key">{{ err }}</p>
        </div>

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
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-semibold text-content">Daftar Materi</h2>
                        <p class="text-sm text-muted">Materi kuliah yang telah diunggah untuk kelas ini.</p>
                    </div>
                    <Button variant="primary" :disabled="!selectedOffering" @click="openCreate">
                        ➕ Tambah Materi
                    </Button>
                </div>
            </template>

            <div v-if="materials.length === 0" class="py-8">
                <EmptyState title="Belum ada materi" description="Tambahkan materi pertama untuk kelas ini." />
            </div>

            <div v-else class="divide-y divide-border">
                <div v-for="material in materials" :key="material.id" class="flex items-start justify-between gap-4 px-3 py-4">
                    <div class="min-w-0 flex-1">
                        <h3 class="font-medium text-content">{{ material.title }}</h3>
                        <p v-if="material.description" class="mt-1 text-sm text-muted line-clamp-2">{{ material.description }}</p>
                        <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-muted">
                            <span>📅 {{ formatDate(material.created_at) }}</span>
                            <span>👤 {{ material.uploaded_by }}</span>
                            <a
                                v-if="material.file_url"
                                :href="material.file_url"
                                target="_blank"
                                class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800"
                            >
                                📎 Lihat File
                            </a>
                        </div>
                    </div>
                    <div class="flex shrink-0 items-center gap-2">
                        <Button variant="ghost" size="sm" @click="openEdit(material)">
                            ✏️
                        </Button>
                        <Button variant="ghost" size="sm" @click="confirmDelete(material.id)">
                            🗑️
                        </Button>
                    </div>
                </div>
            </div>
        </Card>

        <!-- Modal Form -->
        <Modal :open="showModal" :title="editMode ? 'Edit Materi' : 'Tambah Materi'" @close="showModal = false">
            <form @submit.prevent="save" class="space-y-4">
                <Input
                    v-model="form.title"
                    label="Judul Materi"
                    placeholder="Masukkan judul materi"
                    required
                    :error="form.errors.title"
                />

                <Textarea
                    v-model="form.description"
                    label="Deskripsi"
                    placeholder="Deskripsi materi (opsional)"
                    :error="form.errors.description"
                />

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">
                        File (opsional)
                        <span class="text-xs text-muted"> — PDF, DOC, PPT, ZIP, gambar, video (maks 20MB)</span>
                    </label>
                    <input
                        type="file"
                        accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip,.rar,.jpg,.jpeg,.png,.mp4,.mp3"
                        class="block w-full text-sm text-slate-500 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100"
                        @change="handleFileChange"
                    />
                    <p v-if="form.errors.file" class="mt-1 text-xs text-rose-600">{{ form.errors.file }}</p>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <Button variant="secondary" type="button" @click="showModal = false">
                        Batal
                    </Button>
                    <Button variant="primary" type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Menyimpan…' : 'Simpan' }}
                    </Button>
                </div>
            </form>
        </Modal>

        <!-- Confirm Delete -->
        <ConfirmDialog
            :show="deleteConfirmOpen"
            title="Hapus Materi"
            message="Apakah Anda yakin ingin menghapus materi ini? File yang terupload juga akan dihapus."
            confirm-label="Hapus"
            :danger="true"
            :processing="form.processing"
            @close="deleteConfirmOpen = false"
            @confirm="destroy"
        />
    </AuthenticatedLayout>
</template>
