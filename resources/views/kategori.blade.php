<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Kategori Barang</title>
</head>
<body>
    <h1>Data Kategori Barang</h1>
    <table border="1" cellpadding="2" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Kode Kategori</th>
            <th>Nama Kategori</th>
        </tr>

        @foreach ($data as $kategori)
            <tr>
                <td>{{ $kategori->kategori_id }}</td>
                <td>{{ $kategori->kategori_kode }}</td>
                <td>{{ $kategori->kategori_nama }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>