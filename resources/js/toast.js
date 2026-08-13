"use strict";

document.addEventListener('alpine:init', () => {
    Alpine.store('toasts', {
        items: [],
        add(message, type = 'info') {
            const id = Date.now(); // уникальный ID
            this.items.push({ id, message, type });

            // Удаляем конкретно этот тост через 3 секунды
            setTimeout(() => {
                this.remove(id);
            }, 3000);
        },
        remove(id) {
            this.items = this.items.filter(i => i.id !== id);
        }
    });
});
