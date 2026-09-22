<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import PageHeader from '@/components/shared/PageHeader.vue';
import Card from '@/components/ui/Card.vue';
import Input from '@/components/ui/Input.vue';
import Select from '@/components/ui/Select.vue';
import Textarea from '@/components/ui/Textarea.vue';
import Button from '@/components/ui/Button.vue';
import Badge from '@/components/ui/Badge.vue';
import Toast from '@/components/ui/Toast.vue';

const props = defineProps({
    profile: { type: Object, required: true },
    genderOptions: { type: Array, default: () => [] },
});

const form = useForm({
    name: props.profile.name ?? '',
    email: props.profile.email ?? '',
    birth_place: props.profile.birth_place ?? '',
    birth_date: props.profile.birth_date ?? '',
    gender: props.profile.gender ?? '',
    address: props.profile.address ?? '',
    phone: props.profile.phone ?? '',
});

function submit() {
    form.put(route('mahasiswa.profil.update'), { preserveScroll: true });
}

function reset() {
    form.reset();
    form.clearErrors();
}
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Pengaturan Profil" />

        <Toast :message="$page.props.flash?.success" type="success" />

        <PageHeader
            title="Pengaturan Profil"
            subtitle="Perbarui biodata Anda. Data akademik hanya bisa diubah oleh admin."
            :breadcrumbs="['Profil', 'Pengaturan']"
        />

        <form @submit.prevent="submit">
            <Card class="mb-5">
                <template #header>
                    <h2 class="font-semibold text-content">Data Akademik</h2>
                </template>

                <!-- Sengaja hanya dibaca: perubahan data akademik bukan wewenang mahasiswa. -->
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <dt class="text-xs text-muted">NIM</dt>
                        <dd class="mt-0.5 text-sm font-medium tabular-nums text-content">{{ profile.nim ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted">Program Studi</dt>
                        <dd class="mt-0.5 text-sm font-medium text-content">{{ profile.study_program ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted">Angkatan</dt>
                        <dd class="mt-0.5 text-sm font-medium text-content">{{ profile.entry_year ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted">Status</dt>
                        <dd class="mt-0.5">
                            <Badge :variant="profile.status === 'aktif' ? 'success' : 'neutral'" dot>
                                {{ profile.status ?? '—' }}
                            </Badge>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted">IPK</dt>
                        <dd class="mt-0.5 text-sm font-medium tabular-nums text-content">{{ profile.gpa ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted">Total SKS</dt>
                        <dd class="mt-0.5 text-sm font-medium tabular-nums text-content">{{ profile.total_sks ?? '—' }}</dd>
                    </div>
                </dl>
            </Card>

            <Card>
                <template #header>
                    <h2 class="font-semibold text-content">Data Pribadi</h2>
                </template>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <Input v-model="form.name" label="Nama Lengkap" required :error="form.errors.name" />
                    <Input v-model="form.email" label="Email" type="email" required :error="form.errors.email" />
                    <Input v-model="form.birth_place" label="Tempat Lahir" :error="form.errors.birth_place" />
                    <Input v-model="form.birth_date" label="Tanggal Lahir" type="date" :error="form.errors.birth_date" />
                    <Select
                        v-model="form.gender"
                        label="Jenis Kelamin"
                        :options="genderOptions"
                        placeholder="Pilih jenis kelamin"
                        :error="form.errors.gender"
                    />
                    <Input
                        v-model="form.phone"
                        label="No. Telepon"
                        type="tel"
                        placeholder="08xxxxxxxxxx"
                        :error="form.errors.phone"
                    />
                </div>

                <Textarea
                    v-model="form.address"
                    label="Alamat"
                    class="mt-4"
                    :rows="3"
                    :maxlength="1000"
                    :error="form.errors.address"
                />

                <template #footer>
                    <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        <Button type="button" variant="secondary" :disabled="form.processing" @click="reset">
                            Batalkan Perubahan
                        </Button>
                        <Button type="submit" :loading="form.processing">
                            {{ form.processing ? 'Menyimpan…' : 'Simpan Perubahan' }}
                        </Button>
                    </div>
                </template>
            </Card>
        </form>
    </AuthenticatedLayout>
</template>
