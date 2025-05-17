function initEditPenggunaForm() {
    console.log("Initializing edit user form...");

    // Ketika outlet dipilih
    $(document).on('change', '#outlet', function() {
        const outletId = $(this).val();
        console.log("Outlet selected:", outletId);

        if (outletId) {
            $('#nama-lengkap').html('<option value="" disabled selected>Memuat pengguna...</option>');

            $.ajax({
                url: 'get_users.php',
                type: 'POST',
                data: { outlet_id: outletId },
                dataType: 'json',
                success: function(response) {
                    const userSelect = $('#nama-lengkap');
                    userSelect.empty();

                    if (response.length > 0) {
                        userSelect.append('<option value="" disabled selected>Pilih Pengguna</option>');
                        $.each(response, function(index, user) {
                            userSelect.append(`<option value="${user.id}" 
                                data-username="${user.username}" 
                                data-role="${user.role}"
                                data-nama="${user.nama}">${user.nama} (${user.role})</option>`);
                        });
                    } else {
                        userSelect.append('<option value="" disabled>Outlet ini belum memiliki pengguna</option>');
                    }
                },
                error: function() {
                    $('#nama-lengkap').html('<option value="" disabled>Gagal memuat data pengguna</option>');
                }
            });
        } else {
            $('#nama-lengkap').html('<option value="" disabled selected>Pilih outlet terlebih dahulu</option>');
            $('#username').val('');
            $('#role').val('');
        }
    });

    // Ketika pengguna dipilih
    $(document).on('change', '#nama-lengkap', function() {
        const selectedOption = $(this).find('option:selected');
        if (selectedOption.length) {
            const username = selectedOption.data('username') || '';
            const role = selectedOption.data('role') || '';
            const nama = selectedOption.data('nama') || '';

            $('#username').val(username);
            $('#role').val(role);
        } else {
            $('#username').val('');
            $('#role').val('');
        }
    });

    // Handle submit form edit
    $(document).on('submit', '#formEditPengguna', function(e) {
        e.preventDefault();

        // Cegah pengiriman ganda
        if ($(this).data('submitted')) return;
        $(this).data('submitted', true);

        // Validasi form
        if ($('#nama-lengkap').val() === '' || $('#nama-lengkap-baru').val() === '' || 
            $('#username-baru').val() === '' || $('#role-baru').val() === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Semua field harus diisi!'
            });
            $(this).data('submitted', false);
            return;
        }

        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
        submitBtn.prop('disabled', true);

        $.ajax({
            url: 'save_edit_user.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                try {
                    const res = response;  // ✅ Langsung gunakan response
            

                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message || 'Data pengguna berhasil diperbarui!',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            location.href = location.href;
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: res.message || 'Terjadi kesalahan saat memperbarui data.'
                        });
                    }
                } catch (e) {
                    console.error("Parsing error:", e, response);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan dalam memproses respons.'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan koneksi: ' + error
                });
            },
            complete: function() {
                submitBtn.html('Edit');
                submitBtn.prop('disabled', false);
                $('#formEditPengguna').data('submitted', false);
            }
        });
    });
}

// Panggil fungsi inisialisasi saat dokumen siap
$(document).ready(function() {
    if ($('#formEditPengguna').length) {
        initEditPenggunaForm();
    }
});
