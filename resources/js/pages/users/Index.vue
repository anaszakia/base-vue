<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, Search, Trash2, UserPlus, Users } from '@lucide/vue';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import ConfirmDeleteModal from '@/components/ConfirmDeleteModal.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
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

interface Role {
    id: number;
    name: string;
}

interface UserItem {
    id: number;
    name: string;
    email: string;
    created_at: string;
    roles: Role[];
}

interface PaginatedUsers {
    data: UserItem[];
    current_page: number;
    last_page: number;
    total: number;
    prev_page_url: string | null;
    next_page_url: string | null;
}

const props = defineProps<{
    users: PaginatedUsers;
    roles: Role[];
    filters: {
        search: string | null;
    };
}>();

const searchQuery = ref(props.filters.search ?? '');
const isModalOpen = ref(false);
const editingUser = ref<UserItem | null>(null);
const isDeleteModalOpen = ref(false);
const userToDelete = ref<UserItem | null>(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
    roles: [] as string[],
});

const handleSearch = () => {
    router.get(
        '/users',
        { search: searchQuery.value },
        { preserveState: true, replace: true }
    );
};

const openCreateModal = () => {
    editingUser.value = null;
    form.reset();
    form.clearErrors();
    form.roles = [];
    isModalOpen.value = true;
};

const openEditModal = (user: UserItem) => {
    editingUser.value = user;
    form.clearErrors();
    form.name = user.name;
    form.email = user.email;
    form.password = '';
    form.roles = user.roles.map((r) => r.name);
    isModalOpen.value = true;
};

const submit = () => {
    if (editingUser.value) {
        form.put(`/users/${editingUser.value.id}`, {
            onSuccess: () => {
                isModalOpen.value = false;
                toast.success('Data pengguna berhasil diperbarui!');
            },
            onError: () => {
                toast.error('Gagal memperbarui pengguna. Periksa kembali form.');
            },
        });
    } else {
        form.post('/users', {
            onSuccess: () => {
                isModalOpen.value = false;
                toast.success('Pengguna baru berhasil ditambahkan!');
            },
            onError: () => {
                toast.error('Gagal menambahkan pengguna. Periksa kembali form.');
            },
        });
    }
};

const deleteForm = useForm({});

const confirmDelete = (user: UserItem) => {
    userToDelete.value = user;
    isDeleteModalOpen.value = true;
};

const handleDelete = () => {
    if (!userToDelete.value) {
        return;
    }

    deleteForm.delete(`/users/${userToDelete.value.id}`, {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
            userToDelete.value = null;
            toast.success('Pengguna berhasil dihapus!');
        },
        onError: () => {
            toast.error('Gagal menghapus pengguna.');
        },
    });
};

const formatDate = (dateStr: string) => {
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};
</script>

<template>
    <Head title="Manajemen Pengguna" />

    <div class="flex h-full flex-1 flex-col gap-6 p-6">
        <!-- Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Manajemen Pengguna</h1>
                <p class="text-sm text-muted-foreground">
                    Kelola data pengguna, penetapan role, dan akses sistem.
                </p>
            </div>

            <Button @click="openCreateModal" class="w-fit">
                <UserPlus class="mr-2 h-4 w-4" />
                Tambah Pengguna
            </Button>
        </div>

        <!-- Filter & Search Bar -->
        <div class="flex items-center justify-between gap-4">
            <div class="relative w-full max-w-sm">
                <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                <Input
                    v-model="searchQuery"
                    type="search"
                    placeholder="Cari nama atau email..."
                    class="pl-9"
                    @keyup.enter="handleSearch"
                />
            </div>
            <div class="text-xs text-muted-foreground">
                Total: <span class="font-semibold text-foreground">{{ users.total }}</span> pengguna
            </div>
        </div>

        <!-- Table -->
        <div class="rounded-xl border border-sidebar-border bg-card shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-sidebar-border bg-muted/40 font-medium text-muted-foreground">
                        <tr>
                            <th class="px-6 py-3.5">Nama & Email</th>
                            <th class="px-6 py-3.5">Peran (Role)</th>
                            <th class="px-6 py-3.5">Terdaftar</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sidebar-border">
                        <tr v-for="user in users.data" :key="user.id" class="hover:bg-muted/30">
                            <td class="px-6 py-4">
                                <div class="font-medium">{{ user.name }}</div>
                                <div class="text-xs text-muted-foreground font-mono">{{ user.email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    <Badge
                                        v-for="role in user.roles"
                                        :key="role.id"
                                        variant="secondary"
                                        class="font-normal text-xs"
                                    >
                                        {{ role.name }}
                                    </Badge>
                                    <span v-if="user.roles.length === 0" class="text-xs text-muted-foreground italic">
                                        Tanpa Role
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs text-muted-foreground font-mono">
                                {{ formatDate(user.created_at) }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <Button variant="ghost" size="sm" @click="openEditModal(user)">
                                        <Pencil class="h-4 w-4" />
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="text-destructive hover:bg-destructive/10 hover:text-destructive"
                                        @click="confirmDelete(user)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td colspan="4" class="px-6 py-8 text-center text-muted-foreground">
                                Tidak ada data pengguna yang ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="users.last_page > 1"
                class="flex items-center justify-between border-t border-sidebar-border px-6 py-3 text-xs text-muted-foreground"
            >
                <span>Halaman {{ users.current_page }} dari {{ users.last_page }}</span>
                <div class="flex items-center gap-2">
                    <Button
                        variant="outline"
                        size="sm"
                        :disabled="!users.prev_page_url"
                        @click="router.get(users.prev_page_url!)"
                    >
                        Sebelumnya
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        :disabled="!users.next_page_url"
                        @click="router.get(users.next_page_url!)"
                    >
                        Selanjutnya
                    </Button>
                </div>
            </div>
        </div>

        <!-- Modal Tambah/Edit Pengguna -->
        <Dialog :open="isModalOpen" @update:open="isModalOpen = $event">
            <DialogContent class="max-w-[calc(100%-2rem)] sm:max-w-[440px] p-5">
                <DialogHeader class="space-y-1">
                    <DialogTitle class="text-base font-semibold">{{ editingUser ? 'Edit Data Pengguna' : 'Tambah Pengguna Baru' }}</DialogTitle>
                    <DialogDescription class="text-xs text-muted-foreground">
                        Isi informasi pengguna dan tentukan peran (role) yang diberikan.
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submit" class="space-y-3.5 pt-1">
                    <div class="space-y-1.5">
                        <Label for="name" class="text-xs">Nama Lengkap</Label>
                        <Input id="name" v-model="form.name" placeholder="John Doe" class="h-9 text-xs" required />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="space-y-1.5">
                        <Label for="email" class="text-xs">Alamat Email</Label>
                        <Input id="email" type="email" v-model="form.email" placeholder="john@example.com" class="h-9 text-xs" required />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="space-y-1.5">
                        <Label for="password" class="text-xs">
                            Kata Sandi
                            <span v-if="editingUser" class="text-[11px] text-muted-foreground font-normal">(Kosongkan jika tidak ingin diubah)</span>
                        </Label>
                        <PasswordInput
                            id="password"
                            v-model="form.password"
                            class="h-9 text-xs"
                            :placeholder="editingUser ? '••••••••' : 'Minimal 8 karakter'"
                            :required="!editingUser"
                            show-password-label="Tampilkan kata sandi"
                            hide-password-label="Sembunyikan kata sandi"
                        />
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="space-y-1.5 pt-1">
                        <Label class="text-xs">Pilih Peran (Role)</Label>
                        <div class="grid grid-cols-2 gap-2 rounded-lg border border-sidebar-border p-2.5 max-h-36 overflow-y-auto bg-muted/20">
                            <label
                                v-for="role in roles"
                                :key="role.id"
                                class="flex items-center gap-2 rounded p-1.5 hover:bg-muted cursor-pointer text-xs select-none"
                            >
                                <input
                                    type="checkbox"
                                    :id="`user-role-${role.id}`"
                                    :value="role.name"
                                    v-model="form.roles"
                                    class="size-4 rounded border-input text-emerald-600 focus:ring-emerald-500/20 cursor-pointer accent-emerald-600"
                                />
                                <span class="truncate font-medium">{{ role.name }}</span>
                            </label>
                        </div>
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
            :title="`Hapus Pengguna '${userToDelete?.name}'?`"
            message="Apakah Anda yakin ingin menghapus akun pengguna ini? Tindakan ini tidak dapat dibatalkan."
            :loading="deleteForm.processing"
            @update:open="isDeleteModalOpen = $event"
            @confirm="handleDelete"
        />
    </div>
</template>
