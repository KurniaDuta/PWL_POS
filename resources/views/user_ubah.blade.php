<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    <h1>Form Ubah Data User</h1>
    <button onclick="history.back()">Kembali</button>
    <br><br>

    <form action="{{ url('user/ubah_simpan/'.$data->user_id) }}" method="POST">
        {{ csrf_field() }}
        {{ method_field('PUT') }}

        <label for="username">Username</label>
        <input type="text" name="username" placeholder="Masukkan Username" value="{{ $data->username }}" required>
        <br><br>

        <label for="nama">Nama</label>
        <input type="text" name="nama" placeholder="Masukkan Nama" value="{{ $data->nama }}" required>
        <br><br>

        <label for="password">Password</label>
        <input type="password" name="password" placeholder="Masukkan Password" value="{{ $data->password }}" required>
        <br><br>

        <label for="level_id">Level Pengguna</label>
        <input type="number" name="level_id" placeholder="Masukkan ID Level" value="{{ $data->level_id }}" required min="1">
        <br><br>

        <button type="submit">Ubah</button>
    </form>
</body>
</html>