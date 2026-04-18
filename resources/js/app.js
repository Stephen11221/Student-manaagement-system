import './bootstrap';

const SIDEBAR_STORAGE_KEY = 'school-portal.sidebar-collapsed';

function getSidebarShell() {
    const sidebar = document.querySelector('[data-app-sidebar]');
    const toggleButtons = document.querySelectorAll('[data-sidebar-toggle]');
    const overlay = document.querySelector('[data-sidebar-overlay]');

    if (!sidebar || toggleButtons.length === 0) {
        return null;
    }

    return { sidebar, toggleButtons, overlay };
}

function setupSidebar() {
    const shell = getSidebarShell();

    if (!shell) {
        return;
    }

    const { sidebar, toggleButtons, overlay } = shell;
    const body = document.body;
    const mobileQuery = window.matchMedia('(max-width: 1024px)');

    const isMobile = () => mobileQuery.matches;
    const getCollapsed = () => localStorage.getItem(SIDEBAR_STORAGE_KEY) === 'true';

    const syncToggleButtons = (expanded, open) => {
        toggleButtons.forEach((button) => {
            button.setAttribute('aria-expanded', String(expanded));
            button.setAttribute('aria-label', expanded ? 'Collapse sidebar' : 'Expand sidebar');
            button.dataset.state = open ? 'open' : expanded ? 'expanded' : 'collapsed';
        });
    };

    const closeMobile = () => {
        body.dataset.sidebarOpen = 'false';
        syncToggleButtons(false, false);
    };

    const openMobile = () => {
        body.dataset.sidebarOpen = 'true';
        syncToggleButtons(true, true);
    };

    const applyState = () => {
        const collapsed = getCollapsed();

        if (isMobile()) {
            body.dataset.sidebarCollapsed = 'false';
            body.dataset.sidebarOpen = body.dataset.sidebarOpen === 'true' ? 'true' : 'false';
            const open = body.dataset.sidebarOpen === 'true';
            syncToggleButtons(open, open);
            sidebar.setAttribute('aria-hidden', body.dataset.sidebarOpen === 'true' ? 'false' : 'true');
            return;
        }

        body.dataset.sidebarOpen = 'false';
        body.dataset.sidebarCollapsed = collapsed ? 'true' : 'false';
        sidebar.setAttribute('aria-hidden', 'false');
        syncToggleButtons(!collapsed, false);
    };

    const toggleSidebar = () => {
        if (isMobile()) {
            if (body.dataset.sidebarOpen === 'true') {
                closeMobile();
            } else {
                openMobile();
            }
            return;
        }

        const collapsed = body.dataset.sidebarCollapsed === 'true';
        const nextState = !collapsed;
        body.dataset.sidebarCollapsed = nextState ? 'true' : 'false';
        localStorage.setItem(SIDEBAR_STORAGE_KEY, String(nextState));
        syncToggleButtons(!nextState, false);
    };

    toggleButtons.forEach((button) => {
        button.addEventListener('click', toggleSidebar);
    });

    overlay?.addEventListener('click', closeMobile);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && body.dataset.sidebarOpen === 'true') {
            closeMobile();
        }
    });

    mobileQuery.addEventListener('change', applyState);
    applyState();
}

document.addEventListener('DOMContentLoaded', setupSidebar);
