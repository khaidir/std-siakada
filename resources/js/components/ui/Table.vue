<script setup>
defineProps({
    columns: { type: Array, required: true },
    rows: { type: Array, default: () => [] },
    emptyText: { type: String, default: 'Tidak ada data.' },
});
</script>

<template>
    <div class="overflow-x-auto rounded-lg border border-border bg-surface">
        <table class="min-w-full divide-y divide-border text-sm">
            <thead class="bg-surface-muted">
                <tr>
                    <th
                        v-for="col in columns"
                        :key="col.key"
                        class="px-4 py-3 text-left font-semibold text-content"
                    >
                        {{ col.label }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                <tr v-for="(row, i) in rows" :key="i" class="hover:bg-surface-muted">
                    <td v-for="col in columns" :key="col.key" class="px-4 py-3 text-content">
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
