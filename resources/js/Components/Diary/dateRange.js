export const DEFAULT_RANGE_DAYS = 7;

export function toDateInputValue(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

export function clampDateRange(start, end) {
    if (start && end && start > end) {
        return { start: end, end };
    }
    return { start, end };
}

export function defaultDateRange(days = DEFAULT_RANGE_DAYS) {
    const offset = Number.isFinite(days) && days >= 1 ? Math.round(days) : DEFAULT_RANGE_DAYS;
    const end = new Date();
    const start = new Date();
    start.setDate(start.getDate() - offset);

    return {
        start: toDateInputValue(start),
        end: toDateInputValue(end),
    };
}
