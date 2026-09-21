<script setup>
defineProps({
    label: { type: String, default: null },
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: '' },
    rows: { type: Number, default: 3 },
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
        <textarea
            :value="modelValue"
            :rows="rows"
            :placeholder="placeholder"
            :required="required"
            class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            @input="$emit('update:modelValue', $event.target.value)"
        />
        <p v-if="error" class="mt-1 text-xs text-rose-600">{{ error }}</p>
    </div>
</template>
