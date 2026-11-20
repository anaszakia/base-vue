import { usePage } from '@inertiajs/vue3';
import type { Auth } from '@/types';

export function usePermission() {
    const page = usePage<{ auth: Auth }>();

    const user = () => page.props.auth?.user;

    const hasRole = (role: string | string[]): boolean => {
        const roles = user()?.roles ?? [];
        if (Array.isArray(role)) {
            return role.some((r) => roles.includes(r));
        }
        return roles.includes(role);
    };

    const hasPermission = (permission: string | string[]): boolean => {
        const permissions = user()?.permissions ?? [];
        if (Array.isArray(permission)) {
            return permission.some((p) => permissions.includes(p));
        }
        return permissions.includes(permission);
    };

    const can = (permission: string | string[]): boolean => hasPermission(permission);

    return {
        user,
        hasRole,
        hasPermission,
        can,
    };
}
