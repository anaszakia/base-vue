<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    Activity,
    ArrowUpRight,
    CheckCircle2,
    Clock,
    Key,
    Layers,
    Menu as MenuIcon,
    Plus,
    Shield,
    ShieldAlert,
    ShieldCheck,
    Sparkles,
    UserCheck,
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

const props = defineProps<{
    stats: {
        total_users: number;
        total_roles: number;
        total_permissions: number;
        total_menus: number;
    };
    recentUsers: UserItem[];
    rolesSummary: RoleSummary[];
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

const formatDate = (dateStr: string) => {
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Welcome Hero Banner -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-800 p-7 text-white shadow-lg">
            <!-- Decorative blur & glow -->
            <div class="absolute -right-10 -top-10 h-64 w-64 rounded-full bg-emerald-500/20 blur-3xl"></div>
            <div class="absolute -bottom-10 right-1/3 h-48 w-48 rounded-full bg-teal-400/20 blur-2xl"></div>

            <div class="relative z-10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/30 px-3 py-1 text-xs font-medium text-emerald-100 backdrop-blur-sm">
                            <Sparkles class="h-3.5 w-3.5 text-emerald-300" />
                            Sistem Manajemen Akses Aktif
                        </span>
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">
                        {{ greeting }}, {{ currentUser?.name }}!
                    </h1>
                    <p class="text-sm text-emerald-100/90 max-w-xl">
                        Selamat datang kembali di panel administrasi. Sistem RBAC & Manajemen Menu dinamis siap dikelola.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <Button as-child variant="secondary" size="sm" class="bg-white/10 hover:bg-white/20 text-white border-0 backdrop-blur-sm">
                        <Link href="/menus">
                            <MenuIcon class="mr-1.5 h-4 w-4" />
                            Atur Menu
                        </Link>
                    </Button>
                    <Button as-child size="sm" class="bg-white text-emerald-900 hover:bg-emerald-50 font-semibold shadow-sm">
                        <Link href="/roles">
                            <ShieldCheck class="mr-1.5 h-4 w-4" />
                            Kelola Role
                        </Link>
                    </Button>
                </div>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Stat 1: Users -->
            <div class="group relative overflow-hidden rounded-xl border border-sidebar-border bg-card p-5 shadow-xs transition-all hover:shadow-md hover:border-emerald-500/50">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Total Pengguna</span>
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400">
                        <Users class="h-5 w-5" />
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-extrabold tracking-tight">{{ stats.total_users }}</div>
                    <p class="mt-1 flex items-center text-xs text-muted-foreground">
                        <UserCheck class="mr-1 h-3.5 w-3.5 text-emerald-600" />
                        Akun aktif terdaftar
                    </p>
                </div>
            </div>

            <!-- Stat 2: Roles -->
            <div class="group relative overflow-hidden rounded-xl border border-sidebar-border bg-card p-5 shadow-xs transition-all hover:shadow-md hover:border-emerald-500/50">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Role / Peran</span>
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400">
                        <Shield class="h-5 w-5" />
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-extrabold tracking-tight">{{ stats.total_roles }}</div>
                    <p class="mt-1 flex items-center text-xs text-muted-foreground">
                        <CheckCircle2 class="mr-1 h-3.5 w-3.5 text-emerald-600" />
                        Tingkatan hak akses
                    </p>
                </div>
            </div>

            <!-- Stat 3: Permissions -->
            <div class="group relative overflow-hidden rounded-xl border border-sidebar-border bg-card p-5 shadow-xs transition-all hover:shadow-md hover:border-emerald-500/50">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Permissions</span>
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400">
                        <Key class="h-5 w-5" />
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-extrabold tracking-tight">{{ stats.total_permissions }}</div>
                    <p class="mt-1 flex items-center text-xs text-muted-foreground">
                        <Layers class="mr-1 h-3.5 w-3.5 text-emerald-600" />
                        Otomatis tersinkronisasi
                    </p>
                </div>
            </div>

            <!-- Stat 4: Menus -->
            <div class="group relative overflow-hidden rounded-xl border border-sidebar-border bg-card p-5 shadow-xs transition-all hover:shadow-md hover:border-emerald-500/50">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Menu Sidebar</span>
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400">
                        <MenuIcon class="h-5 w-5" />
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-extrabold tracking-tight">{{ stats.total_menus }}</div>
                    <p class="mt-1 flex items-center text-xs text-muted-foreground">
                        <Activity class="mr-1 h-3.5 w-3.5 text-emerald-600" />
                        Item menu dinamis
                    </p>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left Col (2 spans): Ringkasan Role & Hak Akses -->
            <div class="space-y-6 lg:col-span-2">
                <!-- Roles Distribution Card -->
                <div class="rounded-xl border border-sidebar-border bg-card p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-base font-semibold">Tingkat Hak Akses (Role)</h2>
                            <p class="text-xs text-muted-foreground">Persebaran izin dan jumlah pengguna per role.</p>
                        </div>
                        <Button as-child variant="ghost" size="sm" class="text-xs text-primary hover:text-primary">
                            <Link href="/roles">
                                Lihat Semua
                                <ArrowUpRight class="ml-1 h-3.5 w-3.5" />
                            </Link>
                        </Button>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="role in rolesSummary"
                            :key="role.id"
                            class="flex items-center justify-between rounded-lg border border-sidebar-border/70 p-3.5 transition-colors hover:bg-muted/40"
                        >
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 items-center justify-center rounded-md bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">
                                    <ShieldCheck class="h-5 w-5" />
                                </div>
                                <div>
                                    <div class="font-semibold text-sm">{{ role.name }}</div>
                                    <div class="text-xs text-muted-foreground">
                                        {{ role.permissions_count }} Permissions diizinkan
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <Badge variant="secondary" class="font-normal text-xs">
                                    {{ role.users_count }} Pengguna
                                </Badge>
                                <Button as-child variant="outline" size="sm" class="h-8 text-xs">
                                    <Link href="/roles">Atur</Link>
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Registered Users -->
                <div class="rounded-xl border border-sidebar-border bg-card p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-base font-semibold">Pengguna Terbaru</h2>
                            <p class="text-xs text-muted-foreground">Daftar pengguna yang baru saja bergabung.</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-sidebar-border text-xs uppercase tracking-wider text-muted-foreground">
                                <tr>
                                    <th class="pb-3 font-medium">Nama</th>
                                    <th class="pb-3 font-medium">Role</th>
                                    <th class="pb-3 font-medium text-right">Terdaftar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-sidebar-border/50">
                                <tr v-for="user in recentUsers" :key="user.id" class="hover:bg-muted/20">
                                    <td class="py-3">
                                        <div class="font-medium text-xs">{{ user.name }}</div>
                                        <div class="text-[11px] text-muted-foreground">{{ user.email }}</div>
                                    </td>
                                    <td class="py-3">
                                        <div class="flex flex-wrap gap-1">
                                            <Badge
                                                v-for="r in user.roles"
                                                :key="r.id"
                                                variant="outline"
                                                class="text-[10px]"
                                            >
                                                {{ r.name }}
                                            </Badge>
                                            <span v-if="!user.roles || user.roles.length === 0" class="text-xs text-muted-foreground">-</span>
                                        </div>
                                    </td>
                                    <td class="py-3 text-right text-xs text-muted-foreground font-mono">
                                        {{ formatDate(user.created_at) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Col (1 span): Quick Actions & System Info -->
            <div class="space-y-6">
                <!-- Quick Navigation Box -->
                <div class="rounded-xl border border-sidebar-border bg-card p-6 shadow-sm">
                    <h2 class="text-base font-semibold mb-1">Aksi Cepat</h2>
                    <p class="text-xs text-muted-foreground mb-4">Pintasan praktis untuk mengelola sistem.</p>

                    <div class="flex flex-col gap-2.5">
                        <Button as-child variant="outline" class="justify-between h-10">
                            <Link href="/menus">
                                <div class="flex items-center gap-2">
                                    <MenuIcon class="h-4 w-4 text-emerald-700" />
                                    <span>Tambah Menu Sidebar</span>
                                </div>
                                <Plus class="h-4 w-4 text-muted-foreground" />
                            </Link>
                        </Button>

                        <Button as-child variant="outline" class="justify-between h-10">
                            <Link href="/roles">
                                <div class="flex items-center gap-2">
                                    <Shield class="h-4 w-4 text-emerald-700" />
                                    <span>Buat Role Baru</span>
                                </div>
                                <Plus class="h-4 w-4 text-muted-foreground" />
                            </Link>
                        </Button>

                        <Button as-child variant="outline" class="justify-between h-10">
                            <Link href="/permissions">
                                <div class="flex items-center gap-2">
                                    <Key class="h-4 w-4 text-emerald-700" />
                                    <span>Lihat Daftar Permission</span>
                                </div>
                                <ArrowUpRight class="h-4 w-4 text-muted-foreground" />
                            </Link>
                        </Button>
                    </div>
                </div>

                <!-- Security & RBAC Status -->
                <div class="rounded-xl border border-sidebar-border bg-card p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-3">
                        <ShieldCheck class="h-5 w-5 text-emerald-600" />
                        <h2 class="text-base font-semibold">Status RBAC</h2>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between rounded-md bg-muted/40 p-2.5">
                            <span class="text-muted-foreground">Super Admin Bypass</span>
                            <Badge variant="default" class="bg-emerald-600 text-white">Aktif</Badge>
                        </div>
                        <div class="flex items-center justify-between rounded-md bg-muted/40 p-2.5">
                            <span class="text-muted-foreground">Auto-Scan Permission</span>
                            <Badge variant="default" class="bg-emerald-600 text-white">Aktif</Badge>
                        </div>
                        <div class="flex items-center justify-between rounded-md bg-muted/40 p-2.5">
                            <span class="text-muted-foreground">Dynamic Menu Filtering</span>
                            <Badge variant="default" class="bg-emerald-600 text-white">Aktif</Badge>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
