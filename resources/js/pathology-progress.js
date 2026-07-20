/**
 * Progreso de verificación previa a la sesión.
 * El estado vive únicamente en memoria durante la sesión de la página;
 * no se almacena en localStorage ni se sincroniza con el servidor.
 */

function calcPercent(completed, total) {
    if (!total) return 0;
    return Math.round((completed.length / total) * 100);
}

function animatePercent(el, from, to) {
    if (!el) return;
    el.classList.add('pathology-percent-bump');
    const duration = 400;
    const start = performance.now();
    const step = (now) => {
        const t = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - t, 3);
        const val = Math.round(from + (to - from) * eased);
        el.textContent = `${val}%`;
        if (t < 1) {
            requestAnimationFrame(step);
        } else {
            setTimeout(() => el.classList.remove('pathology-percent-bump'), 200);
        }
    };
    requestAnimationFrame(step);
}

function initPathologyProgress(root) {
    const checklistIds = JSON.parse(root.dataset.checklistIds || '[]').map(Number);
    const total = checklistIds.length;

    const bar = root.querySelector('.pathology-progress-bar');
    const percentEl = root.querySelector('.pathology-progress-percent');
    const countEl = root.querySelector('.pathology-progress-count');
    const checkAllBtn = root.querySelector('.pathology-progress-check-all');
    const resetBtn = root.querySelector('.pathology-progress-reset');

    let completed = [];
    let lastPercent = 0;

    function syncUI(animate = true) {
        completed = completed.filter((id) => checklistIds.includes(id));
        const percent = calcPercent(completed, total);

        if (bar) {
            bar.style.width = `${percent}%`;
            bar.setAttribute('aria-valuenow', String(percent));
        }
        if (percentEl) {
            if (animate && percent !== lastPercent) {
                animatePercent(percentEl, lastPercent, percent);
            } else {
                percentEl.textContent = `${percent}%`;
            }
        }
        if (countEl) {
            countEl.textContent = `${completed.length} / ${total}`;
        }
        lastPercent = percent;

        checklistIds.forEach((id) => {
            const btn = root.querySelector(`[data-checklist-done="${id}"]`);
            const row = root.querySelector(`[data-checklist-row="${id}"]`);
            const done = completed.includes(id);
            if (btn) {
                btn.classList.toggle('btn-success', done);
                btn.classList.toggle('btn-outline-primary', !done);
                const label = btn.dataset.labelDone;
                const labelUndo = btn.dataset.labelUndo;
                btn.innerHTML = done
                    ? `<i class="fas fa-check-circle me-1"></i>${labelUndo}`
                    : `<i class="fas fa-circle me-1"></i>${label}`;
            }
            if (row) {
                row.classList.toggle('pathology-checklist-done', done);
            }
        });

        if (checkAllBtn) {
            checkAllBtn.disabled = total === 0 || completed.length === total;
        }
    }

    function toggleItem(itemId) {
        const idx = completed.indexOf(itemId);
        if (idx >= 0) {
            completed.splice(idx, 1);
        } else {
            completed.push(itemId);
        }
        syncUI(true);
    }

    root.querySelectorAll('[data-checklist-done]').forEach((btn) => {
        btn.addEventListener('click', () => toggleItem(Number(btn.dataset.checklistDone)));
    });

    if (checkAllBtn) {
        checkAllBtn.addEventListener('click', () => {
            completed = [...checklistIds];
            syncUI(true);
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            completed = [];
            syncUI(true);
        });
    }

    syncUI(false);
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-pathology-progress]').forEach(initPathologyProgress);
});

export { calcPercent };
