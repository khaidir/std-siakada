<script setup>
import { computed, useId } from 'vue';

const props = defineProps({
    label: { type: String, default: null },
    type: { type: String, default: 'text' },
    modelValue: { type: [String, Number], default: '' },
    error: { type: String, default: null },
    hint: { type: String, default: null },
    placeholder: { type: String, default: '' },
    required: { type: Boolean, default: false },
    autofocus: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    readonly: { type: Boolean, default: false },
});

defineEmits(['update:modelValue']);

const uid = useId();
const inputId = computed(() => `input-${uid}`);
const errorId = computed(() => `input-${uid}-error`);
const hintId = computed(() => `input-${uid}-hint`);

const describedBy = computed(() => {
    const ids = [];
    if (props.error) ids.push(errorId.value);
    if (props.hint) ids.push(hintId.value);
    return ids.length ? ids.join(' ') : undefined;
});
</script>

<template>
    <div>
        <label v-if="label" :for="inputId" class="mb-1 block text-sm font-medium text-content">
            {{ label }}
            <span v-if="required" class="text-danger-strong" aria-hidden="true">*</span>
        </label>

        <div class="relative">
            <span v-if="$slots.prefix" class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-muted">
                <slot name="prefix" />
            </span>

            <input
                :id="inputId"
                :type="type"
                :value="modelValue"
                :placeholder="placeholder"
                :required="required"
                :autofocus="autofocus"
                :disabled="disabled"
                :readonly="readonly"
                :aria-invalid="error ? 'true' : undefined"
                :aria-describedby="describedBy"
                class="block min-h-10 w-full rounded-lg bg-surface px-3 py-2 text-sm text-content shadow-sm ring-1 ring-inset transition placeholder:text-muted focus-visible:outline-none focus-visible:ring-2 disabled:cursor-not-allowed disabled:bg-surface-muted disabled:text-muted read-only:bg-surface-muted"
                :class="[
                    error ? 'ring-danger focus-visible:ring-danger' : 'ring-border-strong focus-visible:ring-focus',
                    $slots.prefix ? 'pl-9' : '',
                    $slots.suffix ? 'pr-9' : '',
                ]"
                @input="$emit('update:modelValue', $event.target.value)"
            />

            <span v-if="$slots.suffix" class="absolute inset-y-0 right-0 flex items-center pr-3 text-muted">
                <slot name="suffix" />
            </span>
        </div>

        <p v-if="hint && !error" :id="hintId" class="mt-1 text-xs text-muted">{{ hint }}</p>
        <p v-if="error" :id="errorId" class="mt-1 text-xs text-danger-strong">{{ error }}</p>
    </div>
</template>
