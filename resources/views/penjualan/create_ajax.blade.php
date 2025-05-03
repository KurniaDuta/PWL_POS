<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Tambah Penjualan Baru</h5>
            <button type="button" class="close" data-dismiss="modal">
                <span>&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form action="{{ url('/penjualan/store_ajax') }}" method="POST" id="form-tambah">
                @csrf

                <div class="form-group">
                    <label>Nama Pembeli</label>
                    <input type="text" name="pembeli" class="form-control" required>
                    <small id="error-pembeli" class="form-text text-danger"></small>
                </div>

                <div class="card mt-3 mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Detail Barang</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="detail_table">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="35%">Nama Barang</th>
                                        <th width="20%">Harga</th>
                                        <th width="15%">Jumlah</th>
                                        <th width="20%">Subtotal</th>
                                        <th width="5%">
                                            <button type="button" class="btn btn-sm btn-success" id="tambah-baris">
                                                +
                                            </button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="row-0">
                                        <td class="text-center row-number">1</td>
                                        <td>
                                            <select name="details[0][barang_id]" class="form-control barang-select"
                                                required>
                                                <option value="">Pilih Barang</option>
                                                @foreach ($barangs as $barang)
                                                    <option value="{{ $barang->barang_id }}"
                                                        data-harga="{{ $barang->harga_jual }}">
                                                        {{ $barang->barang_nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control harga-display" readonly>
                                            <input type="hidden" name="details[0][harga]" class="harga">
                                        </td>
                                        <td>
                                            <input type="number" name="details[0][jumlah]" class="form-control jumlah"
                                                min="1">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control subtotal" readonly>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger hapus-baris">-</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="p-3">
                                <label>Total</label>
                                <input type="text" id="total-harga" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let rowCount = 1;

        function formatRupiah(angka) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
        }

        // Hitung total semua subtotal
        function hitungTotal() {
            let total = 0;
            $('.subtotal').each(function() {
                let value = $(this).val().replace(/Rp\s?/g, '').replace(/\./g, '');
                if (value !== '') {
                    total += parseInt(value);
                }
            });
            $('#total-harga').val(formatRupiah(total));
        }

        // Hitung subtotal per baris
        function hitungSubtotal(row) {
            let harga = row.find('.harga').val().replace(/[^0-9]/g, '') || 0;
            let jumlah = row.find('.jumlah').val() || 0;
            let subtotal = parseInt(harga) * parseInt(jumlah);

            row.find('.subtotal').val(formatRupiah(subtotal));
            hitungTotal();
        }

        // Event: ketika barang dipilih
        $(document).on('change', '.barang-select', function() {
            $(document).on('change', '.barang-select', function() {
                let row = $(this).closest('tr');
                let harga = $(this).find(':selected').data('harga') || 0;

                row.find('.harga-display').val(formatRupiah(harga));
                row.find('.harga').val(harga);

                hitungSubtotal(row);
            });
        });

        // Event: ketika jumlah diinput
        $(document).on('input', '.jumlah', function() {
            let row = $(this).closest('tr');
            hitungSubtotal(row);
        });

        // Tambah baris baru
        $('#tambah-baris').click(function() {
            let lastRow = $('#detail_table tbody tr:last');
            let newRow = lastRow.clone();

            newRow.attr('id', 'row-' + rowCount);
            newRow.find('input, select').each(function() {
                let name = $(this).attr('name');
                if (name) {
                    name = name.replace(/\[\d+\]/, '[' + rowCount + ']');
                    $(this).attr('name', name);
                }

                if ($(this).is('select')) {
                    $(this).val('');
                } else {
                    $(this).val('');
                }
            });

            newRow.find('.row-number').text(rowCount + 1);
            $('#detail_table tbody').append(newRow);
            rowCount++;
        });

        $(document).on('click', '.hapus-baris', function() {
            if ($('#detail_table tbody tr').length > 1) {
                $(this).closest('tr').remove();
                updateRowNumbers();
                hitungTotal();
            }
        });

        function updateRowNumbers() {
            $('#detail_table tbody tr').each(function(index) {
                $(this).find('.row-number').text(index + 1);
            });
        }

        // Submit form
        $("#form-tambah").submit(function(e) {
            e.preventDefault();
            $('.text-danger').text('');

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.status) {
                        $('#modal-master').closest('.modal').modal('hide');
                        if (typeof dataPenjualan !== 'undefined') {
                            dataPenjualan.ajax.reload();
                        }
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON?.errors;
                    if (errors) {
                        $.each(errors, function(key, value) {
                            if (key.includes('.')) {
                                let parts = key.split('.');
                                let index = parts[1];
                                let field = parts[2];
                                let inputField = $(
                                    `tr#row-${index} [name="details[${index}][${field}]"]`
                                );
                                inputField.addClass('is-invalid');
                                inputField.next('.text-danger').text(value[0]);

                                inputField.one('input change', function() {
                                    $(this).removeClass('is-invalid');
                                    $(this).next('.text-danger').text('');
                                });
                            } else {
                                let inputField = $(`[name="${key}"]`);
                                inputField.addClass('is-invalid');
                                inputField.next('.text-danger').text(value[0]);

                                inputField.one('input change', function() {
                                    $(this).removeClass('is-invalid');
                                    $(this).next('.text-danger').text('');
                                });
                            }
                        });
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: xhr.responseJSON?.message ||
                            'Mohon periksa kembali input Anda'
                    });
                }
            });
        });
    });
</script>
