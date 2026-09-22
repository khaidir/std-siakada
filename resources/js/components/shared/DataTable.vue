<script setup>
import { computed, useSlots } from 'vue';
import EmptyState from '@/components/ui/EmptyState.vue';
import Skeleton from '@/components/ui/Skeleton.vue';
import Table from '@/components/ui/Table.vue';

const props = defineProps({
    columns: { type: Array, required: true },
    rows: { type: Array, default: () => [] },
    loading: { type: Boolean, default: false },
    emptyText: { type: String, default: 'Tidak ada data.' },
    actionLabel: { type: String, default: 'Aksi' },
    caption: { type: String, default: null },
    striped: { type: Boolean, default: true },
    // Di bawah sm:, tabel diganti kartu bertumpuk agar tidak jebol di layar sempit.
    stackOnMobile: { type: Boolean, default: true },
});

/*
 * `$slots` BUKAN prop — `props.$slots` bernilai undefined dan mengaksesnya melempar
 * TypeError saat computed dievaluasi. Slot harus diambil lewat useSlots().
 */
const slots = useSlots();

const computedColumns = computed(() => {
    const base = props.columns.map((col) => ({ ...col }));

    if (slots.actions) {
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

        <template v-else>
            <!-- Tabel penuh: sm ke atas -->
            <div :class="stackOnMobile ? 'hidden sm:block' : ''">
                <Table :columns="computedColumns" :rows="rows" :empty-text="emptyText" :caption="caption" :striped="striped">
                    <template v-for="col in columns" :key="col.key" #[`cell-${col.key}`]="{ row, value }">
                        <slot :name="`cell-${col.key}`" :row="row" :value="value">
                            {{ value }}
                        </slot>
                    </template>

                    <template v-if="slots.actions" #cell-actions="{ row }">
                        <slot name="actions" :row="row" />
                    </template>
                </Table>
            </div>

            <!-- Kartu bertumpuk: di bawah sm -->
            <div v-if="stackOnMobile" class="space-y-3 sm:hidden">
                <EmptyState v-if="rows.length === 0" :description="emptyText" />

                <div
                    v-for="(row, i) in rows"
                    :key="i"
                    class="rounded-lg border border-border bg-surface p-4 shadow-sm"
                >
                    <dl class="space-y-2">
                        <div v-for="col in columns" :key="col.key" class="flex items-start justify-between gap-3">
                            <dt class="shrink-0 text-xs font-medium text-muted">{{ col.label }}</dt>
                            <dd class="min-w-0 text-right text-sm text-content">
                                <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
                                    {{ row[col.key] }}
                                </slot>
                            </dd>
                        </div>
                    </dl>

                    <div v-if="slots.actions" class="mt-3 flex flex-wrap justify-end gap-2 border-t border-border pt-3">
                        <slot name="actions" :row="row" />
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>
