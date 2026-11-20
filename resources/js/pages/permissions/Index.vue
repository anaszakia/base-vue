<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Key, Plus, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import ConfirmDeleteModal from '@/components/ConfirmDeleteModal.vue';
import InputError from '@/components/InputError.vue';
import PaginationControls from '@/components/PaginationControls.vue';
import { Badge } from '@/components/ui/badge';
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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { PaginatedCollection } from '@/types/pagination';

interface Permission {
    id: number;
    name: string;
    guard_name: string;
    created_at: string;
}

defineProps<{
    permissions: PaginatedCollection<Permission>;
}>();

const isCreateModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const permissionToDelete = ref<Permission | null>(null);

const form = useForm({
    name: '',
});

const submit = () => {
    form.post('/permissions', {
        onSuccess: () => {
            form.reset();
            isCreateModalOpen.value = false;
            toast.success('Permission berhasil ditambahkan!');
        },
        onError: () => {
            toast.error('Gagal menambahkan permission. Periksa kembali form.');
        },
    });
};

const deleteForm = useForm({});

const confirmDelete = (permission: Permission) => {
    permissionToDelete.value = permission;
    isDeleteModalOpen.value = true;
};

const handleDelete = () => {
    if (!permissionToDelete.value) {
        return;
    }

    deleteForm.delete(`/permissions/${permissionToDelete.value.id}`, {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
            permissionToDelete.value = null;
            toast.success('Permission berhasil dihapus!');
        },
        onError: () => {
            toast.error('Gagal menghapus permission.');
        },
    });
};
</script>

<template>
    <Head title="Manajemen Permission" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Manajemen Permission</h1>
                <p class="text-sm text-muted-foreground">
                    Daftar permission untuk hak akses sistem dan modul.
                </p>
            </div>

            <Button @click="isCreateModalOpen = true" class="w-fit">
                <Plus class="mr-2 h-4 w-4" />
                Tambah Permission
            </Button>
        </div>

        <div class="rounded-xl border border-sidebar-border bg-card shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-sidebar-border bg-muted/40 font-medium text-muted-foreground">
                        <tr>
                            <th class="px-6 py-3.5">ID</th>
                            <th class="px-6 py-3.5">Nama Permission</th>
                            <th class="px-6 py-3.5">Guard</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sidebar-border">
                        <tr v-for="permission in permissions.data" :key="permission.id" class="hover:bg-muted/30">
                            <td class="px-6 py-4 font-mono text-xs text-muted-foreground">{{ permission.id }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <Key class="h-4 w-4 text-primary" />
                                    <span class="font-medium font-mono text-xs">{{ permission.name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <Badge variant="outline">{{ permission.guard_name }}</Badge>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="text-destructive hover:bg-destructive/10 hover:text-destructive"
                                    @click="confirmDelete(permission)"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </td>
                        </tr>
                        <tr v-if="permissions.data.length === 0">
                            <td colspan="4" class="px-6 py-8 text-center text-muted-foreground">
                                Belum ada permission yang terdaftar.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <PaginationControls :pagination="permissions" />
        </div>

        <!-- Modal Tambah Permission -->
        <Dialog :open="isCreateModalOpen" @update:open="isCreateModalOpen = $event">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Tambah Permission Baru</DialogTitle>
                    <DialogDescription>
                        Contoh penamaan permission standar: <code>users.view</code>, <code>courses.create</code>.
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submit" class="space-y-4">
                    <div class="space-y-2">
                        <Label for="name">Nama Permission</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            placeholder="modul.action (e.g. products.create)"
                            required
                        />
                        <InputError :message="form.errors.name" />
                    </div>

                    <DialogFooter>
                        <DialogClose as-child>
                            <Button type="button" variant="outline">Batal</Button>
                        </DialogClose>
                        <Button type="submit" :disabled="form.processing">Simpan</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Pop-up Konfirmasi Hapus -->
        <ConfirmDeleteModal
            :open="isDeleteModalOpen"
            :title="`Hapus Permission '${permissionToDelete?.name}'?`"
            message="Apakah Anda yakin ingin menghapus permission ini? Seluruh role yang memiliki permission ini akan kehilangan izin tersebut."
            :loading="deleteForm.processing"
            @update:open="isDeleteModalOpen = $event"
            @confirm="handleDelete"
        />
    </div>
</template>
