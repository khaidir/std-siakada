<script setup>
import Badge from '@/components/ui/Badge.vue';
import EmptyState from '@/components/ui/EmptyState.vue';

defineProps({
    // [{ course, room, time, date_label, is_live }]
    classes: { type: Array, default: () => [] },
});
</script>

<template>
    <div>
        <EmptyState
            v-if="classes.length === 0"
            title="Tidak ada kelas"
            description="Belum ada kelas terjadwal dalam waktu dekat."
        />

        <ul v-else class="divide-y divide-border">
            <li v-for="(item, i) in classes" :key="i" class="flex items-start gap-3 py-3 first:pt-0 last:pb-0">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-content">{{ item.course }}</p>
                    <p class="mt-0.5 truncate text-xs text-muted">
                        {{ item.time }}<span v-if="item.room"> · {{ item.room }}</span>
                    </p>
                </div>

                <Badge v-if="item.is_live" variant="danger" dot>Live</Badge>
                <Badge v-else-if="item.date_label" variant="neutral">{{ item.date_label }}</Badge>
            </li>
        </ul>
    </div>
</template>
