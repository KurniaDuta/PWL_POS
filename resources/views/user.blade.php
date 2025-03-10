<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Data User</title>
</head>

<body>
    <h1>Data User</h1>
    <a href="user/tambah">+ Tambah Data User</a>
    <table border="1" cellpadding = "2" cellspacing = "0">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Nama</th>
            <th>ID Level Pengguna</th>
            <th>Kode Level</th>
            <th>Nama Level</th>
            <th>Aksi</th>
        </tr>
        @foreach ($data as $data)
            <tr>
                <td>{{ $data->user_id }}</td>
                <td>{{ $data->username }}</td>
                <td>{{ $data->nama }}</td>
                <td>{{ $data->level_id }}</td>
                <td>{{ $data->level->level_kode }}</td>
                <td>{{ $data->level->level_nama }}</td>
                <td><a href="user/ubah/{{ $data->user_id }}">Ubah</a> |
                    <a href="user/hapus/{{ $data->user_id }}">Hapus
                </td>
            </tr>
        @endforeach
    </table>
</body>

</html>
