document.addEventListener('DOMContentLoaded', () => {
    const deck = document.querySelector('[data-preso-deck]');
    if (!deck) {
        return;
    }

    const slides = [...deck.querySelectorAll('[data-preso-slide]')];
    const dots = [...document.querySelectorAll('[data-preso-dot]')];
    const progress = document.querySelector('[data-preso-progress]');
    const btnPrevList = [...document.querySelectorAll('[data-preso-prev]')];
    const btnNextList = [...document.querySelectorAll('[data-preso-next]')];
    const btnFullscreen = document.querySelector('[data-preso-fullscreen]');
    const counter = document.querySelector('[data-preso-counter]');

    let index = 0;
    let locked = false;

    const clamp = (value) => Math.max(0, Math.min(slides.length - 1, value));

    const render = (nextIndex, direction = 1) => {
        if (locked) {
            return;
        }

        nextIndex = clamp(nextIndex);
        if (nextIndex === index && slides[index].classList.contains('is-active')) {
            return;
        }

        locked = true;
        const current = slides[index];
        const next = slides[nextIndex];

        current.classList.remove('is-active');
        current.classList.add(direction >= 0 ? 'is-exit-left' : 'is-exit-right');

        next.classList.remove('is-exit-left', 'is-exit-right');
        next.classList.add('is-active');

        dots.forEach((dot, i) => {
            dot.classList.toggle('is-active', i === nextIndex);
            dot.setAttribute('aria-current', i === nextIndex ? 'true' : 'false');
        });

        if (progress) {
            progress.style.width = `${((nextIndex + 1) / slides.length) * 100}%`;
        }

        if (counter) {
            counter.textContent = `${nextIndex + 1} / ${slides.length}`;
        }

        btnPrevList.forEach((btn) => {
            btn.disabled = nextIndex === 0;
        });
        btnNextList.forEach((btn) => {
            // El CTA del hero siempre puede avanzar; el footer se deshabilita al final
            if (btn.classList.contains('preso-nav-btn')) {
                btn.disabled = nextIndex === slides.length - 1;
            }
        });

        window.setTimeout(() => {
            current.classList.remove('is-exit-left', 'is-exit-right');
            index = nextIndex;
            locked = false;
            history.replaceState(null, '', `#s${index + 1}`);
        }, 420);
    };

    const go = (delta) => render(index + delta, delta);

    btnPrevList.forEach((btn) => btn.addEventListener('click', () => go(-1)));
    btnNextList.forEach((btn) => btn.addEventListener('click', () => go(1)));

    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            const target = Number(dot.dataset.presoDot);
            render(target, target >= index ? 1 : -1);
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.target instanceof HTMLInputElement || event.target instanceof HTMLTextAreaElement) {
            return;
        }

        if (['ArrowRight', 'PageDown', ' ', 'Enter'].includes(event.key)) {
            event.preventDefault();
            go(1);
        }
        if (['ArrowLeft', 'PageUp', 'Backspace'].includes(event.key)) {
            event.preventDefault();
            go(-1);
        }
        if (event.key === 'Home') {
            event.preventDefault();
            render(0, -1);
        }
        if (event.key === 'End') {
            event.preventDefault();
            render(slides.length - 1, 1);
        }
        if (event.key.toLowerCase() === 'f') {
            event.preventDefault();
            toggleFullscreen();
        }
    });

    let touchStartX = null;
    deck.addEventListener('touchstart', (event) => {
        touchStartX = event.changedTouches[0]?.clientX ?? null;
    }, { passive: true });

    deck.addEventListener('touchend', (event) => {
        if (touchStartX === null) {
            return;
        }
        const delta = (event.changedTouches[0]?.clientX ?? touchStartX) - touchStartX;
        if (Math.abs(delta) > 50) {
            go(delta < 0 ? 1 : -1);
        }
        touchStartX = null;
    }, { passive: true });

    const toggleFullscreen = () => {
        const root = document.documentElement;
        if (!document.fullscreenElement) {
            root.requestFullscreen?.();
        } else {
            document.exitFullscreen?.();
        }
    };

    btnFullscreen?.addEventListener('click', toggleFullscreen);

    const hashIndex = Number((location.hash.match(/^#s(\d+)/) || [])[1] || 1) - 1;
    render(clamp(hashIndex), 1);
});
