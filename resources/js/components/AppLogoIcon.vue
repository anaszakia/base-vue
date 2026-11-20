<script setup lang="ts">
import { ShieldCheck } from '@lucide/vue';
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <ShieldCheck :class="className" v-bind="$attrs" />
</template>
