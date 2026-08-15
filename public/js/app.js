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
            if (appSidebar) appSidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });
    }

    // Auto-close mobile navbar on clicking in-page anchor links
    const navbarCollapse = document.getElementById('navbarContent');
    if (navbarCollapse) {
        const navLinks = navbarCollapse.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                const href = link.getAttribute('href');
                if (href && (href.startsWith('#') || href.includes('#'))) {
                    const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                    if (bsCollapse && window.innerWidth < 992) {
                        bsCollapse.hide();
                    }
                }
            });
        });
    }

    // 2. Animated Counter for Landing Page Statistics with IntersectionObserver
    const statCounters = document.querySelectorAll('.counter-val');
    if (statCounters.length > 0) {
        const animateCounter = (counter) => {
            const target = +counter.getAttribute('data-target');
            let count = 0;
            const duration = 1200; // ms
            const steps = 40;
            const increment = target / steps;
            const stepTime = duration / steps;

            const timer = setInterval(() => {
                count += increment;
                if (count >= target) {
                    counter.innerText = target.toLocaleString();
                    clearInterval(timer);
                } else {
                    counter.innerText = Math.ceil(count).toLocaleString();
                }
            }, stepTime);
        };

        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries, obs) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounter(entry.target);
                        obs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.2 });

            statCounters.forEach(counter => observer.observe(counter));
        } else {
            statCounters.forEach(counter => animateCounter(counter));
        }
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
            try {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            } catch (e) {
                // Ignore if already dismissed
            }
        }, 5000);
    });
});
