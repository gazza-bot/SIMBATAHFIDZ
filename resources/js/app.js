import './bootstrap';
import Alpine from 'alpinejs'

window.Alpine = Alpine

window.adminFilter = function (options = {}) {
    const {
        endpoint = '',
        formSelector = '[data-admin-filter]',
        debounce = 300,
        containerSelector = '[data-table-container]',
    } = options;

    let debounceTimer = null;
    let abortController = null;

    return {
        loading: false,
        error: null,
        html: '',
        showDeleteModal: false,
        deleteItem: null,

        init() {
            const container = this.$el.closest(containerSelector);
            if (!container) return;

            const form = container.querySelector(formSelector);
            if (!form) return;

            this.form = form;
            this.container = container;

            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.load();
            });

            form.querySelectorAll('select').forEach((select) => {
                select.addEventListener('change', () => {
                    this.load();
                });
            });

            const searchInput = form.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.addEventListener('input', () => {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => this.load(), debounce);
                });
            }

            window.addEventListener('popstate', () => {
                this.syncFormFromUrl();
                this.load();
            });

            this.$el.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-delete-id]');
                if (btn) {
                    e.preventDefault();
                    this.openDeleteModal(btn.dataset.deleteId, btn.dataset.deleteName);
                }
            });

            this.syncFormFromUrl();
        },

        getFormState() {
            if (!this.form) return {};
            const fd = new FormData(this.form);
            const state = {};
            for (const [k, v] of fd.entries()) {
                if (v !== '' && v !== null && v !== undefined) {
                    state[k] = v;
                }
            }
            return state;
        },

        syncFormFromUrl() {
            if (!this.form) return;
            const params = new URLSearchParams(window.location.search);
            this.form.querySelectorAll('input, select').forEach((el) => {
                const name = el.name;
                if (!name) return;
                if (params.has(name)) {
                    if (el.type === 'checkbox' || el.type === 'radio') {
                        el.checked = params.get(name) === el.value;
                    } else if (el.tagName === 'SELECT') {
                        el.value = params.get(name);
                    } else {
                        el.value = params.get(name);
                    }
                } else {
                    if (el.type === 'checkbox' || el.type === 'radio') {
                        el.checked = false;
                    } else if (el.tagName === 'SELECT') {
                        el.value = '';
                    } else {
                        el.value = '';
                    }
                }
            });
        },

        async load() {
            if (this.loading) return;
            this.loading = true;
            this.error = null;
            if (this.container) {
                this.container.dataset.loading = 'true';
            }

            const params = new URLSearchParams(this.getFormState());
            params.set('partial', '1');

            const url = `${endpoint}?${params.toString()}`;

            abortController = new AbortController();

            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    signal: abortController.signal,
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const data = await response.json();
                this.html = data.html || '';
            } catch (err) {
                if (err.name === 'AbortError') return;
                this.error = err.message || 'Gagal memuat data';
                this.html = this.renderError(this.error);
            } finally {
                this.loading = false;
                if (this.container) {
                    this.container.dataset.loading = 'false';
                }
            }
        },

        retry() {
            this.error = null;
            this.load();
        },

        openDeleteModal(id, name) {
            this.deleteItem = { id, name };
            this.showDeleteModal = true;
        },

        renderError(msg) {
            return `
                <div class="flex flex-col items-center gap-3 py-10 text-center">
                    <svg class="w-10 h-10 text-error-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p class="text-sm text-neutral-600">${msg}</p>
                    <button type="button" class="text-sm font-medium text-primary-600 hover:text-primary-700 underline" onclick="this.closest('[x-data]').__x.$data.retry()">Coba lagi</button>
                </div>
            `;
        },

        renderEmpty() {
            return `
                <div class="flex flex-col items-center gap-3 py-10 text-center">
                    <svg class="w-10 h-10 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-7 4h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm text-neutral-500">Tidak ada data yang cocok.</p>
                </div>
            `;
        },
    };
};

Alpine.start()

document.addEventListener('click', (event) => {
    const link = event.target.closest('a[href]');
    const table = link?.closest('[data-table-container]');

    if (table) {
        table.dataset.loading = 'true';
    }
});

document.addEventListener('submit', (event) => {
    const table = event.target.closest('[data-table-container]');

    if (table) {
        table.dataset.loading = 'true';
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const toggle = document.getElementById('sidebar-toggle');

    if (!sidebar || !overlay || !toggle) return;

    const openSidebar = () => {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        toggle.setAttribute('aria-expanded', 'true');
    };

    const closeSidebar = () => {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        toggle.setAttribute('aria-expanded', 'false');
    };

    toggle.addEventListener('click', () => {
        const isOpen = toggle.getAttribute('aria-expanded') === 'true';
        isOpen ? closeSidebar() : openSidebar();
    });

    overlay.addEventListener('click', closeSidebar);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeSidebar();
    });

    // Global: close open modal when Escape is pressed
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('[data-modal-wrapper][open]').forEach((wrapper) => {
                wrapper.removeAttribute('open');
                const closeButton = wrapper.querySelector('[data-modal-close]');
                if (closeButton && typeof closeButton.focus === 'function') {
                    // Fallback: return focus to the element that opened the modal if any
                    const focusedBefore = document.activeElement;
                    setTimeout(() => {
                        if (document.activeElement === document.body) focusedBefore?.focus();
                    }, 0);
                }
            });
        }
    });

    sidebar.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 1024) closeSidebar();
        });
    });
});
