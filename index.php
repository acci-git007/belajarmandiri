<?php

// KONEKSI DATABASE
$koneksi = mysqli_connect("localhost", "root", "", "crud_siswa");

if (!$koneksi) {
    die("Koneksi gagal");
}

// TAMBAH DATA
if (isset($_POST['tambah'])) {

    $nama     = $_POST['nama'];
    $kelas    = $_POST['kelas'];
    $jurusan  = $_POST['jurusan'];

    mysqli_query($koneksi,
        "INSERT INTO siswa (nama, kelas, jurusan)
         VALUES ('$nama', '$kelas', '$jurusan')");

    header("Location: index.php");
}

// HAPUS DATA
if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    mysqli_query($koneksi,
        "DELETE FROM siswa WHERE id='$id'");

    header("Location: index.php");
}

// AMBIL DATA EDIT
$edit = false;

if (isset($_GET['edit'])) {

    $edit = true;

    $id = $_GET['edit'];

    $dataEdit = mysqli_query($koneksi,
        "SELECT * FROM siswa WHERE id='$id'");

    $row = mysqli_fetch_assoc($dataEdit);
}

// UPDATE DATA
if (isset($_POST['update'])) {

    $id       = $_POST['id'];
    $nama     = $_POST['nama'];
    $kelas    = $_POST['kelas'];
    $jurusan  = $_POST['jurusan'];

    mysqli_query($koneksi,
        "UPDATE siswa SET
        nama='$nama',
        kelas='$kelas',
        jurusan='$jurusan'
        WHERE id='$id'
    ");

    header("Location: index.php");
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>CRUD Data Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background:#f5f5f5;
        }

        .container-box{
            background:white;
            padding:30px;
            border-radius:10px;
            margin-top:40px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
        }
    </style>

</head>
<body>

<div class="container">

    <div class="container-box">

        <h2 class="mb-4 text-center">CRUD Data Siswa</h2>

        <!-- FORM -->

        <form method="POST">

            <input type="hidden" name="id"
                value="<?= $edit ? $row['id'] : '' ?>">

            <div class="mb-3">
                <label>Nama</label>

                <input type="text"
                       name="nama"
                       class="form-control"
                       required
                       value="<?= $edit ? $row['nama'] : '' ?>">
            </div>

            <div class="mb-3">
                <label>Kelas</label>

                <input type="text"
                       name="kelas"
                       class="form-control"
                       required
                       value="<?= $edit ? $row['kelas'] : '' ?>">
            </div>

            <div class="mb-3">
                <label>Jurusan</label>

                <input type="text"
                       name="jurusan"
                       class="form-control"
                       required
                       value="<?= $edit ? $row['jurusan'] : '' ?>">
            </div>

            <?php if($edit) { ?>

                <button type="submit"
                        name="update"
                        class="btn btn-warning">
                    Update
                </button>

                <a href="index.php"
                   class="btn btn-secondary">
                    Batal
                </a>

            <?php } else { ?>

                <button type="submit"
                        name="tambah"
                        class="btn btn-primary">
                    Simpan
                </button>

            <?php } ?>

        </form>

        <hr>

        <!-- TABEL -->

        <table class="table table-bordered table-striped">

            <thead class="table-dark">

                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Jurusan</th>
                    <th width="200">Aksi</th>
                </tr>

            </thead>

            <tbody>

            <?php

            $no = 1;

            $data = mysqli_query($koneksi,
                "SELECT * FROM siswa ORDER BY id DESC");

            while($d = mysqli_fetch_array($data)) {

            ?>

                <tr>

                    <td><?= $no++; ?></td>

                    <td><?= $d['nama']; ?></td>

                    <td><?= $d['kelas']; ?></td>

                    <td><?= $d['jurusan']; ?></td>

                    <td>

                        <a href="?edit=<?= $d['id']; ?>"
                           class="btn btn-success btn-sm">
                           Edit
                        </a>

                        <a href="?hapus=<?= $d['id']; ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Yakin hapus data?')">
                           Hapus
                        </a>

                    </td>

                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>