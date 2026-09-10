import {useCallback, useRef} from 'react';

const INTERACTIVE_SELECTOR = 'input, textarea, select';

export function useHorizontalSwipe(onSwipe, {threshold = 50} = {}) {
    const start = useRef(null);

    const onTouchStart = useCallback((e) => {
        if (e.target.closest?.(INTERACTIVE_SELECTOR)) {
            start.current = null;
            return;
        }
        const touch = e.touches[0];
        start.current = {x: touch.clientX, y: touch.clientY};
    }, []);

    const onTouchEnd = useCallback((e) => {
        if (!start.current) {
            return;
        }
        const touch = e.changedTouches[0];
        const dx = touch.clientX - start.current.x;
        const dy = touch.clientY - start.current.y;
        start.current = null;

        if (Math.abs(dx) < threshold || Math.abs(dx) <= Math.abs(dy)) {
            return;
        }

        onSwipe(dx < 0 ? 'right' : 'left');
    }, [onSwipe, threshold]);

    return {onTouchStart, onTouchEnd};
}
