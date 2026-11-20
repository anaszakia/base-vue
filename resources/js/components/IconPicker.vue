<script setup lang="ts">
import { ChevronDown, Search, X } from '@lucide/vue';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import DynamicIcon from '@/components/DynamicIcon.vue';
import { AVAILABLE_ICONS } from '@/constants/icons';

const props = defineProps<{
    modelValue: string | null;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const isOpen = ref(false);
const searchQuery = ref('');
const pickerRef = ref<HTMLElement | null>(null);

const filteredIcons = computed(() => {
    const query = searchQuery.value.trim().toLowerCase();
    if (!query) {
        return AVAILABLE_ICONS;
    }
    return AVAILABLE_ICONS.filter((icon) => icon.toLowerCase().includes(query));
});

const selectIcon = (icon: string) => {
    emit('update:modelValue', icon);
    isOpen.value = false;
    searchQuery.value = '';
};

const clearIcon = () => {
    emit('update:modelValue', '');
    isOpen.value = false;
    searchQuery.value = '';
};

const handleClickOutside = (event: MouseEvent) => {
    if (pickerRef.value && !pickerRef.value.contains(event.target as Node)) {
        isOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div ref="pickerRef" class="relative w-full">
        <!-- Trigger Button -->
        <button
            type="button"
            class="flex h-9 w-full items-center justify-between rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors hover:bg-muted/50 focus-visible:outline-hidden focus-visible:ring-1 focus-visible:ring-ring"
            @click="isOpen = !isOpen"
        >
            <div class="flex items-center gap-2 overflow-hidden">
                <template v-if="modelValue">
                    <DynamicIcon :name="modelValue" class="h-4 w-4 shrink-0 text-primary" />
                    <span class="truncate font-medium text-xs">{{ modelValue }}</span>
                </template>
                <template v-else>
                    <span class="text-muted-foreground text-xs">-- Pilih Icon --</span>
                </template>
            </div>

            <div class="flex items-center gap-1 shrink-0">
                <button
                    v-if="modelValue"
                    type="button"
                    class="rounded-sm p-0.5 hover:bg-muted text-muted-foreground hover:text-foreground"
                    title="Hapus icon"
                    @click.stop="clearIcon"
                >
                    <X class="h-3 w-3" />
                </button>
                <ChevronDown class="h-4 w-4 opacity-50 transition-transform duration-200" :class="{ 'rotate-180': isOpen }" />
            </div>
        </button>

        <!-- Dropdown Popover with Search & Icon Grid -->
        <div
            v-if="isOpen"
            class="absolute left-0 top-full z-50 mt-1 w-full sm:w-[320px] rounded-lg border border-border bg-popover p-2 text-popover-foreground shadow-lg animate-in fade-in-0 zoom-in-95"
        >
            <!-- Search Bar -->
            <div class="relative mb-2">
                <Search class="absolute left-2.5 top-2.5 h-3.5 w-3.5 text-muted-foreground" />
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Cari icon (e.g. user, shield, book)..."
                    class="h-8 w-full rounded-md border border-input bg-background pl-8 pr-3 text-xs outline-hidden focus:border-primary focus:ring-1 focus:ring-primary"
                    autofocus
                />
            </div>

            <!-- Icon Grid -->
            <div class="max-h-52 overflow-y-auto pr-1">
                <div v-if="filteredIcons.length > 0" class="grid grid-cols-4 gap-1">
                    <button
                        v-for="iconName in filteredIcons"
                        :key="iconName"
                        type="button"
                        class="flex flex-col items-center justify-center gap-1 rounded-md p-2 text-center transition-colors hover:bg-muted hover:text-foreground"
                        :class="modelValue === iconName ? 'bg-primary/10 text-primary font-semibold ring-1 ring-primary' : 'text-muted-foreground'"
                        :title="iconName"
                        @click="selectIcon(iconName)"
                    >
                        <DynamicIcon :name="iconName" class="h-5 w-5" />
                        <span class="w-full truncate text-[10px] leading-tight">{{ iconName }}</span>
                    </button>
                </div>
                <div v-else class="py-6 text-center text-xs text-muted-foreground">
                    Icon tidak ditemukan.
                </div>
            </div>
        </div>
    </div>
</template>
