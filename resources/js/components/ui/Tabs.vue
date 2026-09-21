<script setup>
defineProps({
    tabs: { type: Array, required: true },
    modelValue: { type: [String, Number], default: null },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div class="border-b border-slate-200">
        <nav class="-mb-px flex gap-4">
            <button
                v-for="tab in tabs"
                :key="tab.value"
                type="button"
                class="border-b-2 px-1 py-2 text-sm font-medium transition"
                :class="
                    modelValue === tab.value
                        ? 'border-indigo-500 text-indigo-600'
                        : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700'
                "
                @click="$emit('update:modelValue', tab.value)"
            >
                {{ tab.label }}
            </button>
        </nav>
    </div>
</template>
