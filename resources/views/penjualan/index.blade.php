@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3>{{ $page->title }}</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('penjualan/create_ajax') }}')" class="btn btn-success">
                    <i class="fas fa-plus"></i> Tambah Penjualan
                </button>
                <a class="btn btn-primary" href="{{ url('penjualan/export_excel') }}"><i class="fa fa-file-excel"></i> Export
                    Excel</a>
                <a href="{{ url('penjualan/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export
                    PDF</a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <table class="table table-bordered table-striped table-hover table-sm" id="table_penjualan">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Kode</th>
                        <th>Pembeli</th>
                        <th>Total Item</th>
                        <th>Total Harga</th>
                        <th>Kasir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
        data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }
        var dataPenjualan;
        $(document).ready(function() {
            dataPenjualan = $('#table_penjualan').DataTable({
                serverSide: true,
                ajax: {
                    "url": "{{ url('penjualan/list') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d) {
                        d._token = "{{ csrf_token() }}"
                    }
                },
                columns: [{
                        data: "DT_RowIndex",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "penjualan_tanggal",
                        className: "text-center"
                    },
                    {
                        data: "penjualan_kode"
                    },
                    {
                        data: "pembeli"
                    },
                    {
                        data: "total_item",
                        className: "text-right"
                    },
                    {
                        data: "total_harga",
                        className: "text-right",
                        render: function(data, type, row) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                        }
                    },
                    {
                        data: "user.nama"
                    },
                    {
                        data: "aksi",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'desc']
                ],
                responsive: true
            });
        });
    </script>
@endpush
