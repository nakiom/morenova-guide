/**
 * Interacciones globales de UI: entrada de página, navbar, reveals escalonados.
 */

function initNavbarScroll() {
    const navbar = document.querySelector('.app-navbar');
    if (!navbar) return;

    const onScroll = () => {
        navbar.classList.toggle('navbar-scrolled', window.scrollY > 8);
    };

    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
}

function initStaggerReveal() {
    document.querySelectorAll('[data-stagger]').forEach((container) => {
        const delay = Number(container.dataset.staggerDelay || 70);
        const children = container.querySelectorAll(':scope > *');

        children.forEach((child, index) => {
            child.classList.add('reveal-item');
            child.style.setProperty('--reveal-delay', `${index * delay}ms`);
        });
    });
}

function initActiveNavLinks() {
    const current = window.location.pathname.replace(/\/$/, '') || '/';

    document.querySelectorAll('.app-navbar .nav-link[href]').forEach((link) => {
        const href = link.getAttribute('href');
        if (!href || href.startsWith('?') || href.startsWith('#')) return;

        try {
            const path = new URL(link.href).pathname.replace(/\/$/, '') || '/';
            if (path === current || (path !== '/' && current.startsWith(path))) {
                link.classList.add('active');
            }
        } catch {
            // ignore malformed href
        }
    });
}

function initAlertDismiss() {
    document.querySelectorAll('.alert-dismissible').forEach((alert) => {
        alert.addEventListener('closed.bs.alert', () => {
            alert.remove();
        });
    });
}

function initPageShell() {
    document.querySelectorAll('main .container, main .home-video-container').forEach((shell) => {
        shell.classList.add('page-shell');
    });
}

function relocateModalsToBody() {
    // Evita que transform/overflow de contenedores atrapen position:fixed del modal.
    document.querySelectorAll('.modal').forEach((modal) => {
        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });
}

function initInputMasks() {
    document.querySelectorAll('[data-mask="phone"]').forEach((input) => {
        input.addEventListener('input', () => {
            const digits = input.value.replace(/\D/g, '').slice(0, 15);
            if (!digits) {
                input.value = '';
                return;
            }

            let formatted = digits;
            if (digits.length > 2) {
                const country = digits.slice(0, 2);
                const rest = digits.slice(2);
                const chunks = rest.match(/.{1,4}/g) || [];
                formatted = `+${country} ${chunks.join(' ')}`;
            } else {
                formatted = `+${digits}`;
            }
            input.value = formatted.trim();
        });
    });

    document.querySelectorAll('[data-mask="serial"]').forEach((input) => {
        input.addEventListener('input', () => {
            input.value = input.value
                .toUpperCase()
                .replace(/[^A-Z0-9\-]/g, '')
                .slice(0, 40);
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.body.classList.add('app-ready');
    initNavbarScroll();
    initStaggerReveal();
    initActiveNavLinks();
    initAlertDismiss();
    initPageShell();
    initInputMasks();
    relocateModalsToBody();
});
