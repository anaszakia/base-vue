<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { LogOut, Settings } from '@lucide/vue';
import { ref } from 'vue';
import ConfirmDeleteModal from '@/components/ConfirmDeleteModal.vue';
import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import UserInfo from '@/components/UserInfo.vue';
import { logout } from '@/routes';
import { edit } from '@/routes/profile';
import type { User } from '@/types';

type Props = {
    user: User;
};

defineProps<Props>();

const isLogoutModalOpen = ref(false);
const isLoggingOut = ref(false);

const openLogoutConfirmation = () => {
    isLogoutModalOpen.value = true;
};

const proceedLogout = () => {
    isLoggingOut.value = true;
    router.flushAll();
    router.post(logout());
};
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            <UserInfo :user="user" :show-email="true" />
        </div>
    </DropdownMenuLabel>
    <DropdownMenuSeparator />
    <DropdownMenuGroup>
        <DropdownMenuItem :as-child="true">
            <Link class="block w-full cursor-pointer" :href="edit()" prefetch>
                <Settings class="mr-2 h-4 w-4" />
                Settings
            </Link>
        </DropdownMenuItem>
    </DropdownMenuGroup>
    <DropdownMenuSeparator />
    <DropdownMenuItem
        class="cursor-pointer text-destructive focus:bg-destructive/10 focus:text-destructive"
        @select.prevent="openLogoutConfirmation"
        data-test="logout-button"
    >
        <LogOut class="mr-2 h-4 w-4" />
        Log out
    </DropdownMenuItem>

    <!-- Pop-up Konfirmasi Logout -->
    <ConfirmDeleteModal
        :open="isLogoutModalOpen"
        title="Konfirmasi Keluar Akun"
        message="Apakah Anda yakin ingin keluar dari sistem? Sesi Anda saat ini akan diakhiri."
        confirm-text="Ya, Keluar Akun"
        cancel-text="Batal"
        :loading="isLoggingOut"
        @update:open="isLogoutModalOpen = $event"
        @confirm="proceedLogout"
    />
</template>
