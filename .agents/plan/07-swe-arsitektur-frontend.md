# 07 — Senior SWE: Arsitektur Frontend (Vue 3 + Inertia + Tailwind v4)

## 1. Struktur Folder `resources/js/`

```
resources/js/
├── app.js                      # createInertiaApp + ZiggyVue
├── Layouts/
│   └── AuthenticatedLayout.vue
├── components/
│   ├── ui/                     # Button, Card, Input, Select, Table, Modal, Badge, Tabs, Toast, Skeleton, Dropdown
│   └── shared/                 # StatCard, DataTable, PageHeader, ConfirmDialog
└── pages/
    ├── auth/                   # Login.vue
    ├── admin/                  # users, roles, faculties, study-programs, courses, classrooms, offerings, periods, krs-monitoring, skripsi, kp, announcements
    ├── kaprodi/                # courses, offerings, grades, krs-monitoring, skripsi, kp
    ├── dosen/                  # nilai, presensi, materi, tugas, kelas, bimbingan-pa, bimbingan-skripsi, kehadiran
    ├── mahasiswa/              # krs, jadwal, presensi, materi, tugas, nilai, khs, transkrip, ai-advisor, skripsi, kp
    └── pimpinan/               # dashboard, laporan
```

## 2. Konvensi Komponen

- Vue 3 **Composition API** `<script setup>`, tanpa TypeScript.
- `defineProps` / `defineEmits` untuk kontrak props/event.
- Komponen `ui/` generik (slot-based); komponen `shared/` berorientasi domain (tabel data, header halaman).
- Data server masuk lewat props Inertia; interaksi pakai `useForm` / `router.post`.

## 3. Pola Halaman

```vue
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';

defineProps({ offering: Object, students: Array });

const form = useForm({ grades: [] });

function save() {
  form.post(route('dosen.nilai.store'));
}
</script>
```

## 4. Komponen UI yang Dibutuhkan

`Button`, `Card`, `Input`, `Select`, `Textarea`, `Table`, `Modal`, `Badge`, `Tabs`, `Toast`, `Skeleton`, `Dropdown`, `EmptyState`.

## 5. Tailwind v4

- `resources/css/app.css`: `@import "tailwindcss";`.
- `vite.config.js`: plugin `laravel`, `vue`, `tailwindcss`.
- Tema terang/gelap via CSS variables; seluruh label Bahasa Indonesia.
