<script setup>
import { computed, useId } from 'vue';

const props = defineProps({
    label: { type: String, default: null },
    modelValue: { type: [String, Number], default: '' },
    options: { type: Array, default: () => [] },
    placeholder: { type: String, default: 'Pilih…' },
    error: { type: String, default: null },
    hint: { type: String, default: null },
    required: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
});

defineEmits(['update:modelValue']);

const uid = useId();
const selectId = computed(() => `select-${uid}`);
const errorId = computed(() => `select-${uid}-error`);
const hintId = computed(() => `select-${uid}-hint`);

const describedBy = computed(() => {
    const ids = [];
    if (props.error) ids.push(errorId.value);
    if (props.hint) ids.push(hintId.value);
    return ids.length ? ids.join(' ') : undefined;
});
</script>

<template>
    <div>
        <label v-if="label" :for="selectId" class="mb-1 block text-sm font-medium text-content">
            {{ label }}
            <span v-if="required" class="text-danger-strong" aria-hidden="true">*</span>
        </label>

        <div class="relative">
            <select
                :id="selectId"
                :value="modelValue"
                :required="required"
                :disabled="disabled"
                :aria-invalid="error ? 'true' : undefined"
                :aria-describedby="describedBy"
                class="block min-h-10 w-full appearance-none rounded-lg bg-surface py-2 pl-3 pr-9 text-sm text-content shadow-sm ring-1 ring-inset transition focus-visible:outline-none focus-visible:ring-2 disabled:cursor-not-allowed disabled:bg-surface-muted disabled:text-muted"
                :class="error ? 'ring-danger focus-visible:ring-danger' : 'ring-border-strong focus-visible:ring-focus'"
                @change="$emit('update:modelValue', $event.target.value)"
            >
                <option value="" disabled>{{ placeholder }}</option>
                <option v-for="opt in options" :key="opt.value" :value="opt.value">
                    {{ opt.label }}
                </option>
            </select>

            <svg
                class="pointer-events-none absolute inset-y-0 right-3 my-auto h-4 w-4 text-muted"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                aria-hidden="true"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </div>

        <p v-if="hint && !error" :id="hintId" class="mt-1 text-xs text-muted">{{ hint }}</p>
        <p v-if="error" :id="errorId" class="mt-1 text-xs text-danger-strong">{{ error }}</p>
    </div>
</template>
