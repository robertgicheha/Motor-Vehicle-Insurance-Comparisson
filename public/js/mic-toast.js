/*
 * Shared toast helper used by every quote form (homepage, frontend, dashboard).
 * Delegates to the app-wide Alpine toast (components/toast.blade.php) when it is
 * present on the page, so styling always matches the rest of the system
 * (dark green success / dark red error, white text). Falls back to a
 * self-contained toast with identical styling on pages that don't load Alpine
 * (e.g. the standalone motor-quote/motor-psv/motor-private pages).
 */
(function (global) {
    var CONTAINER_ID = 'mic-toast-container';

    function ensureContainer() {
        var el = document.getElementById(CONTAINER_ID);
        if (!el) {
            el = document.createElement('div');
            el.id = CONTAINER_ID;
            el.style.cssText = 'position:fixed;top:80px;right:24px;z-index:100000;display:flex;flex-direction:column;gap:8px;max-width:360px;width:calc(100% - 48px);pointer-events:none;';
            document.body.appendChild(el);
        }
        return el;
    }

    function showFallback(message, type, duration) {
        var container = ensureContainer();
        var bg = type === 'error' ? '#991b1b' : '#065f46';
        var toast = document.createElement('div');
        toast.style.cssText = 'pointer-events:auto;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,.15);padding:12px 16px;font-size:12px;font-weight:600;line-height:1.4;color:#fff;background:' + bg + ';opacity:0;transform:translateY(-6px);transition:opacity .2s ease,transform .2s ease;';
        toast.textContent = message;
        container.appendChild(toast);
        requestAnimationFrame(function () {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        });
        setTimeout(function () {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-6px)';
            setTimeout(function () {
                toast.remove();
            }, 200);
        }, duration);
    }

    function show(message, type, duration) {
        if (!message) return;
        duration = duration || 2000;
        if (global._toastManager && typeof global._toastManager.addToast === 'function') {
            global._toastManager.addToast(message, type, false, duration);
            return;
        }
        showFallback(message, type, duration);
    }

    global.micToastSuccess = function (message, duration) {
        show(message, 'success', duration);
    };
    global.micToastError = function (message, duration) {
        show(message, 'error', duration);
    };

    // For flows that redirect to a new page before the toast would be seen
    // (e.g. after a quote is generated and the browser navigates to the
    // result page): stash the message and show it once the next page loads.
    global.micFlagFlashToast = function (message, type, duration) {
        try {
            sessionStorage.setItem('mic_flash_toast', JSON.stringify({
                message: message,
                type: type || 'success',
                duration: duration || 2000
            }));
        } catch (e) {}
    };

    document.addEventListener('DOMContentLoaded', function () {
        try {
            var raw = sessionStorage.getItem('mic_flash_toast');
            if (raw) {
                sessionStorage.removeItem('mic_flash_toast');
                var data = JSON.parse(raw);
                if (data && data.message) show(data.message, data.type || 'success', data.duration || 2000);
            }
        } catch (e) {}
    });
})(window);
