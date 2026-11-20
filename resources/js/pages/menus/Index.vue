<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { CornerDownRight, Menu as MenuIcon, Pencil, Plus, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import ConfirmDeleteModal from '@/components/ConfirmDeleteModal.vue';
import DynamicIcon from '@/components/DynamicIcon.vue';
import IconPicker from '@/components/IconPicker.vue';
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
import { AVAILABLE_ICONS } from '@/constants/icons';
import type { PaginatedCollection } from '@/types/pagination';

interface Permission {
    id: number;
    name: string;
}

interface MenuItem {
    id: number;
    parent_id: number | null;
    category: string | null;
    title: string;
    route_or_url: string | null;
    icon: string | null;
    permission_name: string | null;
    order: number;
    is_active: boolean;
    parent?: MenuItem;
}

const props = defineProps<{
    menus: PaginatedCollection<MenuItem>;
    parentMenus: MenuItem[];
    permissions: Permission[];
}>();

const isModalOpen = ref(false);
const editingMenu = ref<MenuItem | null>(null);
const isDeleteModalOpen = ref(false);
const menuToDelete = ref<MenuItem | null>(null);

const form = useForm({
    parent_id: null as number | null,
    category: 'Menu',
    title: '',
    route_or_url: '',
    icon: '',
    permission_name: null as string | null,
    order: 0,
    is_active: true,
});

const openCreateModal = () => {
    editingMenu.value = null;
    form.reset();
    form.clearErrors();
    form.parent_id = null;
    form.category = 'Menu';
    form.permission_name = null;
    form.icon = '';
    form.order = props.menus.total + 1;
    form.is_active = true;
    isModalOpen.value = true;
};

const openEditModal = (item: MenuItem) => {
    editingMenu.value = item;
    form.clearErrors();
    form.parent_id = item.parent_id;
    form.category = item.category ?? 'Menu';
    form.title = item.title;
    form.route_or_url = item.route_or_url ?? '';
    form.icon = item.icon ?? '';
    form.permission_name = item.permission_name;
    form.order = item.order;
    form.is_active = item.is_active;
    isModalOpen.value = true;
};

const submit = () => {
    if (editingMenu.value) {
        form.put(`/menus/${editingMenu.value.id}`, {
            onSuccess: () => {
                isModalOpen.value = false;
                toast.success('Menu berhasil diperbarui!');
            },
            onError: () => {
                toast.error('Gagal memperbarui menu. Periksa kembali form.');
            },
        });
    } else {
        form.post('/menus', {
            onSuccess: () => {
                isModalOpen.value = false;
                toast.success('Menu baru berhasil ditambahkan!');
            },
            onError: () => {
                toast.error('Gagal menambahkan menu. Periksa kembali form.');
            },
        });
    }
};

const deleteForm = useForm({});

const confirmDelete = (item: MenuItem) => {
    menuToDelete.value = item;
    isDeleteModalOpen.value = true;
};

const handleDelete = () => {
    if (!menuToDelete.value) {
        return;
    }

    deleteForm.delete(`/menus/${menuToDelete.value.id}`, {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
            menuToDelete.value = null;
            toast.success('Menu berhasil dihapus!');
        },
        onError: () => {
            toast.error('Gagal menghapus menu.');
        },
    });
};
</script>

<template>
    <Head title="Manajemen Menu Sidebar" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Manajemen Menu Sidebar</h1>
                <p class="text-sm text-muted-foreground">
                    Atur menu sidebar, hierarki sub-menu, ikon, serta permission yang dibutuhkan.
                </p>
            </div>

            <Button @click="openCreateModal" class="w-fit">
                <Plus class="mr-2 h-4 w-4" />
                Tambah Menu Baru
            </Button>
        </div>

        <div class="rounded-xl border border-sidebar-border bg-card shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-sidebar-border bg-muted/40 font-medium text-muted-foreground">
                        <tr>
                            <th class="px-6 py-3.5">Urutan</th>
                            <th class="px-6 py-3.5">Kategori</th>
                            <th class="px-6 py-3.5">Nama Menu</th>
                            <th class="px-6 py-3.5">Route / URL</th>
                            <th class="px-6 py-3.5">Permission Terkait</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sidebar-border">
                        <tr v-for="item in menus.data" :key="item.id" class="hover:bg-muted/30">
                            <td class="px-6 py-4 font-mono text-xs">{{ item.order }}</td>
                            <td class="px-6 py-4">
                                <Badge v-if="!item.parent_id" variant="outline" class="font-normal text-[11px]">
                                    {{ item.category || 'Menu' }}
                                </Badge>
                                <span v-else class="text-xs text-muted-foreground">-</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <CornerDownRight v-if="item.parent_id" class="h-4 w-4 text-muted-foreground ml-3" />
                                    <DynamicIcon v-if="item.icon" :name="item.icon" class="h-4 w-4 text-primary" />
                                    <span :class="item.parent_id ? 'text-muted-foreground text-xs' : 'font-medium'">
                                        {{ item.title }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-muted-foreground">
                                {{ item.route_or_url || '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <Badge v-if="item.permission_name" variant="secondary" class="font-mono text-[11px]">
                                    {{ item.permission_name }}
                                </Badge>
                                <span v-else class="text-xs text-muted-foreground italic">Semua Role</span>
                            </td>
                            <td class="px-6 py-4">
                                <Badge :variant="item.is_active ? 'default' : 'outline'">
                                    {{ item.is_active ? 'Aktif' : 'Nonaktif' }}
                                </Badge>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <Button variant="ghost" size="sm" @click="openEditModal(item)">
                                        <Pencil class="h-4 w-4" />
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="text-destructive hover:bg-destructive/10 hover:text-destructive"
                                        @click="confirmDelete(item)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <PaginationControls :pagination="menus" />
        </div>

        <!-- Modal Tambah/Edit Menu -->
        <Dialog :open="isModalOpen" @update:open="isModalOpen = $event">
            <DialogContent class="max-w-[calc(100%-2rem)] sm:max-w-[440px] p-5">
                <DialogHeader class="space-y-1">
                    <DialogTitle class="text-base font-semibold">{{ editingMenu ? 'Edit Menu' : 'Tambah Menu Baru' }}</DialogTitle>
                    <DialogDescription class="text-xs text-muted-foreground">
                        Konfigurasi menu sidebar dan hubungkan dengan hak akses permission.
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submit" class="space-y-3 pt-1">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <Label for="title" class="text-xs">Judul Menu</Label>
                            <Input id="title" v-model="form.title" placeholder="e.g. Mahasiswa" class="h-9 text-xs" required />
                            <InputError :message="form.errors.title" />
                        </div>

                        <div class="space-y-1.5">
                            <Label for="category" class="text-xs">Kategori Menu</Label>
                            <Input
                                id="category"
                                v-model="form.category"
                                placeholder="e.g. Master Data"
                                class="h-9 text-xs"
                                :disabled="form.parent_id !== null"
                            />
                            <InputError :message="form.errors.category" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <Label for="parent_id" class="text-xs">Parent Menu</Label>
                            <select
                                id="parent_id"
                                v-model="form.parent_id"
                                class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-xs shadow-xs transition-colors focus-visible:outline-hidden focus-visible:ring-1 focus-visible:ring-ring"
                            >
                                <option :value="null">-- Menu Utama --</option>
                                <option
                                    v-for="p in parentMenus"
                                    :key="p.id"
                                    :value="p.id"
                                    :disabled="editingMenu?.id === p.id"
                                >
                                    {{ p.title }}
                                </option>
                            </select>
                            <InputError :message="form.errors.parent_id" />
                        </div>

                        <div class="space-y-1.5">
                            <Label for="order" class="text-xs">Urutan (Order)</Label>
                            <Input id="order" type="number" v-model="form.order" class="h-9 text-xs" required />
                            <InputError :message="form.errors.order" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <Label for="route_or_url" class="text-xs">Route Name / URL</Label>
                            <Input id="route_or_url" v-model="form.route_or_url" placeholder="e.g. users.index" class="h-9 text-xs" />
                            <InputError :message="form.errors.route_or_url" />
                        </div>

                        <div class="space-y-1.5">
                            <Label for="icon" class="text-xs">Pilih Icon</Label>
                            <IconPicker v-model="form.icon" />
                            <InputError :message="form.errors.icon" />
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <Label for="permission_name" class="text-xs">Syarat Permission</Label>
                        <select
                            id="permission_name"
                            v-model="form.permission_name"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-xs shadow-xs transition-colors focus-visible:outline-hidden focus-visible:ring-1 focus-visible:ring-ring"
                        >
                            <option :value="null">-- Bebas (Semua Role) --</option>
                            <option v-for="perm in permissions" :key="perm.id" :value="perm.name">
                                {{ perm.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.permission_name" />
                    </div>

                    <div class="flex items-center gap-2 pt-1">
                        <Checkbox
                            id="is_active"
                            :checked="form.is_active"
                            @update:checked="form.is_active = $event"
                        />
                        <Label for="is_active" class="cursor-pointer text-xs">Status Menu Aktif</Label>
                    </div>

                    <DialogFooter class="mt-4 pt-2 border-t border-sidebar-border">
                        <DialogClose as-child>
                            <Button type="button" variant="outline" size="sm" class="h-8 text-xs">Batal</Button>
                        </DialogClose>
                        <Button type="submit" size="sm" class="h-8 text-xs" :disabled="form.processing">Simpan</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Pop-up Konfirmasi Hapus -->
        <ConfirmDeleteModal
            :open="isDeleteModalOpen"
            :title="`Hapus Menu '${menuToDelete?.title}'?`"
            message="Apakah Anda yakin ingin menghapus menu ini? Jika menu ini memiliki sub-menu, sub-menu tersebut juga akan ikut terhapus."
            :loading="deleteForm.processing"
            @update:open="isDeleteModalOpen = $event"
            @confirm="handleDelete"
        />
    </div>
</template>
