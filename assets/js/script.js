document.addEventListener('DOMContentLoaded', () => {
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
                    $('#ajaxAlertBox')
                        .removeClass('d-none alert-danger')
                        .addClass('alert-success')
                        .text(res.message || 'Employee record deleted successfully.');
                } else {
                    alert(res.message || 'Failed to delete employee record.');
                }
            },
            error: function () {
                alert('Server error occurred while deleting record.');
            },
            complete: function () {
                btn.prop('disabled', false).text('Delete Record');
                pendingDeleteId = null;
            }
        });
    });
});
