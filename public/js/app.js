/**
 * SIWES Application Core JavaScript Helpers
 * Handles:
 * 1. Mobile Navbar Hamburger Toggle & Icon Switching (Bars <-> Xmark)
 * 2. Mobile Dashboard Sidebar Drawer Toggle
 * 3. Animated Counter for Landing Page Statistics
 * 4. Password Toggle Visibility
 * 5. Auto-dismissing Alerts
 */

document.addEventListener('DOMContentLoaded', () => {
    // =========================================================================
    // 1. Mobile Navigation Hamburger Menu & Icon State Manager
    // =========================================================================
    const navbarCollapse = document.getElementById('navbarContent');
    const navbarToggler = document.querySelector('.navbar-toggler');
    const menuIconBars = document.querySelector('.menu-icon-bars');
    const menuIconClose = document.querySelector('.menu-icon-close');

    if (navbarToggler && navbarCollapse) {
        // Function to synchronize icon and aria attributes
        const setMenuState = (isOpen) => {
            navbarToggler.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            if (menuIconBars && menuIconClose) {
                if (isOpen) {
                    menuIconBars.classList.add('d-none');
                    menuIconClose.classList.remove('d-none');
                } else {
                    menuIconBars.classList.remove('d-none');
                    menuIconClose.classList.add('d-none');
                }
            }
        };

        // Bootstrap Collapse Event Listeners
        navbarCollapse.addEventListener('show.bs.collapse', () => setMenuState(true));
        navbarCollapse.addEventListener('shown.bs.collapse', () => setMenuState(true));
        navbarCollapse.addEventListener('hide.bs.collapse', () => setMenuState(false));
        navbarCollapse.addEventListener('hidden.bs.collapse', () => setMenuState(false));

        // Fallback toggle handler
        navbarToggler.addEventListener('click', () => {
            setTimeout(() => {
                const isOpen = navbarCollapse.classList.contains('show') || navbarCollapse.classList.contains('active');
                setMenuState(isOpen);
            }, 60);
        });

        // Close mobile menu when an anchor link or button is clicked
        const navLinks = navbarCollapse.querySelectorAll('.nav-link, .btn');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    try {
                        if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
                            const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse) || new bootstrap.Collapse(navbarCollapse, { toggle: false });
                            bsCollapse.hide();
                        } else {
                            navbarCollapse.classList.remove('show', 'active');
                        }
                    } catch (e) {
                        navbarCollapse.classList.remove('show', 'active');
                    }
                    setMenuState(false);
                }
            });
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (event) => {
            if (window.innerWidth < 992) {
                const isInsideNav = navbarCollapse.contains(event.target) || navbarToggler.contains(event.target);
                const isOpen = navbarCollapse.classList.contains('show') || navbarCollapse.classList.contains('active');
                
                if (!isInsideNav && isOpen) {
                    try {
                        if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
                            const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                            if (bsCollapse) bsCollapse.hide();
                        } else {
                            navbarCollapse.classList.remove('show', 'active');
                        }
                    } catch (e) {
                        navbarCollapse.classList.remove('show', 'active');
                    }
                    setMenuState(false);
                }
            }
        });

        // Reset mobile menu state on window resize to desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 992) {
                try {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
                        const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                        if (bsCollapse) bsCollapse.hide();
                    }
                } catch (e) {}
                navbarCollapse.classList.remove('show', 'active');
                setMenuState(false);
            }
        });
    }

    // =========================================================================
    // 2. Mobile Dashboard Sidebar Drawer Toggle
    // =========================================================================
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

    // =========================================================================
    // 3. Animated Counter for Landing Page Statistics with IntersectionObserver
    // =========================================================================
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

    // =========================================================================
    // 4. Password Toggle Visibility
    // =========================================================================
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

    // =========================================================================
    // 5. Auto-dismiss alerts after 5 seconds
    // =========================================================================
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            try {
                if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                } else {
                    alert.remove();
                }
            } catch (e) {
                // Ignore if already closed
            }
        }, 5000);
    });
});
