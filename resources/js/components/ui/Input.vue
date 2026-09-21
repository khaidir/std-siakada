<script setup>
defineProps({
    label: { type: String, default: null },
    type: { type: String, default: 'text' },
    modelValue: { type: [String, Number], default: '' },
    error: { type: String, default: null },
    placeholder: { type: String, default: '' },
    required: { type: Boolean, default: false },
    autofocus: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <label v-if="label" class="mb-1 block text-sm font-medium text-slate-700">
            {{ label }}
            <span v-if="required" class="text-rose-500">*</span>
        </label>
        <input
            :type="type"
            :value="modelValue"
            :placeholder="placeholder"
            :required="required"
            :autofocus="autofocus"
            :disabled="disabled"
            class="block w-full rounded-md border bg-white px-3 py-2 text-sm text-slate-900 shadow-sm transition focus:outline-none focus:ring-2"
            :class="
                error
                    ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-500'
                    : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-500'
            "
            @input="$emit('update:modelValue', $event.target.value)"
        />
        <p v-if="error" class="mt-1 text-xs text-rose-600">{{ error }}</p>
    </div>
</template>
