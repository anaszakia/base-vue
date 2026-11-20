<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, RefreshCw, ShieldCheck, Trash2 } from '@lucide/vue';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';
import ConfirmDeleteModal from '@/components/ConfirmDeleteModal.vue';
import InputError from '@/components/InputError.vue';
import PaginationControls from '@/components/PaginationControls.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { PaginatedCollection } from '@/types/pagination';

interface Permission {
    id: number;
    name: string;
}

interface Role {
    id: number;
    name: string;
    permissions: Permission[];
}

const props = defineProps<{
    roles: PaginatedCollection<Role>;
    permissions: Permission[];
}>();

const isModalOpen = ref(false);
const editingRole = ref<Role | null>(null);
const isDeleteModalOpen = ref(false);
const roleToDelete = ref<Role | null>(null);

const form = useForm({
    name: '',
    permissions: [] as string[],
});

// Kelompokkan permission berdasarkan nama modul (misal: "users.create" -> modul "users")
const groupedPermissions = computed(() => {
    const groups: Record<string, Permission[]> = {};
    for (const perm of props.permissions) {
        const parts = perm.name.split('.');
        const groupKey = parts.length > 1 ? parts[0] : 'general';
        if (!groups[groupKey]) {
            groups[groupKey] = [];
        }
        groups[groupKey].push(perm);
    }
    return groups;
});

const openCreateModal = () => {
    editingRole.value = null;
    form.reset();
    form.clearErrors();
    form.permissions = [];
    isModalOpen.value = true;
};

const openEditModal = (role: Role) => {
    editingRole.value = role;
    form.clearErrors();
    form.name = role.name;
    form.permissions = role.permissions.map((p) => p.name);
    isModalOpen.value = true;
};

const togglePermission = (permName: string) => {
    const index = form.permissions.indexOf(permName);
    if (index > -1) {
        form.permissions.splice(index, 1);
    } else {
        form.permissions.push(permName);
    }
};

const toggleGroup = (groupKey: string) => {
    const groupPerms = groupedPermissions.value[groupKey] || [];
    const groupNames = groupPerms.map((p) => p.name);
    const allSelected = groupNames.every((name) => form.permissions.includes(name));

    if (allSelected) {
        // Hapus semua dari grup ini
        form.permissions = form.permissions.filter((name) => !groupNames.includes(name));
    } else {
        // Tambahkan semua dari grup ini
        for (const name of groupNames) {
            if (!form.permissions.includes(name)) {
                form.permissions.push(name);
            }
        }
    }
};

const isGroupAllSelected = (groupKey: string) => {
    const groupPerms = groupedPermissions.value[groupKey] || [];
    if (groupPerms.length === 0) {
        return false;
    }
    return groupPerms.every((p) => form.permissions.includes(p.name));
};

const toggleAllPermissions = () => {
    if (form.permissions.length === props.permissions.length) {
        form.permissions = [];
    } else {
        form.permissions = props.permissions.map((p) => p.name);
    }
};

const submit = () => {
    if (editingRole.value) {
        form.put(`/roles/${editingRole.value.id}`, {
            onSuccess: () => {
                isModalOpen.value = false;
                toast.success('Role & hak akses berhasil diperbarui!');
            },
            onError: () => {
                toast.error('Gagal memperbarui role. Periksa kembali form.');
            },
        });
    } else {
        form.post('/roles', {
            onSuccess: () => {
                isModalOpen.value = false;
                toast.success('Role baru berhasil ditambahkan!');
            },
            onError: () => {
                toast.error('Gagal menambahkan role. Periksa kembali form.');
            },
        });
    }
};

const deleteForm = useForm({});

const confirmDelete = (role: Role) => {
    roleToDelete.value = role;
    isDeleteModalOpen.value = true;
};

const handleDelete = () => {
    if (!roleToDelete.value) {
        return;
    }

    deleteForm.delete(`/roles/${roleToDelete.value.id}`, {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
            roleToDelete.value = null;
            toast.success('Role berhasil dihapus!');
        },
        onError: () => {
            toast.error('Gagal menghapus role.');
        },
    });
};
</script>

<template>
    <Head title="Manajemen Role & Hak Akses" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Role & Hak Akses</h1>
                <p class="text-sm text-muted-foreground">
                    Modul baru otomatis terdeteksi. Cukup buka modal edit role dan centang izin yang diinginkan.
                </p>
            </div>

            <Button @click="openCreateModal" class="w-fit">
                <Plus class="mr-2 h-4 w-4" />
                Tambah Role Baru
            </Button>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="role in roles.data"
                :key="role.id"
                class="flex flex-col justify-between rounded-xl border border-sidebar-border bg-card p-6 shadow-sm"
            >
                <div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <ShieldCheck class="h-5 w-5 text-primary" />
                            <h2 class="font-semibold text-lg">{{ role.name }}</h2>
                        </div>
                        <Badge variant="secondary">
                            {{ role.permissions.length }} Permissions
                        </Badge>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-1.5 max-h-36 overflow-y-auto">
                        <Badge
                            v-for="perm in role.permissions"
                            :key="perm.id"
                            variant="outline"
                            class="text-[11px] font-mono"
                        >
                            {{ perm.name }}
                        </Badge>
                        <span v-if="role.permissions.length === 0" class="text-xs text-muted-foreground italic">
                            Belum ada permission.
                        </span>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-2 border-t border-sidebar-border pt-4">
                    <Button variant="outline" size="sm" @click="openEditModal(role)">
                        <Pencil class="mr-1.5 h-3.5 w-3.5" />
                        Edit Hak Akses
                    </Button>
                    <Button
                        v-if="role.name !== 'Super Admin'"
                        variant="ghost"
                        size="sm"
                        class="text-destructive hover:bg-destructive/10 hover:text-destructive"
                        @click="confirmDelete(role)"
                    >
                        <Trash2 class="h-3.5 w-3.5" />
                    </Button>
                </div>
            </div>
        </div>
        <PaginationControls :pagination="roles" />

        <!-- Modal Tambah/Edit Role -->
        <Dialog :open="isModalOpen" @update:open="isModalOpen = $event">
            <DialogContent class="max-w-[calc(100%-2rem)] sm:max-w-[500px] max-h-[85vh] flex flex-col p-5">
                <DialogHeader class="space-y-1">
                    <DialogTitle class="text-base font-semibold">{{ editingRole ? `Edit Hak Akses: ${editingRole.name}` : 'Tambah Role Baru' }}</DialogTitle>
                    <DialogDescription class="text-xs text-muted-foreground">
                        Setiap modul baru otomatis memiliki grup permission tersendiri. Anda tinggal mencentang sesuai kebutuhan.
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submit" class="flex flex-col gap-3.5 overflow-y-auto pr-1 pt-1">
                    <div class="space-y-1.5">
                        <Label for="role_name" class="text-xs">Nama Role</Label>
                        <Input
                            id="role_name"
                            v-model="form.name"
                            placeholder="Contoh: Manager, Dosen, Finance"
                            class="h-9 text-xs"
                            required
                        />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between border-b border-sidebar-border pb-1.5">
                            <div>
                                <Label class="text-xs font-semibold">Daftar Permission per Modul</Label>
                                <p class="text-[11px] text-muted-foreground">Centang modul atau aksi yang diizinkan</p>
                            </div>
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                class="text-[11px] h-6 px-2"
                                @click="toggleAllPermissions"
                            >
                                {{ form.permissions.length === permissions.length ? 'Batal Pilih Semua' : 'Pilih Semua' }}
                            </Button>
                        </div>

                        <!-- Grouped Permissions List -->
                        <div class="flex flex-col gap-2.5 max-h-60 overflow-y-auto pr-1">
                            <div
                                v-for="(perms, groupName) in groupedPermissions"
                                :key="groupName"
                                class="rounded-lg border border-sidebar-border p-2.5 bg-muted/20"
                            >
                                <div class="flex items-center justify-between mb-1.5">
                                    <div class="flex items-center gap-1.5">
                                        <Checkbox
                                            :id="`group-${groupName}`"
                                            :checked="isGroupAllSelected(groupName as string)"
                                            @update:checked="toggleGroup(groupName as string)"
                                        />
                                        <label
                                            :for="`group-${groupName}`"
                                            class="font-semibold text-[11px] uppercase tracking-wider cursor-pointer"
                                        >
                                            Modul: {{ groupName }}
                                        </label>
                                    </div>
                                    <span class="text-[10px] text-muted-foreground font-mono">
                                        {{ perms.filter(p => form.permissions.includes(p.name)).length }}/{{ perms.length }} dipilih
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-1.5 pl-5">
                                    <label
                                        v-for="permission in perms"
                                        :key="permission.id"
                                        class="flex items-center gap-1.5 rounded p-1 hover:bg-muted cursor-pointer text-xs font-mono"
                                    >
                                        <Checkbox
                                            :checked="form.permissions.includes(permission.name)"
                                            @update:checked="togglePermission(permission.name)"
                                        />
                                        <span class="truncate">{{ permission.name }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <DialogFooter class="mt-4 pt-2 border-t border-sidebar-border">
                        <DialogClose as-child>
                            <Button type="button" variant="outline" size="sm" class="h-8 text-xs">Batal</Button>
                        </DialogClose>
                        <Button type="submit" size="sm" class="h-8 text-xs" :disabled="form.processing">Simpan Perubahan</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Pop-up Konfirmasi Hapus -->
        <ConfirmDeleteModal
            :open="isDeleteModalOpen"
            :title="`Hapus Role '${roleToDelete?.name}'?`"
            message="Apakah Anda yakin ingin menghapus role ini? Pengguna dengan role ini tidak akan lagi memiliki hak akses terkait."
            :loading="deleteForm.processing"
            @update:open="isDeleteModalOpen = $event"
            @confirm="handleDelete"
        />
    </div>
</template>
