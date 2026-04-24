/**
 * Idle Timeout Handler
 * Tracks user activity and logs out after configured idle time
 */

class IdleTimeoutHandler {
    constructor(options = {}) {
        this.idleTimeout = options.idleTimeout || 15; // minutes
        this.warningTime = options.warningTime || 1; // minutes before logout
        this.warningShown = false;
        this.logoutInProgress = false;
        this.logoutTimer = null;
        this.warningTimer = null;
        this.warningCountdownTimer = null;
        this.warningDeadline = null;
        this.activityEvents = ['mousedown', 'keydown', 'scroll', 'touchstart', 'click'];

        this.init();
    }

    init() {
        this.attachActivityListeners();
        this.startIdleTimer();
    }

    attachActivityListeners() {
        this.activityEvents.forEach(event => {
            document.addEventListener(event, () => this.resetTimer(), false);
        });
    }

    startIdleTimer() {
        this.clearTimers();

        const idleMs = this.idleTimeout * 60 * 1000;
        const warningMs = (this.idleTimeout - this.warningTime) * 60 * 1000;
        const warningDelayMs = Math.max(warningMs, 0);

        // Set warning timer
        this.warningTimer = setTimeout(() => {
            this.showWarning();
        }, warningDelayMs);

        // Set logout timer
        this.logoutTimer = setTimeout(() => {
            this.logout();
        }, idleMs);
    }

    resetTimer() {
        if (this.warningShown) {
            return;
        }

        this.clearTimers();
        this.warningShown = false;
        this.startIdleTimer();
    }

    clearTimers() {
        if (this.logoutTimer) clearTimeout(this.logoutTimer);
        if (this.warningTimer) clearTimeout(this.warningTimer);
        if (this.warningCountdownTimer) clearInterval(this.warningCountdownTimer);
    }

    showWarning() {
        this.warningShown = true;

        const modal = document.getElementById('idleTimeoutModal');
        if (modal) {
            modal.classList.remove('hidden');
            this.warningDeadline = Date.now() + (Math.max(this.warningTime, 0) * 60 * 1000);
            this.updateCountdown();
            if (this.warningCountdownTimer) clearInterval(this.warningCountdownTimer);
            this.warningCountdownTimer = setInterval(() => this.updateCountdown(), 1000);

            const continueBtn = document.getElementById('continueSession');
            if (continueBtn) {
                continueBtn.onclick = () => {
                    this.closeWarning();
                    this.resetTimer();
                };
            }
        }
    }

    updateCountdown() {
        const countdown = document.getElementById('idleTimeoutCountdown');
        if (!countdown || !this.warningDeadline) {
            return;
        }

        const remainingMs = Math.max(this.warningDeadline - Date.now(), 0);
        const remainingSeconds = Math.ceil(remainingMs / 1000);
        const minutes = Math.floor(remainingSeconds / 60);
        const seconds = remainingSeconds % 60;

        countdown.textContent = `${minutes}:${String(seconds).padStart(2, '0')}`;

        if (remainingMs <= 0) {
            this.logout();
        }
    }

    closeWarning() {
        const modal = document.getElementById('idleTimeoutModal');
        if (modal) {
            modal.classList.add('hidden');
        }
        if (this.warningCountdownTimer) {
            clearInterval(this.warningCountdownTimer);
            this.warningCountdownTimer = null;
        }
        this.warningDeadline = null;
        this.warningShown = false;
    }

    logout() {
        if (this.logoutInProgress) {
            return;
        }

        this.logoutInProgress = true;
        const form = document.getElementById('logoutForm');
        if (form) {
            form.submit();
        } else {
            const fallbackForm = document.createElement('form');
            fallbackForm.method = 'POST';
            fallbackForm.action = '/logout';

            const token = document.querySelector('meta[name="csrf-token"]');
            if (token) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = '_token';
                input.value = token.getAttribute('content');
                fallbackForm.appendChild(input);
            }

            document.body.appendChild(fallbackForm);
            fallbackForm.submit();
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    new IdleTimeoutHandler({
        idleTimeout: parseInt(document.documentElement.dataset.idleTimeout || 15, 10),
        warningTime: parseInt(document.documentElement.dataset.warningTime || 1, 10)
    });
});
