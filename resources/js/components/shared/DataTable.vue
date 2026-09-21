<script setup>
import { computed } from 'vue';
import Skeleton from '@/components/ui/Skeleton.vue';
import Table from '@/components/ui/Table.vue';

const props = defineProps({
    columns: { type: Array, required: true },
    rows: { type: Array, default: () => [] },
    loading: { type: Boolean, default: false },
    emptyText: { type: String, default: 'Tidak ada data.' },
    actionLabel: { type: String, default: 'Aksi' },
});

const computedColumns = computed(() => {
    const base = props.columns.map((col) => ({ ...col }));

    if (props.$slots.actions) {
        base.push({ key: 'actions', label: props.actionLabel });
    }

    return base;
});
</script>

<template>
    <div>
        <div v-if="loading" class="space-y-3">
            <Skeleton variant="card" class="h-10 w-full" />
            <Skeleton variant="card" class="h-10 w-full" />
            <Skeleton variant="card" class="h-10 w-full" />
        </div>

        <Table v-else :columns="computedColumns" :rows="rows" :empty-text="emptyText">
            <template v-for="col in columns" :key="col.key" #[`cell-${col.key}`]="{ row, value }">
                <slot :name="`cell-${col.key}`" :row="row" :value="value">
                    {{ value }}
                </slot>
            </template>

            <template v-if="$slots.actions" #cell-actions="{ row }">
                <slot name="actions" :row="row" />
            </template>
        </Table>
    </div>
</template>
