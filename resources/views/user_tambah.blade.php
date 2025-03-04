<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Data User</title>
</head>
<body>
    <h1>Form Tambah Data User</h1>
    <form method="POST" action="tambah_simpan">
        {{ csrf_field() }}

        <label for="username">Username</label>
        <input type="text" name="username" placeholder="Masukkan Username" required>
        <br><br>

        <label for="nama">Nama</label>
        <input type="text" name="nama" placeholder="Masukkan Nama" required>
        <br><br>

        <label for="password">Password</label>
        <input type="password" name="password" placeholder="Masukkan Password" required>
        <br><br>

        <label for="level_id">Level Pengguna</label>
        <input type="number" name="level_id" placeholder="Masukkan ID Level" required min="1">
        <br><br>

        <button type="submit">Simpan</button>
    </form>
</body>
</html>