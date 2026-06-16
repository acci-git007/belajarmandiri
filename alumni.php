<?php
require 'vendor/autoload.php';

use Aws\S3\S3Client;

$host = "dblatihanlks2026.cs16kc6eazau.us-east-1.rds.amazonaws.com";
$user = "admin";
$pass = "admin2026";
$db   = "dbalumni";

$bucket = "latihanlks2026";
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

    $nisn         = $_POST['nisn'];
    $nama         = $_POST['nama'];
    $jurusan      = $_POST['jurusan'];
    $tahun_lulus  = $_POST['tahun_lulus'];

    $fotoUrl = "";

    if(!empty($_FILES['foto']['name'])){

        $namaFile = "alumni/" . time() . "_" . basename($_FILES['foto']['name']);

        $upload = $s3->putObject([
            'Bucket'     => $latihanlks2026,
            'Key'        => $alumni,
            'SourceFile' => $_FILES['foto']['tmp_name']
        ]);

        $fotoUrl = $upload['ObjectURL'];
    }

    mysqli_query($conn,"
    INSERT INTO tbalumni
    (nisn,nama,jurusan,tahun_lulus,foto)
    VALUES
    ('$nisn','$nama','$jurusan','$tahun_lulus','$fotoUrl')
    ");
}

if(isset($_GET['hapus'])){

    $id = $_GET['hapus'];

    mysqli_query($conn,"
    DELETE FROM tbalumni
    WHERE id='$id'
    ");
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Data Alumni</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

<div class="container mt-4">

<h2 class="mb-4">Data Alumni</h2>

<div class="card">
<div class="card-body">

<form method="POST" enctype="multipart/form-data">

<div class="mb-3">
<label>NISN</label>
<input type="text" name="nisn" class="form-control" required>
</div>

<div class="mb-3">
<label>Nama</label>
<input type="text" name="nama" class="form-control" required>
</div>

<div class="mb-3">
<label>Jurusan</label>
<input type="text" name="jurusan" class="form-control" required>
</div>

<div class="mb-3">
<label>Tahun Lulus</label>
<input type="number" name="tahun_lulus" class="form-control" required>
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

<h4>Data Alumni</h4>

<table class="table table-bordered">

<tr>
<th>ID</th>
<th>NISN</th>
<th>Nama</th>
<th>Jurusan</th>
<th>Tahun Lulus</th>
<th>Foto</th>
<th>Aksi</th>
</tr>

<?php

$data = mysqli_query($conn,"
SELECT * FROM tbalumni
ORDER BY id DESC
");

while($d = mysqli_fetch_assoc($data)){
?>

<tr>

<td><?= $d['id']; ?></td>
<td><?= $d['nisn']; ?></td>
<td><?= $d['nama']; ?></td>
<td><?= $d['jurusan']; ?></td>
<td><?= $d['tahun_lulus']; ?></td>

<td>
<img src="<?= $d['foto']; ?>" width="100">
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
