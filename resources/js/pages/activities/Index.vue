<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Clock, History, User } from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

interface ActivityItem {
    id: number;
    description: string;
    log_name: string;
    causer?: {
        id: number;
        name: string;
        email: string;
    };
    properties?: Record<string, unknown>;
    created_at: string;
}

interface PaginatedActivities {
    data: ActivityItem[];
    current_page: number;
    last_page: number;
    total: number;
    prev_page_url: string | null;
    next_page_url: string | null;
}

defineProps<{
    activities: PaginatedActivities;
}>();

const formatDateTime = (dateStr: string) => {
    return new Date(dateStr).toLocaleString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    });
};
</script>

<template>
    <Head title="Log Aktivitas Sistem" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Log Aktivitas Sistem</h1>
                <p class="text-sm text-muted-foreground">
                    Audit trail dan riwayat aksi penting yang dilakukan oleh pengguna di dalam sistem.
                </p>
            </div>
            <div class="text-xs text-muted-foreground">
                Total tercatat: <span class="font-semibold text-foreground">{{ activities.total }}</span> aksi
            </div>
        </div>

        <div class="rounded-xl border border-sidebar-border bg-card shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-sidebar-border bg-muted/40 font-medium text-muted-foreground">
                        <tr>
                            <th class="px-6 py-3.5">Waktu</th>
                            <th class="px-6 py-3.5">Pengguna (Pelaku)</th>
                            <th class="px-6 py-3.5">Aktivitas / Deskripsi</th>
                            <th class="px-6 py-3.5">Grup Log</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sidebar-border">
                        <tr v-for="item in activities.data" :key="item.id" class="hover:bg-muted/30">
                            <td class="px-6 py-4 font-mono text-xs text-muted-foreground whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <Clock class="h-3.5 w-3.5 text-primary" />
                                    <span>{{ formatDateTime(item.created_at) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div v-if="item.causer" class="flex items-center gap-2">
                                    <div class="flex h-7 w-7 items-center justify-center rounded-full bg-emerald-500/10 text-emerald-700">
                                        <User class="h-4 w-4" />
                                    </div>
                                    <div>
                                        <div class="font-medium text-xs">{{ item.causer.name }}</div>
                                        <div class="text-[11px] text-muted-foreground">{{ item.causer.email }}</div>
                                    </div>
                                </div>
                                <span v-else class="text-xs text-muted-foreground italic">Sistem Otomatis</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-xs">{{ item.description }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <Badge variant="outline" class="text-[11px] font-mono">
                                    {{ item.log_name || 'default' }}
                                </Badge>
                            </td>
                        </tr>
                        <tr v-if="activities.data.length === 0">
                            <td colspan="4" class="px-6 py-12 text-center text-muted-foreground">
                                <History class="mx-auto mb-2 h-8 w-8 text-muted-foreground/50" />
                                Belum ada aktivitas yang tercatat dalam log audit.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="activities.last_page > 1"
                class="flex items-center justify-between border-t border-sidebar-border px-6 py-3 text-xs text-muted-foreground"
            >
                <span>Halaman {{ activities.current_page }} dari {{ activities.last_page }}</span>
                <div class="flex items-center gap-2">
                    <Button
                        variant="outline"
                        size="sm"
                        :disabled="!activities.prev_page_url"
                        @click="router.get(activities.prev_page_url!)"
                    >
                        Sebelumnya
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        :disabled="!activities.next_page_url"
                        @click="router.get(activities.next_page_url!)"
                    >
                        Selanjutnya
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
