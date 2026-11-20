<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    Activity as ActivityIcon,
    ArrowRight,
    ArrowUpRight,
    CheckCircle2,
    Clock,
    FileSpreadsheet,
    History,
    KeyRound,
    Layers,
    Menu as MenuIcon,
    Plus,
    Server,
    Shield,
    ShieldCheck,
    UserPlus,
    Users,
} from '@lucide/vue';
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes';
import type { Auth } from '@/types';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

interface UserItem {
    id: number;
    name: string;
    email: string;
    created_at: string;
    roles?: { id: number; name: string }[];
}

interface RoleSummary {
    id: number;
    name: string;
    users_count: number;
    permissions_count: number;
}

interface ActivityItem {
    id: number;
    description: string;
    created_at: string;
    causer?: {
        id: number;
        name: string;
        email: string;
    } | null;
}

const props = defineProps<{
    stats: {
        total_users: number;
        total_roles: number;
        total_permissions: number;
        total_menus: number;
    };
    recentUsers: UserItem[];
    rolesSummary: RoleSummary[];
    recentActivities?: ActivityItem[];
}>();

const page = usePage<{ auth: Auth }>();
const currentUser = computed(() => page.props.auth?.user);

const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Selamat Pagi';
    if (hour < 15) return 'Selamat Siang';
    if (hour < 18) return 'Selamat Sore';
    return 'Selamat Malam';
});

const todayFormatted = computed(() => {
    return new Intl.DateTimeFormat('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(new Date());
});

const formatTimeAgo = (dateStr: string) => {
    const diff = Math.floor((new Date().getTime() - new Date(dateStr).getTime()) / 1000);
    if (diff < 60) return 'Baru saja';
    if (diff < 3600) return `${Math.floor(diff / 60)} mnt lalu`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} jam lalu`;
    return `${Math.floor(diff / 86400)} hari lalu`;
};

const formatDate = (dateStr: string) => {
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const getInitials = (name: string) => {
    return name
        .split(' ')
        .map((n) => n[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
};
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Top Header & Context Bar -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-border/60 pb-5">
            <div>
                <div class="flex items-center gap-2 text-xs font-medium text-muted-foreground">
                    <span>{{ todayFormatted }}</span>
                    <span>•</span>
                    <span class="inline-flex items-center gap-1.5 text-emerald-700 dark:text-emerald-400 font-semibold">
                        <span class="size-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Portal Aktif & Terlindungi
                    </span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-foreground mt-1">
                    {{ greeting }}, {{ currentUser?.name }}
                </h1>
                <p class="text-xs text-muted-foreground mt-0.5">
                    Ringkasan performa hak akses, pengguna aktif, dan riwayat audit sistem terbaru.
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                <Button as-child variant="outline" size="sm" class="h-9 text-xs">
                    <Link href="/activities">
                        <History class="mr-1.5 size-3.5 text-muted-foreground" />
                        Log Audit
                    </Link>
                </Button>
                <Button as-child size="sm" class="h-9 text-xs bg-emerald-700 hover:bg-emerald-800 text-white font-medium shadow-xs">
                    <Link href="/users">
                        <UserPlus class="mr-1.5 size-3.5" />
                        Tambah Pengguna
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Metric Stat Cards (Modern, Clean Border with Focus Highlights) -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Users -->
            <div class="group relative rounded-xl border border-border/80 bg-card p-5 shadow-xs transition hover:border-emerald-500/50 hover:shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-muted-foreground">Total Pengguna</p>
                        <h3 class="text-2xl font-bold tracking-tight text-foreground mt-2 font-mono">
                            {{ stats.total_users }}
                        </h3>
                    </div>
                    <div class="flex size-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400">
                        <Users class="size-4" />
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between pt-3 border-t border-border/40 text-[11px]">
                    <span class="text-muted-foreground">Akun terdaftar dalam sistem</span>
                    <Link href="/users" class="font-medium text-emerald-700 hover:text-emerald-800 dark:text-emerald-400 flex items-center gap-0.5">
                        Kelola <ArrowRight class="size-3" />
                    </Link>
                </div>
            </div>

            <!-- Total Roles -->
            <div class="group relative rounded-xl border border-border/80 bg-card p-5 shadow-xs transition hover:border-emerald-500/50 hover:shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-muted-foreground">Peran (Roles)</p>
                        <h3 class="text-2xl font-bold tracking-tight text-foreground mt-2 font-mono">
                            {{ stats.total_roles }}
                        </h3>
                    </div>
                    <div class="flex size-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400">
                        <Shield class="size-4" />
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between pt-3 border-t border-border/40 text-[11px]">
                    <span class="text-muted-foreground">Tingkatan wewenang hak akses</span>
                    <Link href="/roles" class="font-medium text-emerald-700 hover:text-emerald-800 dark:text-emerald-400 flex items-center gap-0.5">
                        Atur <ArrowRight class="size-3" />
                    </Link>
                </div>
            </div>

            <!-- Total Permissions -->
            <div class="group relative rounded-xl border border-border/80 bg-card p-5 shadow-xs transition hover:border-emerald-500/50 hover:shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-muted-foreground">Hak Akses (Permissions)</p>
                        <h3 class="text-2xl font-bold tracking-tight text-foreground mt-2 font-mono">
                            {{ stats.total_permissions }}
                        </h3>
                    </div>
                    <div class="flex size-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400">
                        <KeyRound class="size-4" />
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between pt-3 border-t border-border/40 text-[11px]">
                    <span class="text-muted-foreground">Auto-scan controller & rute</span>
                    <Link href="/permissions" class="font-medium text-emerald-700 hover:text-emerald-800 dark:text-emerald-400 flex items-center gap-0.5">
                        Rincian <ArrowRight class="size-3" />
                    </Link>
                </div>
            </div>

            <!-- Dynamic Menus -->
            <div class="group relative rounded-xl border border-border/80 bg-card p-5 shadow-xs transition hover:border-emerald-500/50 hover:shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-muted-foreground">Menu Dinamis</p>
                        <h3 class="text-2xl font-bold tracking-tight text-foreground mt-2 font-mono">
                            {{ stats.total_menus }}
                        </h3>
                    </div>
                    <div class="flex size-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400">
                        <MenuIcon class="size-4" />
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between pt-3 border-t border-border/40 text-[11px]">
                    <span class="text-muted-foreground">Navigasi sidebar terhubung RBAC</span>
                    <Link href="/menus" class="font-medium text-emerald-700 hover:text-emerald-800 dark:text-emerald-400 flex items-center gap-0.5">
                        Susun <ArrowRight class="size-3" />
                    </Link>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left 2 Columns: Roles & Recent Users -->
            <div class="space-y-6 lg:col-span-2">
                <!-- Roles Distribution Card -->
                <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-sm font-semibold text-foreground">Distribusi Wewenang Peran</h2>
                            <p class="text-xs text-muted-foreground">Perbandingan izin hak akses dan jumlah pengguna per role.</p>
                        </div>
                        <Button as-child variant="ghost" size="sm" class="h-8 text-xs text-emerald-700 hover:text-emerald-800 dark:text-emerald-400 font-medium">
                            <Link href="/roles">
                                Lihat Semua Role
                                <ArrowUpRight class="ml-1 size-3.5" />
                            </Link>
                        </Button>
                    </div>

                    <div class="space-y-2.5">
                        <div
                            v-for="role in rolesSummary"
                            :key="role.id"
                            class="flex items-center justify-between rounded-lg border border-border/60 p-3 hover:bg-muted/40 transition-colors"
                        >
                            <div class="flex items-center gap-3">
                                <div class="flex size-8 items-center justify-center rounded-md bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 font-semibold text-xs font-mono">
                                    {{ role.name[0] }}
                                </div>
                                <div>
                                    <div class="font-medium text-xs text-foreground flex items-center gap-1.5">
                                        {{ role.name }}
                                        <Badge v-if="role.name === 'Super Admin'" variant="outline" class="text-[10px] py-0 border-emerald-500/40 text-emerald-700 dark:text-emerald-400">
                                            Bypass All
                                        </Badge>
                                    </div>
                                    <div class="text-[11px] text-muted-foreground mt-0.5">
                                        {{ role.permissions_count }} izin akses terpasang
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="text-xs font-mono text-muted-foreground">
                                    {{ role.users_count }} user
                                </span>
                                <Button as-child variant="ghost" size="sm" class="h-7 px-2 text-xs">
                                    <Link href="/roles">Kelola</Link>
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Registered Users -->
                <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-sm font-semibold text-foreground">Pengguna Baru Terdaftar</h2>
                            <p class="text-xs text-muted-foreground">Daftar pengguna terbaru yang masuk ke dalam sistem.</p>
                        </div>
                        <Button as-child variant="ghost" size="sm" class="h-8 text-xs text-emerald-700 hover:text-emerald-800 dark:text-emerald-400 font-medium">
                            <Link href="/users">
                                Kelola Pengguna
                                <ArrowUpRight class="ml-1 size-3.5" />
                            </Link>
                        </Button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="border-b border-border/60 text-muted-foreground uppercase text-[10px] tracking-wider font-semibold">
                                <tr>
                                    <th class="pb-2.5 font-medium">Pengguna</th>
                                    <th class="pb-2.5 font-medium">Peran / Role</th>
                                    <th class="pb-2.5 font-medium text-right">Bergabung</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border/40">
                                <tr v-for="user in recentUsers" :key="user.id" class="hover:bg-muted/30 transition-colors">
                                    <td class="py-2.5">
                                        <div class="flex items-center gap-2.5">
                                            <div class="flex size-7 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 font-semibold text-[11px]">
                                                {{ getInitials(user.name) }}
                                            </div>
                                            <div class="truncate">
                                                <div class="font-medium text-foreground truncate">{{ user.name }}</div>
                                                <div class="text-[11px] text-muted-foreground font-mono truncate">{{ user.email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-2.5">
                                        <div class="flex flex-wrap gap-1">
                                            <Badge
                                                v-for="r in user.roles"
                                                :key="r.id"
                                                variant="secondary"
                                                class="text-[10px] py-0 font-normal"
                                            >
                                                {{ r.name }}
                                            </Badge>
                                            <span v-if="!user.roles || user.roles.length === 0" class="text-xs text-muted-foreground italic">
                                                Tanpa Role
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-2.5 text-right font-mono text-muted-foreground text-[11px]">
                                        {{ formatDate(user.created_at) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right 1 Column: Activity Feed & Security Infrastructure -->
            <div class="space-y-6">
                <!-- Activity Audit Trail Card -->
                <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <ActivityIcon class="size-4 text-emerald-700 dark:text-emerald-400" />
                            <h2 class="text-sm font-semibold text-foreground">Log Aktivitas Terkini</h2>
                        </div>
                        <Button as-child variant="ghost" size="sm" class="h-7 px-1.5 text-xs text-muted-foreground hover:text-foreground">
                            <Link href="/activities">Semua</Link>
                        </Button>
                    </div>

                    <div v-if="recentActivities && recentActivities.length > 0" class="space-y-3">
                        <div
                            v-for="activity in recentActivities"
                            :key="activity.id"
                            class="flex items-start gap-2.5 text-xs border-l-2 border-emerald-600/40 pl-3 py-1 hover:border-emerald-600 transition-colors"
                        >
                            <div class="flex-1 min-w-0">
                                <p class="text-foreground font-medium text-xs leading-snug">
                                    {{ activity.description }}
                                </p>
                                <div class="flex items-center gap-2 mt-1 text-[11px] text-muted-foreground">
                                    <span class="truncate">{{ activity.causer?.name ?? 'Sistem' }}</span>
                                    <span>•</span>
                                    <span class="font-mono">{{ formatTimeAgo(activity.created_at) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-6 text-xs text-muted-foreground italic">
                        Belum ada aktivitas audit log yang tercatat.
                    </div>
                </div>

                <!-- System Architecture & Security Status -->
                <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs">
                    <div class="flex items-center gap-2 mb-3">
                        <ShieldCheck class="size-4 text-emerald-700 dark:text-emerald-400" />
                        <h2 class="text-sm font-semibold text-foreground">Infrastruktur Keamanan</h2>
                    </div>

                    <div class="space-y-2.5 text-xs">
                        <div class="flex items-center justify-between rounded-lg bg-muted/40 p-2.5 border border-border/50">
                            <div class="flex flex-col">
                                <span class="font-medium text-foreground">RBAC Guard</span>
                                <span class="text-[11px] text-muted-foreground">Spatie Laravel Permission</span>
                            </div>
                            <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-700 dark:text-emerald-400 font-mono">
                                <span class="size-1.5 rounded-full bg-emerald-500"></span> Aktif
                            </span>
                        </div>

                        <div class="flex items-center justify-between rounded-lg bg-muted/40 p-2.5 border border-border/50">
                            <div class="flex flex-col">
                                <span class="font-medium text-foreground">Audit Columns</span>
                                <span class="text-[11px] text-muted-foreground">created/updated/deleted_by</span>
                            </div>
                            <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-700 dark:text-emerald-400 font-mono">
                                <span class="size-1.5 rounded-full bg-emerald-500"></span> Aktif
                            </span>
                        </div>

                        <div class="flex items-center justify-between rounded-lg bg-muted/40 p-2.5 border border-border/50">
                            <div class="flex flex-col">
                                <span class="font-medium text-foreground">Security Headers</span>
                                <span class="text-[11px] text-muted-foreground">XSS, CSRF, Clickjacking</span>
                            </div>
                            <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-700 dark:text-emerald-400 font-mono">
                                <span class="size-1.5 rounded-full bg-emerald-500"></span> Aktif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Action Shortcuts -->
                <div class="rounded-xl border border-border/80 bg-card p-5 shadow-xs">
                    <h2 class="text-sm font-semibold text-foreground mb-3">Pintasan Navigasi</h2>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <Button as-child variant="outline" class="h-9 justify-start text-xs border-border/60">
                            <Link href="/menus">
                                <MenuIcon class="mr-1.5 size-3.5 text-emerald-700 dark:text-emerald-400" />
                                Kelola Menu
                            </Link>
                        </Button>
                        <Button as-child variant="outline" class="h-9 justify-start text-xs border-border/60">
                            <Link href="/roles">
                                <Shield class="mr-1.5 size-3.5 text-emerald-700 dark:text-emerald-400" />
                                Tambah Role
                            </Link>
                        </Button>
                        <Button as-child variant="outline" class="h-9 justify-start text-xs border-border/60">
                            <Link href="/permissions">
                                <KeyRound class="mr-1.5 size-3.5 text-emerald-700 dark:text-emerald-400" />
                                Permissions
                            </Link>
                        </Button>
                        <Button as-child variant="outline" class="h-9 justify-start text-xs border-border/60">
                            <Link href="/activities">
                                <History class="mr-1.5 size-3.5 text-emerald-700 dark:text-emerald-400" />
                                Log Aktivitas
                            </Link>
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
