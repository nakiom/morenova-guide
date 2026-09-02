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

function initHomeIntro() {
    const root = document.querySelector('[data-home-intro]');
    const video = root?.querySelector('.home-background-video');
    if (!root || !video) return;

    const started = performance.now();
    let revealed = false;

    const reveal = () => {
        if (revealed) return;
        revealed = true;
        const wait = Math.max(0, 850 - (performance.now() - started));
        window.setTimeout(() => root.classList.add('is-ready'), wait);
    };

    if (video.readyState >= 3) {
        reveal();
        return;
    }

    video.addEventListener('canplay', reveal, { once: true });
    video.addEventListener('playing', reveal, { once: true });
    video.addEventListener('error', reveal, { once: true });
    window.setTimeout(reveal, 2800);
}

function initPageShell() {
    document.querySelectorAll('main .container').forEach((shell) => {
        if (shell.closest('.home-video-container')) {
            return;
        }
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
    initActiveNavLinks();
    initAlertDismiss();
    initPageShell();
    initHomeIntro();
    initInputMasks();
    relocateModalsToBody();
});
