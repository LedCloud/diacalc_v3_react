<div x-data
     @notify.window="$store.toasts.add($event.detail.message, $event.detail.type)"
     class="fixed top-5 right-5 space-y-2 z-50">

    <template x-for="toast in $store.toasts.items" :key="toast.id">
        <div x-transition.duration.300ms
             class="bg-white shadow-lg border-l-4 p-4 rounded"
             :class="toast.type === 'error' ? 'border-red-500' :  (toast.type === 'info' ? 'border-green-500' : 'border-amber-500')">
            <span x-text="toast.message"></span>
        </div>
    </template>

</div>
