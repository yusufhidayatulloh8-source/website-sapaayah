document.addEventListener('DOMContentLoaded', function () {
    const body = document.body;
    const sidebar = document.querySelector('[data-sidebar]');
    const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
    const sidebarBackdrop = document.querySelector('[data-sidebar-backdrop]');
    const sidebarCollapse = document.querySelector('[data-sidebar-collapse]');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function () {
            body.classList.add('sidebar-open');
        });
    }

    if (sidebarBackdrop) {
        sidebarBackdrop.addEventListener('click', function () {
            body.classList.remove('sidebar-open');
        });
    }

    if (sidebar && sidebarCollapse) {
        sidebarCollapse.addEventListener('click', function () {
            sidebar.classList.toggle('is-collapsed');
        });
    }

    document.querySelectorAll('.nav-admin a').forEach((item) => {
        item.addEventListener('click', function () {
            body.classList.remove('sidebar-open');
        });
    });

    const profileMenu = document.querySelector('[data-profile-menu]');
    const profileBtn = document.querySelector('[data-profile-btn]');

    if (profileMenu && profileBtn) {
        profileBtn.addEventListener('click', function () {
            profileMenu.classList.toggle('open');
        });

        document.addEventListener('click', function (event) {
            if (!profileMenu.contains(event.target)) {
                profileMenu.classList.remove('open');
            }
        });
    }

    const useSwal = typeof Swal !== 'undefined';

    document.querySelectorAll('.alert').forEach((alertEl) => {
        const isSuccess = alertEl.classList.contains('alert-success');
        const message = alertEl.textContent.trim();

        if (useSwal && message) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: isSuccess ? 'success' : 'error',
                title: message,
                showConfirmButton: false,
                timer: 2600,
                timerProgressBar: true,
            });
            alertEl.style.display = 'none';
        }
    });

    document.querySelectorAll('[data-confirm]').forEach((button) => {
        button.addEventListener('click', function (e) {
            const message = this.dataset.confirm || 'Yakin ingin melanjutkan aksi ini?';

            if (!useSwal) {
                if (!confirm(message)) {
                    e.preventDefault();
                }
                return;
            }

            e.preventDefault();
            const href = this.getAttribute('href');
            Swal.fire({
                title: 'Konfirmasi',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, lanjutkan',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed && href) {
                    window.location.href = href;
                }
            });
        });
    });
});
