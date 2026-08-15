/**
 * SIWES Application Core JavaScript Helpers
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Mobile Sidebar Drawer Toggle
    const sidebarToggleBtn = document.getElementById('sidebarToggle');
    const appSidebar = document.querySelector('.app-sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (sidebarToggleBtn && appSidebar) {
        sidebarToggleBtn.addEventListener('click', () => {
            appSidebar.classList.toggle('show');
            if (sidebarOverlay) sidebarOverlay.classList.toggle('show');
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', () => {
            appSidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });
    }

    // 2. Animated Counter for Landing Page Statistics
    const statCounters = document.querySelectorAll('.counter-val');
    if (statCounters.length > 0) {
        statCounters.forEach(counter => {
            const target = +counter.getAttribute('data-target');
            let count = 0;
            const speed = target / 50;
            const updateCount = () => {
                count += speed;
                if (count < target) {
                    counter.innerText = Math.ceil(count);
                    setTimeout(updateCount, 30);
                } else {
                    counter.innerText = target;
                }
            };
            updateCount();
        });
    }

    // 3. Password Toggle Visibility
    const togglePasswordBtns = document.querySelectorAll('.toggle-password');
    togglePasswordBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    btn.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
                } else {
                    input.type = 'password';
                    btn.innerHTML = '<i class="fa-solid fa-eye"></i>';
                }
            }
        });
    });

    // 4. Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
