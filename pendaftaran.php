<?php
require 'vendor/autoload.php';

use Aws\S3\S3Client;

$host   = "RDS-ENDPOINT";
$user   = "admin";
$pass   = "PASSWORD";
$db     = "dbpendaftar";

$bucket = "traininglks2026";
$region = "us-east-1";

$conn = mysqli_connect($host,$user,$pass,$db);
if(!$conn){
    die("Koneksi RDS gagal");
}

$s3 = new S3Client([
    'version' => 'latest',
    'region' => $region
]);

if(isset($_POST['simpan'])){

    $nomor_formulir = $_POST['nomor_formulir'];
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $asal_sekolah = $_POST['asal_sekolah'];

    $fotoUrl = "";

    if($_FILES['foto']['error']==0){

        $namaAsli = preg_replace(
            '/[^A-Za-z0-9.\-_]/',
            '_',
            $_FILES['foto']['name']
        );

        $key = "pendaftar/".time()."_".$namaAsli;

        $result = $s3->putObject([
            'Bucket' => $bucket,
            'Key' => $key,
            'SourceFile' => $_FILES['foto']['tmp_name']
        ]);

        $fotoUrl = $result['ObjectURL'];
    }

    mysqli_query($conn,"
    INSERT INTO tbpendaftar
    (nomor_formulir,nisn,nama,asal_sekolah,foto)
    VALUES
    ('$nomor_formulir','$nisn','$nama','$asal_sekolah','$fotoUrl')
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

<h2>Pendaftaran Siswa Baru</h2>

<form method="post" enctype="multipart/form-data">

<input type="text" name="nomor_formulir" class="form-control mb-2" placeholder="Nomor Formulir" required>

<input type="text" name="nisn" class="form-control mb-2" placeholder="NISN" required>

<input type="text" name="nama" class="form-control mb-2" placeholder="Nama" required>

<input type="text" name="asal_sekolah" class="form-control mb-2" placeholder="Asal Sekolah" required>

<input type="file" name="foto" class="form-control mb-2" required>

<button type="submit" name="simpan" class="btn btn-primary">
Simpan
</button>

</form>

<hr>

<table class="table table-bordered">

<tr>
<th>ID</th>
<th>No Formulir</th>
<th>NISN</th>
<th>Nama</th>
<th>Asal Sekolah</th>
<th>Foto</th>
</tr>

<?php

$data=mysqli_query($conn,"
SELECT * FROM tbpendaftar
ORDER BY id DESC
");

while($d=mysqli_fetch_assoc($data)){
?>

<tr>

<td><?= $d['id']; ?></td>
<td><?= $d['nomor_formulir']; ?></td>
<td><?= $d['nisn']; ?></td>
<td><?= $d['nama']; ?></td>
<td><?= $d['asal_sekolah']; ?></td>

<td>
<?php if(!empty($d['foto'])){ ?>
<img src="<?= htmlspecialchars($d['foto']); ?>" width="120">
<?php } ?>
</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>
