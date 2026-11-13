document.addEventListener('DOMContentLoaded', () => {
    // ── Toast helper ───────────────────────────────────────────────
    function showToast(message, type = 'success') {
        // Buat container toast jika belum ada
        let container = document.getElementById('stafflyToastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'stafflyToastContainer';
            container.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:8px;';
            document.body.appendChild(container);
        }

        const bgColor   = type === 'success' ? '#84f03a' : '#dc3545';
        const textColor = type === 'success' ? '#0c0d0e' : '#ffffff';

        const toastEl = document.createElement('div');
        toastEl.style.cssText = `
            background:${bgColor};color:${textColor};
            padding:12px 18px;border-radius:14px;font-size:0.85rem;font-weight:700;
            box-shadow:0 8px 24px rgba(0,0,0,0.35);
            display:flex;align-items:center;gap:10px;min-width:260px;max-width:360px;
            animation:stafflySlideIn .25s ease;
        `;
        const icon = type === 'success' ? '✓' : '✕';
        toastEl.innerHTML = `<span style="font-size:1rem;">${icon}</span><span>${message}</span>`;
        container.appendChild(toastEl);

        // Inject animasi CSS sekali
        if (!document.getElementById('stafflyToastStyle')) {
            const style = document.createElement('style');
            style.id = 'stafflyToastStyle';
            style.textContent = `
                @keyframes stafflySlideIn {
                    from { opacity:0; transform:translateX(30px); }
                    to   { opacity:1; transform:translateX(0); }
                }
                @keyframes stafflySlideOut {
                    from { opacity:1; transform:translateX(0); }
                    to   { opacity:0; transform:translateX(30px); }
                }
            `;
            document.head.appendChild(style);
        }

        // Auto-remove setelah 3.5 detik
        setTimeout(() => {
            toastEl.style.animation = 'stafflySlideOut .25s ease forwards';
            setTimeout(() => toastEl.remove(), 260);
        }, 3500);
    }

    // ── Auto-show flash toast dari PHP ─────────────────────────────
    const flashMsg = document.getElementById('stafflyFlashToast');
    if (flashMsg) {
        showToast(flashMsg.dataset.message || flashMsg.textContent.trim(), 'success');
    }

    // Login form client-side validation
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            const errorBox = document.getElementById('clientErrorBox');

            if (!username || !password) {
                e.preventDefault();
                errorBox.textContent = 'Username and password are required before signing in.';
                errorBox.classList.remove('d-none');
            }
        });
    }

    // Add / Edit Employee form client-side validation
    const employeeForm = document.getElementById('employeeForm');
    if (employeeForm) {
        employeeForm.addEventListener('submit', (e) => {
            const nip = document.getElementById('nip').value.trim();
            const nama = document.getElementById('nama').value.trim();
            const email = document.getElementById('email').value.trim();
            const jabatan = document.getElementById('jabatan').value.trim();
            const errorBox = document.getElementById('formErrorBox');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            let message = '';
            if (!nip || nip.length < 6) {
                message = 'Employee ID (NIP) is required and must be at least 6 characters.';
            } else if (!nama) {
                message = 'Full name is required.';
            } else if (!email || !emailRegex.test(email)) {
                message = 'Please enter a valid work email address.';
            } else if (!jabatan) {
                message = 'Role / position is required.';
            }

            if (message) {
                e.preventDefault();
                errorBox.textContent = message;
                errorBox.classList.remove('d-none');
            }
        });
    }

    // Live search filter on Employee List table
    const searchInput = document.getElementById('searchEmployee');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.trim().toLowerCase();
            const rows = document.querySelectorAll('#employeeTableBody tr[data-row]');
            rows.forEach((row) => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }

    // Asynchronous Delete via jQuery AJAX (FR-07)
    let pendingDeleteId = null;
    let pendingDeleteRow = null;
    const deleteModalEl = document.getElementById('deleteConfirmModal');
    const deleteModal = deleteModalEl ? new bootstrap.Modal(deleteModalEl) : null;

    $(document).on('click', '.btn-delete-employee', function () {
        pendingDeleteId = $(this).data('id');
        const name = $(this).data('name');
        const nip = $(this).data('nip');
        pendingDeleteRow = $(this).closest('tr');

        $('#deleteEmployeeName').text(name);
        $('#deleteEmployeeNip').text(nip);
        if (deleteModal) deleteModal.show();
    });

    $('#confirmDeleteBtn').on('click', function () {
        if (!pendingDeleteId) return;
        const btn = $(this);
        btn.prop('disabled', true).text('Deleting...');

        $.ajax({
            url: '../proses/proses_hapus.php',
            method: 'POST',
            dataType: 'json',
            data: { id: pendingDeleteId },
            success: function (res) {
                if (res.status === 'success') {
                    if (deleteModal) deleteModal.hide();
                    if (pendingDeleteRow) {
                        pendingDeleteRow.fadeOut(250, function () {
                            $(this).remove();
                        });
                    }
                    showToast(res.message || 'Employee record deleted successfully.', 'success');
                } else {
                    showToast(res.message || 'Failed to delete employee record.', 'error');
                }
            },
            error: function () {
                showToast('Server error occurred while deleting record.', 'error');
            },
            complete: function () {
                btn.prop('disabled', false).text('Delete Record');
                pendingDeleteId = null;
            }
        });
    });
});
