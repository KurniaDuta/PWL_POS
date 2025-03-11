@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools"></div>
        </div>
        <div class="card-body">
            @empty($barang)
                <div class="alert alert-danger alert-dismissible">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!</h5>
                    Data yang Anda cari tidak ditemukan.
                </div>
                <a href="{{ url('barang') }}" class="btn btn-sm btn-default mt-2">Kembali</a>
            @else
                <form method="POST" action="{{ url('/barang/' . $barang->barang_id) }}" class="form-horizontal">
                    @csrf
                    @method('PUT')

                    <div class="form-group row">
                        <label class="col-1 control-label col-form-label">Kategori</label>
                        <div class="col-11">
                            <select class="form-control" id="kategori_id" name="kategori_id" required>
                                <option value="">- Pilih Kategori -</option>
                                @foreach ($kategori as $item)
                                    <option value="{{ $item->kategori_id }}"
                                        {{ old('kategori_id', $barang->kategori_id) == $item->kategori_id ? 'selected' : '' }}>
                                        {{ $item->kategori_nama }}</option>
                                @endforeach
                            </select>
                            @error('kategori_id')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-1 control-label col-form-label">Barang Kode</label>
                        <div class="col-11">
                            <input type="text" class="form-control" id="barang_kode" name="barang_kode"
                                value="{{ old('barang_kode', $barang->barang_kode) }}" required>
                            @error('barang_kode')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-1 control-label col-form-label">Barang Nama</label>
                        <div class="col-11">
                            <input type="text" class="form-control" id="barang_nama" name="barang_nama"
                                value="{{ old('barang_nama', $barang->barang_nama) }}" required>
                            @error('barang_nama')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-1 control-label col-form-label">Harga Beli</label>
                        <div class="col-11">
                            <input type="text" class="form-control" id="harga_beli_display"
                                value="{{ number_format($barang->harga_beli, 0, ',', '.') }}">
                            <input type="hidden" name="harga_beli" id="harga_beli_hidden" value="{{ $barang->harga_beli }}">
                            @error('harga_beli')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-1 control-label col-form-label">Harga Jual</label>
                        <div class="col-11">
                            <input type="text" class="form-control" id="harga_jual_display"
                                value="{{ number_format($barang->harga_jual, 0, ',', '.') }}">
                            <input type="hidden" name="harga_jual" id="harga_jual_hidden" value="{{ $barang->harga_jual }}">
                            @error('harga_jual')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-11 offset-1">
                            <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                            <a class="btn btn-sm btn-default ml-1" href="{{ url('barang') }}">Kembali</a>
                        </div>
                    </div>
                </form>
            @endempty
        </div>
    </div>
@endsection

@push('css')
@endpush

@push('js')
    <script>
        $(document).ready(function() {
            $('#harga_beli_display, #harga_jual_display').on('input', function() {
                let value = $(this).val().replace(/\D/g, '');

                let hiddenId = $(this).attr('id').replace('_display', '_hidden');
                $('#' + hiddenId).val(value);

                if (value !== '') {
                    $(this).val(formatRupiah(value));
                }
            });

            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID').format(angka);
            }

            $('form').on('submit', function() {
                return true;
            });
        });
    </script>
@endpush
