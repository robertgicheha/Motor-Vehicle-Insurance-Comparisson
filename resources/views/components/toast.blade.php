@php
    $flashMessages = [];
    foreach (['success', 'error', 'warning', 'info'] as $type) {
        if (session()->has($type)) {
            $flashMessages[] = ['message' => session($type), 'type' => $type];
        }
    }
@endphp

<div
    x-data="toastManager({{ json_encode($flashMessages) }})"
    x-init="init()"
    class="fixed top-4 right-4 z-[100] flex flex-col gap-2 max-w-sm w-full pointer-events-none">
    <template x-for="(toast, index) in toasts" :key="index">
        <div
            x-show="toast.show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-8 opacity-0"
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="translate-x-8 opacity-0"
            class="pointer-events-auto w-full rounded-lg shadow-lg px-4 py-3 flex items-start gap-3"
            :class="{
                'bg-emerald-800 text-white': toast.type === 'success',
                'bg-red-800 text-white': toast.type === 'error',
                'bg-amber-700 text-white': toast.type === 'warning',
                'bg-blue-800 text-white': toast.type === 'info'
            }">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium" x-text="toast.message"></p>
            </div>
            <button @click="removeToast(index)" class="shrink-0 opacity-70 hover:opacity-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </template>
</div>

<script>
function toastManager(messages = []) {
    return {
        toasts: [],
        init() {
            var self = this;
            messages.forEach(function(msg) {
                self.addToast(msg.message, msg.type);
            });
        },
        addToast(message, type) {
            if (!message) return;
            var toast = { message: message, type: type || 'success', show: true };
            this.toasts.push(toast);
            setTimeout(() => {
                this.removeToast(this.toasts.indexOf(toast));
            }, 5000);
        },
        removeToast(index) {
            if (index >= 0 && index < this.toasts.length) {
                this.toasts[index].show = false;
                setTimeout(() => {
                    if (index >= 0 && index < this.toasts.length) {
                        this.toasts.splice(index, 1);
                    }
                }, 200);
            }
        }
    }
}
</script>
