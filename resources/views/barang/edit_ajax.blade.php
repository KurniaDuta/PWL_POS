@empty($barang)
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang anda cari tidak ditemukan
                </div>
                <a href="{{ url('/barang') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/barang/' . $barang->barang_id . '/update_ajax') }}" method="POST" id="form-edit">
        @csrf
        @method('PUT')
        <div id="modal-master" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Barang</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kategori Barang</label>
                        <select name="kategori_id" id="kategori_id" class="form-control" required>
                            <option value="">- Pilih Kategori -</option>
                            @foreach ($kategori as $k)
                                <option {{ $k->kategori_id == $barang->kategori_id ? 'selected' : '' }}
                                    value="{{ $k->kategori_id }}">{{ $k->kategori_nama }}</option>
                            @endforeach
                        </select>
                        <small id="error-kategori_id" class="form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Barang Kode</label>
                        <input value="{{ $barang->barang_kode }}" type="text" name="barang_kode" id="barang_kode"
                            class="form-control" required>
                        <small id="error-barang_kode" class="form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Barang Nama</label>
                        <input value="{{ $barang->barang_nama }}" type="text" name="barang_nama" id="barang_nama"
                            class="form-control" required>
                        <small id="error-barang_nama" class="form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Harga Beli</label>
                        <input type="text" id="harga_beli_display" class="form-control"
                            value="{{ number_format($barang->harga_beli, 0, ',', '.') }}" required>
                        <input type="hidden" name="harga_beli" id="harga_beli_hidden"
                            value="{{ $barang->harga_beli }}">
                        <small id="error-harga_beli" class="form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label>Harga Jual</label>
                        <input type="text" id="harga_jual_display" class="form-control"
                            value="{{ number_format($barang->harga_jual, 0, ',', '.') }}" required>
                        <input type="hidden" name="harga_jual" id="harga_jual_hidden"
                            value="{{ $barang->harga_jual }}">
                        <small id="error-harga_jual" class="form-text text-danger"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </form>

    <script>
        $(document).ready(function() {
            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID').format(angka);
            }

            function cleanRupiah(value) {
                return value.replace(/\D/g, '');
            }

            $('#harga_beli_display, #harga_jual_display').on('input', function() {
                let value = cleanRupiah($(this).val());
                let hiddenId = $(this).attr('id').replace('_display', '_hidden');
                $('#' + hiddenId).val(value);

                if (value !== '') {
                    $(this).val(formatRupiah(value));
                }
            });

            $('#harga_beli_display').val(formatRupiah($('#harga_beli_hidden').val()));
            $('#harga_jual_display').val(formatRupiah($('#harga_jual_hidden').val()));

            $('#form-edit').validate({
                rules: {
                    kategori_id: { required: true },
                    barang_kode: { required: true, minlength: 3, maxlength: 20 },
                    barang_nama: { required: true, minlength: 3, maxlength: 100 },
                    harga_beli: { required: true, number: true },
                    harga_jual: { required: true, number: true },
                },
                submitHandler: function(form) {
                    $('#harga_beli_hidden').val(cleanRupiah($('#harga_beli_display').val()));
                    $('#harga_jual_hidden').val(cleanRupiah($('#harga_jual_display').val()));

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
                                dataBarang.ajax.reload();
                            } else {
                                $('.error-text').text('');
                                $.each(response.msgField, function(prefix, val) {
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
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
@endempty
