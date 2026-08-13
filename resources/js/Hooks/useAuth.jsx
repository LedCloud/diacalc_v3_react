// hooks/useAuth.js
import {usePage} from "@inertiajs/react";

export function useAuth() {
    // Если используете Inertia.js:
    const { auth } = usePage().props;
    const userPermissions = auth.user?.permissions || [];

    // Если используете window.User:
    //const userPermissions = window.User?.permissions || [];

    const hasAccess = (permission) => {
        return userPermissions.includes(permission);
    };

    return { hasAccess };
}
