<script setup>
defineProps({
    label: { type: String, default: null },
    modelValue: { type: [String, Number], default: '' },
    options: { type: Array, default: () => [] },
    placeholder: { type: String, default: 'Pilih…' },
    error: { type: String, default: null },
    required: { type: Boolean, default: false },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div>
        <label v-if="label" class="mb-1 block text-sm font-medium text-slate-700">
            {{ label }}
            <span v-if="required" class="text-rose-500">*</span>
        </label>
        <select
            :value="modelValue"
            class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            :class="error ? 'border-rose-300' : ''"
            @change="$emit('update:modelValue', $event.target.value)"
        >
            <option value="" disabled>{{ placeholder }}</option>
            <option v-for="opt in options" :key="opt.value" :value="opt.value">
                {{ opt.label }}
            </option>
        </select>
        <p v-if="error" class="mt-1 text-xs text-rose-600">{{ error }}</p>
    </div>
</template>
