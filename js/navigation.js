document.addEventListener('DOMContentLoaded', function () {
    const menuItems = document.querySelectorAll('.sidebar a, .sidebar .menu-group');
    const dropdowns = document.querySelectorAll('.sidebar-dropdown');
    const notifToggle = document.getElementById('notifToggle');
    const notifPanel = document.getElementById('notifPanel');
    const sidebarCheckbox = document.getElementById('menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const openBtn = document.querySelector('label[for="menu-toggle"].open-btn');

    function updateParentStates() {
        dropdowns.forEach(dropdown => {
            const summary = dropdown.querySelector('.menu-group');
            const childLinks = dropdown.querySelectorAll('.menu-link');
            const hasActiveChild = Array.from(childLinks).some(link => link.classList.contains('active'));

            if (summary) {
                if (hasActiveChild && !dropdown.open) {
                    summary.classList.add('active');
                } else {
                    summary.classList.remove('active');
                }
            }
        });
    }

    menuItems.forEach(item => {
        item.addEventListener('click', function (e) {
            if (this.classList.contains('menu-group')) {
                return;
            }

            const href = this.getAttribute('href');
            if (href === '#') {
                e.preventDefault();
                menuItems.forEach(link => link.classList.remove('active'));
                this.classList.add('active');
                updateParentStates();
            }
        });
    });

    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('toggle', updateParentStates);
    });

    if (notifToggle && notifPanel) {
        notifToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            notifPanel.classList.toggle('open');
        });

        notifPanel.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    }

    if (openBtn && sidebarCheckbox) {
        openBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            sidebarCheckbox.checked = !sidebarCheckbox.checked;
        });
    }

    if (sidebar) {
        sidebar.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    }

    document.addEventListener('click', function (e) {
        const clickedNotif = notifToggle && notifToggle.contains(e.target);
        const clickedNotifPanel = notifPanel && notifPanel.contains(e.target);
        if (sidebarCheckbox && sidebarCheckbox.checked && sidebar && !sidebar.contains(e.target) && (!openBtn || !openBtn.contains(e.target))) {
            sidebarCheckbox.checked = false;
        }

        if (notifPanel && notifPanel.classList.contains('open') && !clickedNotif && !clickedNotifPanel) {
            notifPanel.classList.remove('open');
        }
    });

    updateParentStates();
});