<script setup lang="ts">
import { AlertTriangle } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

const props = withDefaults(
    defineProps<{
        open: boolean;
        title?: string;
        message?: string;
        confirmText?: string;
        cancelText?: string;
        loading?: boolean;
    }>(),
    {
        title: 'Konfirmasi Hapus Data',
        message: 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.',
        confirmText: 'Ya, Hapus Data',
        cancelText: 'Batal',
        loading: false,
    }
);

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
    (e: 'confirm'): void;
}>();
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="sm:max-w-[420px]">
            <DialogHeader class="flex flex-col items-center sm:items-start">
                <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-full bg-destructive/10 text-destructive">
                    <AlertTriangle class="h-6 w-6" />
                </div>
                <DialogTitle class="text-lg font-semibold">{{ title }}</DialogTitle>
                <DialogDescription class="mt-1 text-sm text-muted-foreground">
                    {{ message }}
                </DialogDescription>
            </DialogHeader>

            <DialogFooter class="mt-5 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <DialogClose as-child>
                    <Button type="button" variant="outline" :disabled="loading">
                        {{ cancelText }}
                    </Button>
                </DialogClose>
                <Button
                    type="button"
                    variant="destructive"
                    :disabled="loading"
                    @click="emit('confirm')"
                >
                    <span v-if="loading">Menghapus...</span>
                    <span v-else>{{ confirmText }}</span>
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
