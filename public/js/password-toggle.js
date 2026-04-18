(function () {
    function updateButton(button, isVisible) {
        var showLabel = button.getAttribute('data-show-label') || 'Show';
        var hideLabel = button.getAttribute('data-hide-label') || 'Hide';
        var showIcon = button.getAttribute('data-show-icon') || 'fa-eye';
        var hideIcon = button.getAttribute('data-hide-icon') || 'fa-eye-slash';
        var icon = button.querySelector('i');

        button.setAttribute('aria-pressed', isVisible ? 'true' : 'false');
        button.setAttribute('aria-label', isVisible ? hideLabel : showLabel);
        button.setAttribute('title', isVisible ? hideLabel : showLabel);

        if (icon) {
            icon.classList.remove(isVisible ? showIcon : hideIcon);
            icon.classList.add(isVisible ? hideIcon : showIcon);
        }

        var label = button.querySelector('[data-password-label]');
        if (label) {
            label.textContent = isVisible ? hideLabel : showLabel;
        }
    }

    document.addEventListener('click', function (event) {
        var button = event.target.closest('[data-password-toggle]');
        if (!button) {
            return;
        }

        var field = button.closest('.password-field');
        if (!field) {
            return;
        }

        var input = field.querySelector('input');
        if (!input) {
            return;
        }

        var isVisible = input.type === 'text';
        input.type = isVisible ? 'password' : 'text';
        updateButton(button, !isVisible);
    });

    document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
        var field = button.closest('.password-field');
        var input = field ? field.querySelector('input') : null;
        updateButton(button, input ? input.type === 'text' : false);
    });
})();
