<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import type { PaginatedCollection } from '@/types/pagination';

type PaginationMetadata = Pick<
    PaginatedCollection<unknown>,
    'current_page' | 'last_page' | 'prev_page_url' | 'next_page_url'
>;

const props = defineProps<{
    pagination: PaginationMetadata;
}>();

const visitPage = (url: string | null): void => {
    if (url) {
        router.get(url);
    }
};
</script>

<template>
    <nav
        v-if="pagination.last_page > 1"
        aria-label="Navigasi pagination"
        class="flex flex-col gap-3 border-t border-sidebar-border px-6 py-3 text-xs text-muted-foreground sm:flex-row sm:items-center sm:justify-between"
    >
        <span
            >Halaman {{ pagination.current_page }} dari
            {{ pagination.last_page }}</span
        >
        <div class="flex items-center gap-2">
            <Button
                type="button"
                variant="outline"
                size="sm"
                :disabled="!pagination.prev_page_url"
                @click="visitPage(pagination.prev_page_url)"
            >
                Sebelumnya
            </Button>
            <Button
                type="button"
                variant="outline"
                size="sm"
                :disabled="!pagination.next_page_url"
                @click="visitPage(pagination.next_page_url)"
            >
                Selanjutnya
            </Button>
        </div>
    </nav>
</template>
