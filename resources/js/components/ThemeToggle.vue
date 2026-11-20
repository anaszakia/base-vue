<script setup lang="ts">
import { Moon, Sun } from '@lucide/vue';
import { useAppearance } from '@/composables/useAppearance';
import { Button } from '@/components/ui/button';

const { resolvedAppearance, updateAppearance } = useAppearance();

const toggleTheme = () => {
    updateAppearance(resolvedAppearance.value === 'dark' ? 'light' : 'dark');
};
</script>

<template>
    <Button
        variant="ghost"
        size="sm"
        class="h-9 w-9 rounded-md p-0 text-muted-foreground hover:text-foreground"
        :title="resolvedAppearance === 'dark' ? 'Beralih ke mode terang' : 'Beralih ke mode gelap'"
        @click="toggleTheme"
    >
        <Sun v-if="resolvedAppearance === 'dark'" class="h-4 w-4 transition-transform hover:rotate-45" />
        <Moon v-else class="h-4 w-4 transition-transform hover:-rotate-12" />
        <span class="sr-only">Toggle theme</span>
    </Button>
</template>
