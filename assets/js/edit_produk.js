function initEditProdukForm() {
    // When outlet selection changes
    $('#outlet').change(function() {
        const outletId = $(this).val();
        
        if (outletId) {
            // Fetch packages for the selected outlet
            $.ajax({
                url: 'get_packages.php',
                type: 'POST',
                data: { outlet_id: outletId },
                dataType: 'json',
                success: function(response) {
                    const packageSelect = $('#nama-paket');
                    packageSelect.empty();
                    
                    if (response.length > 0) {
                        packageSelect.append('<option value="" disabled selected>Pilih Paket</option>');
                        $.each(response, function(index, package) {
                            packageSelect.append(`<option value="${package.id}" data-jenis="${package.jenis}" data-harga="${package.harga}">${package.nama_paket}</option>`);
                        });
                    } else {
                        packageSelect.append('<option value="" disabled>Outlet ini belum memiliki paket</option>');
                    }
                },
                error: function() {
                    $('#nama-paket').html('<option value="" disabled>Error loading packages</option>');
                }
            });
        } else {
            $('#nama-paket').html('<option value="" disabled selected>Pilih outlet terlebih dahulu</option>');
            $('#jenis-produk-1').val('');
            $('#harga').val('');
        }
    });

    // When package selection changes
    $('#nama-paket').change(function() {
        const selectedOption = $(this).find('option:selected');
        $('#jenis-produk-1').val(selectedOption.data('jenis'));
        $('#harga').val(selectedOption.data('harga'));
    });
}

// // Panggil fungsi inisialisasi saat dokumen siap
// $(document).ready(function() {
//     initEditProdukForm();
// });