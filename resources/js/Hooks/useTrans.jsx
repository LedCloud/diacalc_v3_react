import { usePage } from '@inertiajs/react';
import {useMemo} from "react";

export function useTrans() {
    // 1. Grab the shared translations array from the Inertia page props
    const { translations } = usePage().props;

    return useMemo(() => {
        const __ = (key, replace = {}) => {
            if (!translations) return '_' + key;

            // 2. Traverse the translations object using dot notation (e.g., "auth.failed")
            let translation = key.split('.').reduce((t, i) => {
                return t && t[i] !== undefined ? t[i] : null;
            }, translations);

            // 3. Fallback to the original key if no translation was found
            if (translation === null || typeof translation !== 'string') {
                return '_' + key;
            }

            // 4. Handle dynamic replacements (e.g., "Welcome, :name")
            Object.keys(replace).forEach((placeholder) => {
                translation = translation.replace(`:${placeholder}`, replace[placeholder]);
            });

            return translation;
        };

        return { __ };

    }, [translations]);
}
