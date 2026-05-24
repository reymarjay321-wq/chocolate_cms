/**
 * Chocolate Management System — Main JavaScript
 * Handles: sidebar, theme, search, sort, pagination, image preview, toasts, confirms
 */

document.addEventListener('DOMContentLoaded', () => {

    // ── Page Loader ──────────────────────────────────
    const loader = document.getElementById('pageLoader');
    if (loader) {
        setTimeout(() => loader.classList.add('hidden'), 600);
    }

    // ── Sidebar Toggle ───────────────────────────────
    const sidebar   = document.getElementById('sidebar');
    const overlay   = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');

    function openSidebar()  { sidebar.classList.add('open'); overlay.classList.add('open'); }
    function closeSidebar() { sidebar.classList.remove('open'); overlay.classList.remove('open'); }

    if (toggleBtn) toggleBtn.addEventListener('click', () =>
        sidebar.classList.contains('open') ? closeSidebar() : openSidebar()
    );
    if (overlay) overlay.addEventListener('click', closeSidebar);

    // ── Theme Toggle ─────────────────────────────────
    const html      = document.documentElement;
    const themeBtn  = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const saved     = localStorage.getItem('chocoTheme') || 'dark';

    html.setAttribute('data-theme', saved);
    syncThemeIcon(saved);

    if (themeBtn) {
        themeBtn.addEventListener('click', () => {
            const cur  = html.getAttribute('data-theme');
            const next = cur === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('chocoTheme', next);
            syncThemeIcon(next);
        });
    }

    function syncThemeIcon(theme) {
        if (!themeIcon) return;
        themeIcon.className = theme === 'dark' ? 'fa-solid fa-moon' : 'fa-solid fa-sun';
    }

    // ── Flash Toast ──────────────────────────────────
    const flashEl = document.getElementById('flashToast');
    if (flashEl) {
        const type = flashEl.dataset.type;
        const msg  = flashEl.dataset.msg;
        showToast(type, msg);
    }

    function showToast(type, message) {
        const icons = { success: 'fa-circle-check', error: 'fa-circle-xmark', warning: 'fa-triangle-exclamation', info: 'fa-circle-info' };
        const colors = { success: '#3ecf66', error: '#ff6464', warning: '#ffb347', info: '#64b4ff' };

        Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            background: 'var(--bg-card)',
            color: 'var(--text-primary)',
            iconColor: colors[type] || colors.info,
        }).fire({
            icon: type === 'success' ? 'success' : (type === 'error' ? 'error' : (type === 'warning' ? 'warning' : 'info')),
            title: message,
        });
    }

    // ── Live Search ──────────────────────────────────
    const searchInput  = document.getElementById('searchInput');
    const tableBody    = document.getElementById('chocoTableBody');
    const allRows      = tableBody ? Array.from(tableBody.querySelectorAll('tr[data-row]')) : [];

    // Filter selects
    const catFilter   = document.getElementById('catFilter');
    const brandFilter = document.getElementById('brandFilter');

    let currentPage   = 1;
    const perPage     = 8;
    let filteredRows  = [...allRows];
    let sortCol       = -1;
    let sortDir       = 1;

    function applyFilters() {
        const q     = searchInput  ? searchInput.value.toLowerCase().trim() : '';
        const cat   = catFilter    ? catFilter.value.toLowerCase()   : '';
        const brand = brandFilter  ? brandFilter.value.toLowerCase() : '';

        filteredRows = allRows.filter(row => {
            const text      = row.textContent.toLowerCase();
            const rowCat    = (row.dataset.category || '').toLowerCase();
            const rowBrand  = (row.dataset.brand    || '').toLowerCase();
            const matchQ     = !q     || text.includes(q);
            const matchCat   = !cat   || rowCat.includes(cat);
            const matchBrand = !brand || rowBrand.includes(brand);
            return matchQ && matchCat && matchBrand;
        });

        currentPage = 1;
        renderTable();
    }

    function renderTable() {
        if (!tableBody) return;

        const start   = (currentPage - 1) * perPage;
        const end     = start + perPage;
        const paged   = filteredRows.slice(start, end);
        const total   = filteredRows.length;

        // Hide all, show paged
        allRows.forEach(r => r.style.display = 'none');
        paged.forEach((r, i) => {
            r.style.display = '';
            r.style.animationDelay = (i * 0.04) + 's';
        });

        // Empty state
        const empty = document.getElementById('emptyState');
        if (empty) empty.style.display = total === 0 ? '' : 'none';

        // Pagination info
        const pInfo = document.getElementById('paginationInfo');
        if (pInfo) {
            const s = total === 0 ? 0 : start + 1;
            const e = Math.min(end, total);
            pInfo.textContent = `Showing ${s}–${e} of ${total} chocolates`;
        }

        buildPagination(total);
    }

    function buildPagination(total) {
        const wrap = document.getElementById('paginationBtns');
        if (!wrap) return;
        const pages = Math.ceil(total / perPage);
        wrap.innerHTML = '';

        if (pages <= 1) return;

        const mkBtn = (label, page, disabled, active) => {
            const b = document.createElement('button');
            b.className = 'page-btn' + (active ? ' active' : '');
            b.innerHTML = label;
            b.disabled  = disabled;
            if (!disabled) b.addEventListener('click', () => { currentPage = page; renderTable(); });
            return b;
        };

        wrap.appendChild(mkBtn('<i class="fa-solid fa-chevron-left"></i>', currentPage - 1, currentPage === 1));

        const range = [];
        for (let i = 1; i <= pages; i++) {
            if (i === 1 || i === pages || (i >= currentPage - 1 && i <= currentPage + 1)) range.push(i);
            else if (range[range.length - 1] !== '…') range.push('…');
        }
        range.forEach(p => {
            if (p === '…') {
                const s = document.createElement('button');
                s.className = 'page-btn'; s.textContent = '…'; s.disabled = true;
                wrap.appendChild(s);
            } else {
                wrap.appendChild(mkBtn(p, p, false, p === currentPage));
            }
        });

        wrap.appendChild(mkBtn('<i class="fa-solid fa-chevron-right"></i>', currentPage + 1, currentPage === pages));
    }

    if (searchInput)  searchInput.addEventListener('input', applyFilters);
    if (catFilter)    catFilter.addEventListener('change', applyFilters);
    if (brandFilter)  brandFilter.addEventListener('change', applyFilters);

    // ── Column Sort ──────────────────────────────────
    document.querySelectorAll('.sortable').forEach(th => {
        th.addEventListener('click', () => {
            const col = parseInt(th.dataset.col);
            if (sortCol === col) sortDir *= -1;
            else { sortCol = col; sortDir = 1; }

            document.querySelectorAll('.sortable').forEach(h => h.querySelector('.sort-icon')?.remove());
            const icon = document.createElement('i');
            icon.className = 'fa-solid sort-icon ' + (sortDir === 1 ? 'fa-sort-up' : 'fa-sort-down');
            th.appendChild(icon);

            filteredRows.sort((a, b) => {
                const aT = a.children[col]?.textContent.trim() || '';
                const bT = b.children[col]?.textContent.trim() || '';
                const aN = parseFloat(aT.replace(/[^\d.]/g, ''));
                const bN = parseFloat(bT.replace(/[^\d.]/g, ''));
                if (!isNaN(aN) && !isNaN(bN)) return (aN - bN) * sortDir;
                return aT.localeCompare(bT) * sortDir;
            });

            currentPage = 1;
            filteredRows.forEach(r => tableBody.appendChild(r));
            renderTable();
        });
    });

    // Initial table render
    renderTable();

    // ── Image Preview ─────────────────────────────────
    const fileInput  = document.getElementById('imgFile');
    const imgPreview = document.getElementById('imgPreview');

    if (fileInput && imgPreview) {
        fileInput.addEventListener('change', () => {
            const file = fileInput.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => {
                    imgPreview.src = e.target.result;
                    imgPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Drag-over styling for upload zone
    const uploadZone = document.querySelector('.upload-zone');
    if (uploadZone) {
        uploadZone.addEventListener('dragover', e => { e.preventDefault(); uploadZone.classList.add('dragover'); });
        uploadZone.addEventListener('dragleave', () => uploadZone.classList.remove('dragover'));
        uploadZone.addEventListener('drop', e => {
            e.preventDefault();
            uploadZone.classList.remove('dragover');
            if (fileInput && e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });
    }

    // ── Delete Single Confirm ─────────────────────────
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            const href = btn.dataset.href || btn.href;
            const name = btn.dataset.name || 'this chocolate';
            Swal.fire({
                title: 'Delete Chocolate?',
                html: `<span style="color:var(--text-muted)">This will permanently remove <strong style="color:#ff8080">${name}</strong> from the system.</span>`,
                icon: 'warning',
                background: '#1a0a00',
                color: '#f5e6d3',
                iconColor: '#ff8080',
                showCancelButton: true,
                confirmButtonColor: '#c8522d',
                cancelButtonColor: '#3d2010',
                confirmButtonText: '<i class="fa-solid fa-trash"></i> Yes, Delete',
                cancelButtonText: 'Cancel',
                borderRadius: '14px',
            }).then(result => {
                if (result.isConfirmed) window.location.href = href;
            });
        });
    });

    // ── Delete ALL Confirm ────────────────────────────
    const deleteAllBtn = document.getElementById('deleteAllBtn');
    if (deleteAllBtn) {
        deleteAllBtn.addEventListener('click', () => {
            Swal.fire({
                title: 'Delete ALL Chocolates?',
                html: `<span style="color:var(--text-muted)">This will <strong style="color:#ff4040">permanently delete every chocolate</strong> in the system. This action cannot be undone.</span>`,
                icon: 'error',
                background: '#1a0a00',
                color: '#f5e6d3',
                iconColor: '#ff4040',
                showCancelButton: true,
                confirmButtonColor: '#c00',
                cancelButtonColor: '#3d2010',
                confirmButtonText: '<i class="fa-solid fa-trash-can"></i> Delete Everything',
                cancelButtonText: 'Cancel',
                input: 'text',
                inputLabel: 'Type DELETE to confirm',
                inputPlaceholder: 'DELETE',
                inputAttributes: { autocomplete: 'off' },
                preConfirm: val => {
                    if (val !== 'DELETE') {
                        Swal.showValidationMessage('You must type DELETE exactly to proceed');
                        return false;
                    }
                    return true;
                }
            }).then(result => {
                if (result.isConfirmed) window.location.href = 'delete_all.php';
            });
        });
    }

    // ── Animate stat numbers ──────────────────────────
    document.querySelectorAll('.stat-value[data-target]').forEach(el => {
        const target = parseInt(el.dataset.target);
        let current  = 0;
        const step   = Math.ceil(target / 40);
        const timer  = setInterval(() => {
            current = Math.min(current + step, target);
            el.textContent = current.toLocaleString();
            if (current >= target) clearInterval(timer);
        }, 30);
    });

});
