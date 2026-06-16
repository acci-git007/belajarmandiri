<?php
require 'vendor/autoload.php';

use Aws\S3\S3Client;

$host = "dblatihanlks2026.cs16kc6eazau.us-east-1.rds.amazonaws.com";
$user = "admin";
$pass = "admin2026";
$db   = "dbpendaftar";

$bucket = "traininglks2026";
$region = "us-east-1";

$conn = mysqli_connect($host,$user,$pass,$db);

if(!$conn){
    die("Koneksi RDS gagal");
}

$s3 = new S3Client([
    'version' => 'latest',
    'region'  => $region
]);

if(isset($_POST['simpan'])){

    $nomor_formulir = $_POST['nomor_formulir'];
    $nisn           = $_POST['nisn'];
    $nama           = $_POST['nama'];
    $asal_sekolah   = $_POST['asal_sekolah'];

    $fotoUrl = "";

    if(!empty($_FILES['foto']['name'])){

        $namaFile = "pendaftar/" . time() . "_" . basename($_FILES['foto']['name']);

        $upload = $s3->putObject([
            'Bucket'     => $traininglks2026t,
            'Key'        => $pendaftaran,
            'SourceFile' => $_FILES['foto']['tmp_name']
        ]);

        $fotoUrl = $upload['ObjectURL'];
    }

    mysqli_query($conn,"
    INSERT INTO tbpendaftar
    (nomor_formulir,nisn,nama,asal_sekolah,foto)
    VALUES
    ('$nomor_formulir','$nisn','$nama','$asal_sekolah','$fotoUrl')
    ");
}

if(isset($_GET['hapus'])){

    $id = $_GET['hapus'];

    mysqli_query($conn,"
    DELETE FROM tbpendaftar
    WHERE id='$id'
    ");
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Pendaftaran Siswa Baru</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

<div class="container mt-4">

<h2 class="mb-4">Pendaftaran Siswa Baru</h2>

<div class="card">
<div class="card-body">

<form method="POST" enctype="multipart/form-data">

<div class="mb-3">
<label>Nomor Formulir</label>
<input type="text" name="nomor_formulir" class="form-control" required>
</div>

<div class="mb-3">
<label>NISN</label>
<input type="text" name="nisn" class="form-control" required>
</div>

<div class="mb-3">
<label>Nama</label>
<input type="text" name="nama" class="form-control" required>
</div>

<div class="mb-3">
<label>Asal Sekolah</label>
<input type="text" name="asal_sekolah" class="form-control" required>
</div>

<div class="mb-3">
<label>Foto</label>
<input type="file" name="foto" class="form-control" required>
</div>

<button type="submit" name="simpan" class="btn btn-primary">
Simpan
</button>

</form>

</div>
</div>

<hr>

<h4>Data Pendaftar</h4>

<table class="table table-bordered">

<tr>
<th>ID</th>
<th>Nomor Formulir</th>
<th>NISN</th>
<th>Nama</th>
<th>Asal Sekolah</th>
<th>Foto</th>
<th>Aksi</th>
</tr>

<?php

$data = mysqli_query($conn,"
SELECT * FROM tbpendaftar
ORDER BY id DESC
");

while($d = mysqli_fetch_assoc($data)){
?>

<tr>

<td><?= $d['id']; ?></td>
<td><?= $d['nomor_formulir']; ?></td>
<td><?= $d['nisn']; ?></td>
<td><?= $d['nama']; ?></td>
<td><?= $d['asal_sekolah']; ?></td>

<td>
<img src="<?= $d['foto']; ?>"
width="100">
</td>

<td>
<a href="?hapus=<?= $d['id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Hapus data?')">
Hapus
</a>
</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>
