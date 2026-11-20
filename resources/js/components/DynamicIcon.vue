<script setup lang="ts">
import { computed } from 'vue';
import * as icons from '@lucide/vue';
import { HelpCircle } from '@lucide/vue';

const props = defineProps<{
    name?: string | null;
    class?: string;
}>();

const iconComponent = computed(() => {
    if (!props.name) {
        return null;
    }
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    return (icons as Record<string, any>)[props.name] || HelpCircle;
});
</script>

<template>
    <component :is="iconComponent" v-if="iconComponent" :class="props.class" />
</template>
