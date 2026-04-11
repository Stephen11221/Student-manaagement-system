/**
 * Idle Timeout Handler
 * Tracks user activity and logs out after configured idle time
 */

class IdleTimeoutHandler {
    constructor(options = {}) {
        this.idleTimeout = options.idleTimeout || 15; // minutes
        this.warningTime = options.warningTime || 1; // minutes before logout
        this.warningShown = false;
        this.logoutTimer = null;
        this.warningTimer = null;
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

        // Set warning timer
        this.warningTimer = setTimeout(() => {
            this.showWarning();
        }, warningMs);

        // Set logout timer
        this.logoutTimer = setTimeout(() => {
            this.logout();
        }, idleMs);
    }

    resetTimer() {
        if (this.warningShown) {
            return; // Don't reset if warning is already shown
        }
        
        this.clearTimers();
        this.warningShown = false;
        this.startIdleTimer();
    }

    clearTimers() {
        if (this.logoutTimer) clearTimeout(this.logoutTimer);
        if (this.warningTimer) clearTimeout(this.warningTimer);
    }

    showWarning() {
        this.warningShown = true;
        
        const modal = document.getElementById('idleTimeoutModal');
        if (modal) {
            modal.classList.remove('hidden');
            
            // Add continue button handler
            const continueBtn = document.getElementById('continueSession');
            if (continueBtn) {
                continueBtn.onclick = () => {
                    this.closeWarning();
                    this.resetTimer();
                };
            }
        }
    }

    closeWarning() {
        const modal = document.getElementById('idleTimeoutModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    logout() {
        // Submit the logout form
        const form = document.getElementById('logoutForm');
        if (form) {
            form.submit();
        } else {
            // Fallback: Create and submit a form
            const fallbackForm = document.createElement('form');
            fallbackForm.method = 'POST';
            fallbackForm.action = '/logout';
            
            // Add CSRF token
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

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const idleHandler = new IdleTimeoutHandler({
        idleTimeout: parseInt(document.documentElement.dataset.idleTimeout || 15),
        warningTime: parseInt(document.documentElement.dataset.warningTime || 1)
    });
});
