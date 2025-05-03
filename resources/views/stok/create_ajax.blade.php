<form action="{{ url('/stok/ajax') }}" method="POST" id="form-tambah">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Stok</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="form_kategori_id" id="form_kategori_id" class="form-control" required>
                        <option value="">Pilih Kategori</option>
                        @foreach ($kategori as $k)
                            <option value="{{ $k->kategori_id }}">{{ $k->kategori_nama }}</option>
                        @endforeach
                    </select>
                    <small id="error-kategori_id" class="form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Nama Barang</label>
                    <select name="barang_id" id="form_barang_id" class="form-control" required disabled>
                        <option value="">Pilih Barang</option>
                    </select>
                    <small id="error-barang_id" class="form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Supplier</label>
                    <select name="supplier_id" class="form-control" required>
                        <option value="">Pilih Supplier</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->supplier_id }}">{{ $supplier->supplier_nama }}</option>
                        @endforeach
                    </select>
                    <small id="error-supplier_id" class="form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="stok_tanggal" class="form-control" required value="{{ date('Y-m-d') }}">
                    <small id="error-stok_tanggal" class="form-text text-danger"></small>
                </div>

                <div class="form-group">
                    <label>Jumlah Stok</label>
                    <input type="number" name="stok_jumlah" class="form-control" required min="1">
                    <small id="error-stok_jumlah" class="form-text text-danger"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('#form_kategori_id').on('change', function() {
            let kategori_id = $(this).val();
            if (kategori_id) {
                $.ajax({
                    url: '{{ url("/stok/get-barang") }}/' + kategori_id,
                    type: 'GET',
                    success: function(response) {
                        $('#form_barang_id').removeAttr('disabled');
                        $('#form_barang_id').empty();
                        $('#form_barang_id').append('<option value="">Pilih Barang</option>');
                        
                        if (response.status && response.barang.length > 0) {
                            $.each(response.barang, function(key, value) {
                                $('#form_barang_id').append(
                                    '<option value="' + value.barang_id + '">' + 
                                    value.barang_nama + '</option>'
                                );
                            });
                        } else {
                            $('#form_barang_id').append(
                                '<option value="">Tidak ada barang tersedia</option>'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", {
                            status: status,
                            error: error,
                            response: xhr.responseText
                        });
                        alert("Terjadi kesalahan saat mengambil data barang");
                    }
                });
            } else {
                $('#form_barang_id').attr('disabled', true);
                $('#form_barang_id').empty();
                $('#form_barang_id').append('<option value="">Pilih Barang</option>');
            }
        });
        $("#form-tambah").validate({
            rules: {
                kategori_id: {
                    required: true
                },
                barang_id: {
                    required: true
                },
                supplier_id: {
                    required: true
                },
                stok_tanggal: {
                    required: true
                },
                stok_jumlah: {
                    required: true,
                    number: true,
                    min: 1
                }
            },
            messages: {
                kategori_id: {
                    required: "Kategori harus dipilih"
                },
                barang_id: {
                    required: "Barang harus dipilih"
                },
                supplier_id: {
                    required: "Supplier harus dipilih"
                },
                stok_tanggal: {
                    required: "Tanggal harus diisi"
                },
                stok_jumlah: {
                    required: "Jumlah stok harus diisi",
                    number: "Jumlah stok harus berupa angka",
                    min: "Jumlah stok minimal 1"
                }
            },
            submitHandler: function(form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function(response) {
                        if (response.status) {
                            $('#myModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            dataStok.ajax.reload();
                        } else {
                            $('.error-text').text('');
                            $.each(response.errors, function(prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.message
                            });
                        }
                    }
                });
                return false;
            }
        });
    });
</script>
