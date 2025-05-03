<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            margin: 6px 20px 5px 20px;
            line-height: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 4px 3px;
        }

        th {
            text-align: left;
        }

        .d-block {
            display: block;
        }

        img.image {
            width: auto;
            height: 80px;
            max-width: 150px;
            max-height: 150px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        p-1 {
            padding: 5px 1px 5px 1px;
        }

        .font-10 {
            font-size: 10pt;
        }

        .font-11 {
            font-size: 11pt;
        }

        .font-12 {
            font-size: 12pt;
        }

        .font-13 {
            font-size: 13pt;
        }

        .border-bottom-header {
            border-bottom: 1px solid;
        }

        .border-all,
        .border-all th,
        .border-all td {
            border: 1px solid;
        }
    </style>
</head>

<body>
    <table class="border-bottom-header">
        <tr>
            <td width="1%">
                <img src="{{ asset('polinema.jpg') }}" style="width: 100px; height: auto;">
            </td>
            <td width="85%" class="text-center">
                <div class="text-bold" style="font-size: 11pt">
                    KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI
                </div>
                <div class="text-bold" style="font-size: 13pt">
                    POLITEKNIK NEGERI MALANG
                </div>
                <div style="font-size: 10pt">
                    Jl. Soekarno-Hatta No. 9 Malang 65141<br>
                    Telepon (0341) 484424 Pes. 101-105, 0341-484428, Fax. (0341) 484428<br>
                    Laman: www.polinema.ac.id
                </div>
            </td>
        </tr>
    </table>

    <h3 class="text-center">LAPORAN DATA PENJUALAN</h3>

    @foreach ($penjualan as $index => $p)
        <table class="border-all mb-10" style="background-color: #e2e2e2; margin-top: 20px;">
            <tr>
                <th">Kode Penjualan</th>
                    <td">: {{ $p->penjualan_kode }}</td>
                        <th">Tanggal</th>
                            <td">: {{ date('d-m-Y', strtotime($p->penjualan_tanggal)) }}</td>
            </tr>
            <tr>
                <th>Pembeli</th>
                <td>: {{ $p->pembeli }}</td>
                <th>Kasir</th>
                <td>: {{ $p->user->nama }}</td>
            </tr>
        </table>

        <table class="border-all detail-table">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th>Nama Barang</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-right">Harga</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalItem = 0;
                    $totalHarga = 0;
                @endphp
                @foreach ($p->details as $detail)
                    @php
                        $totalItem += $detail->jumlah;
                        $totalHarga += $detail->jumlah * $detail->harga;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $detail->barang->barang_nama }}</td>
                        <td class="text-center">{{ $detail->jumlah }}</td>
                        <td class="currency">{{ number_format($detail->harga, 0, ',', '.') }}</td>
                        <td class="currency">{{ number_format($detail->jumlah * $detail->harga, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2" class="text-right text-bold">Total:</td>
                    <td class="text-center text-bold">{{ $totalItem }}</td>
                    <td></td>
                    <td class="currency text-bold">{{ number_format($totalHarga, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @endforeach
</body>

</html>
