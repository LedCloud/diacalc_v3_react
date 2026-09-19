import { usePage } from '@inertiajs/react';
import {useMemo} from "react";

export function useTrans() {
    // 1. Grab the shared translations array from the Inertia page props
    const { translations } = usePage().props;
    console.log(translations);
    return useMemo(() => {
        const __ = (key, replace = {}) => {
            console.log(translations);
            if (!translations) return '_' + key;
            console.log('key', key, 'repl', replace);
            // 2. Traverse the translations object using dot notation (e.g., "auth.failed")
            let translation = key.split('.').reduce((t, i) => {
                return t && t[i] !== undefined ? t[i] : null;
            }, translations);

            console.log('Trans found =', translation);

            // 3. Fallback to the original key if no translation was found
            if (translation === null || typeof translation !== 'string') {
                console.log('Not found');
                return '_' + key;
            }

            // 4. Handle dynamic replacements (e.g., "Welcome, :name")
            Object.keys(replace).forEach((placeholder) => {
                translation = translation.replace(`:${placeholder}`, replace[placeholder]);
            });
            console.log('found =', translation);
            return translation;
        };

        return { __ };

    }, [translations]);
}
