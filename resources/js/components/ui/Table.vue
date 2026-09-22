<script setup>
defineProps({
    columns: { type: Array, required: true },
    rows: { type: Array, default: () => [] },
    emptyText: { type: String, default: 'Tidak ada data.' },
    caption: { type: String, default: null },
    // Baris bergaris seperti pada spesifikasi desain.
    striped: { type: Boolean, default: false },
});

const aligns = {
    left: 'text-left',
    center: 'text-center',
    right: 'text-right',
};
</script>

<template>
    <div class="relative overflow-x-auto rounded-lg border border-border bg-surface">
        <table class="min-w-full text-sm">
            <caption v-if="caption" class="sr-only">{{ caption }}</caption>

            <thead class="bg-surface-muted">
                <tr>
                    <th
                        v-for="col in columns"
                        :key="col.key"
                        scope="col"
                        class="sticky top-0 z-10 whitespace-nowrap bg-surface-muted px-4 py-3 font-semibold text-content"
                        :class="aligns[col.align] ?? 'text-left'"
                        :style="col.width ? { width: col.width } : undefined"
                    >
                        {{ col.label }}
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-border">
                <tr
                    v-for="(row, i) in rows"
                    :key="i"
                    class="transition hover:bg-surface-muted"
                    :class="striped && i % 2 === 1 ? 'bg-surface-muted/50' : ''"
                >
                    <td
                        v-for="col in columns"
                        :key="col.key"
                        class="px-4 py-3 text-content"
                        :class="aligns[col.align] ?? 'text-left'"
                    >
                        <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
                            {{ row[col.key] }}
                        </slot>
                    </td>
                </tr>

                <tr v-if="rows.length === 0">
                    <td :colspan="columns.length" class="px-4 py-8 text-center text-muted">
                        {{ emptyText }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
