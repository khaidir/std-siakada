<script setup>
import { ref } from 'vue';

const props = defineProps({
    tabs: { type: Array, required: true },
    modelValue: { type: [String, Number], default: null },
});

const emit = defineEmits(['update:modelValue']);

const buttons = ref([]);

function move(index, step) {
    const next = (index + step + props.tabs.length) % props.tabs.length;
    emit('update:modelValue', props.tabs[next].value);
    buttons.value[next]?.focus();
}
</script>

<template>
    <div class="border-b border-border">
        <!-- snap-x agar tab tidak terpotong setengah saat digeser di layar sempit -->
        <nav
            role="tablist"
            class="-mb-px flex snap-x gap-1 overflow-x-auto sm:gap-4"
            aria-label="Tab"
        >
            <button
                v-for="(tab, i) in tabs"
                :key="tab.value"
                :ref="(el) => (buttons[i] = el)"
                type="button"
                role="tab"
                :aria-selected="modelValue === tab.value"
                :tabindex="modelValue === tab.value ? 0 : -1"
                class="shrink-0 snap-start whitespace-nowrap border-b-2 px-3 py-2.5 text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-focus focus-visible:ring-offset-2 focus-visible:ring-offset-surface"
                :class="
                    modelValue === tab.value
                        ? 'border-primary text-primary'
                        : 'border-transparent text-muted hover:border-border-strong hover:text-content'
                "
                @click="emit('update:modelValue', tab.value)"
                @keydown.right.prevent="move(i, 1)"
                @keydown.left.prevent="move(i, -1)"
            >
                {{ tab.label }}
            </button>
        </nav>
    </div>
</template>
