<?php

require __DIR__ . '/vendor/autoload.php';

use Aws\S3\S3Client;

/* =========================
   KONFIGURASI RDS
========================= */

$host = "db-data-siswa.c83ya4kmsi7u.us-east-1.rds.amazonaws.com";
$user = "admin";
$pass = "admin2026";
$db   = "dbsiswa";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi RDS gagal : " . mysqli_connect_error());
}

/* =========================
   KONFIGURASI S3
========================= */

$bucket = "foto-data-siswa";
$s3 = new S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);

/* =========================
   SIMPAN DATA
========================= */

if (isset($_POST['simpan'])) {

    $nis    = mysqli_real_escape_string($conn, $_POST['nis']);
    $nama   = mysqli_real_escape_string($conn, $_POST['nama']);
    $kelas  = mysqli_real_escape_string($conn, $_POST['kelas']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    $fotoUrl = "";

    if (!empty($_FILES['foto']['name'])) {

        $namaFile = time() . "_" . basename($_FILES['foto']['name']);

        try {

            $upload = $s3->putObject([
                'Bucket'      => $bucket,
                'Key'         => 'siswa/' . $namaFile,
                'SourceFile'  => $_FILES['foto']['tmp_name'],
                'ContentType' => $_FILES['foto']['type']
            ]);

            $fotoUrl = $upload['ObjectURL'];

        } catch (Exception $e) {

            die("Upload ke S3 gagal : " . $e->getMessage());
        }
    }

    $sql = "INSERT INTO siswa
            (nis,nama,kelas,alamat,foto)
            VALUES
            ('$nis','$nama','$kelas','$alamat','$fotoUrl')";

    mysqli_query($conn, $sql);

    header("Location:index.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Aplikasi Data Siswa</title>

<style>

body{
    font-family:Arial;
    background:#f5f5f5;
    margin:30px;
}

.container{
    background:white;
    padding:20px;
    border-radius:10px;
}

input, textarea{
    width:100%;
    padding:10px;
    margin-bottom:10px;
}

button{
    background:#0d6efd;
    color:white;
    border:none;
    padding:10px 20px;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

table th, table td{
    border:1px solid #ddd;
    padding:10px;
}

table th{
    background:#0d6efd;
    color:white;
}

</style>
</head>
<body>

<div class="container">

<h2>Input Data Siswa</h2>

<form method="POST" enctype="multipart/form-data">

    <input type="text" name="nis" placeholder="NIS" required>

    <input type="text" name="nama" placeholder="Nama Siswa" required>

    <input type="text" name="kelas" placeholder="Kelas" required>

    <textarea name="alamat" placeholder="Alamat"></textarea>

    <input type="file" name="foto" required>

    <button type="submit" name="simpan">
        Simpan
    </button>

</form>

<hr>

<h2>Daftar Siswa</h2>

<table>

<tr>
    <th>ID</th>
    <th>Foto</th>
    <th>NIS</th>
    <th>Nama</th>
    <th>Kelas</th>
    <th>Alamat</th>
</tr>

<?php

$data = mysqli_query($conn,"SELECT * FROM siswa ORDER BY id DESC");

while($row = mysqli_fetch_assoc($data))
{
?>

<tr>

<td><?php echo $row['id']; ?></td>

<td>

<?php if(!empty($row['foto'])) { ?>

<img
src="<?php echo $row['foto']; ?>"
width="100"
height="100">

<?php } ?>

</td>

<td><?php echo $row['nis']; ?></td>
<td><?php echo $row['nama']; ?></td>
<td><?php echo $row['kelas']; ?></td>
<td><?php echo $row['alamat']; ?></td>

</tr>

<?php
}
?>

</table>

</div>

</body>
</html>
