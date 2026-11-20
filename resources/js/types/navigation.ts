import type { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from '@lucide/vue';

export type BreadcrumbItem = {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
};

export type NavItem = {
    id?: number;
    category?: string;
    title: string;
    href: NonNullable<InertiaLinkProps['href']> | string;
    icon?: LucideIcon | string | null;
    isActive?: boolean;
    permission_name?: string | null;
    children?: NavItem[];
};

export type NavGroup = {
    category: string;
    items: NavItem[];
};
