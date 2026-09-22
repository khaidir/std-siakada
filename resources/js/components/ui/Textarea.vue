<script setup>
import { computed, useId } from 'vue';

const props = defineProps({
    label: { type: String, default: null },
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: '' },
    rows: { type: Number, default: 3 },
    error: { type: String, default: null },
    hint: { type: String, default: null },
    required: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    maxlength: { type: Number, default: null },
});

defineEmits(['update:modelValue']);

const uid = useId();
const areaId = computed(() => `textarea-${uid}`);
const errorId = computed(() => `textarea-${uid}-error`);
const hintId = computed(() => `textarea-${uid}-hint`);

const describedBy = computed(() => {
    const ids = [];
    if (props.error) ids.push(errorId.value);
    if (props.hint) ids.push(hintId.value);
    return ids.length ? ids.join(' ') : undefined;
});

const used = computed(() => (props.modelValue ?? '').length);
</script>

<template>
    <div>
        <label v-if="label" :for="areaId" class="mb-1 block text-sm font-medium text-content">
            {{ label }}
            <span v-if="required" class="text-danger-strong" aria-hidden="true">*</span>
        </label>

        <textarea
            :id="areaId"
            :value="modelValue"
            :rows="rows"
            :placeholder="placeholder"
            :required="required"
            :disabled="disabled"
            :maxlength="maxlength ?? undefined"
            :aria-invalid="error ? 'true' : undefined"
            :aria-describedby="describedBy"
            class="block w-full rounded-lg bg-surface px-3 py-2 text-sm text-content shadow-sm ring-1 ring-inset transition placeholder:text-muted focus-visible:outline-none focus-visible:ring-2 disabled:cursor-not-allowed disabled:bg-surface-muted disabled:text-muted"
            :class="error ? 'ring-danger focus-visible:ring-danger' : 'ring-border-strong focus-visible:ring-focus'"
            @input="$emit('update:modelValue', $event.target.value)"
        />

        <div class="mt-1 flex items-start justify-between gap-3">
            <p v-if="error" :id="errorId" class="text-xs text-danger-strong">{{ error }}</p>
            <p v-else-if="hint" :id="hintId" class="text-xs text-muted">{{ hint }}</p>
            <span v-else></span>
            <span v-if="maxlength" class="shrink-0 text-xs tabular-nums text-muted">{{ used }}/{{ maxlength }}</span>
        </div>
    </div>
</template>
