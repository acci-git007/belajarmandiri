<?php
require 'vendor/autoload.php';

use Aws\S3\S3Client;

$host   = "RDS-ENDPOINT";
$user   = "admin";
$pass   = "PASSWORD";
$db     = "dbalumni";

$bucket = "traininglks2026";
$region = "us-east-1";

$conn = mysqli_connect($host,$user,$pass,$db);

$s3 = new S3Client([
    'version' => 'latest',
    'region' => $region
]);

if(isset($_POST['simpan'])){

    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $jurusan = $_POST['jurusan'];
    $tahun_lulus = $_POST['tahun_lulus'];

    $fotoUrl = "";

    if($_FILES['foto']['error']==0){

        $namaAsli = preg_replace(
            '/[^A-Za-z0-9.\-_]/',
            '_',
            $_FILES['foto']['name']
        );

        $key = "alumni/".time()."_".$namaAsli;

        $result = $s3->putObject([
            'Bucket' => $bucket,
            'Key' => $key,
            'SourceFile' => $_FILES['foto']['tmp_name']
        ]);

        $fotoUrl = $result['ObjectURL'];
    }

    mysqli_query($conn,"
    INSERT INTO tbalumni
    (nisn,nama,jurusan,tahun_lulus,foto)
    VALUES
    ('$nisn','$nama','$jurusan','$tahun_lulus','$fotoUrl')
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

<h2>Data Alumni</h2>

<form method="post" enctype="multipart/form-data">

<input type="text" name="nisn" class="form-control mb-2" placeholder="NISN" required>

<input type="text" name="nama" class="form-control mb-2" placeholder="Nama" required>

<input type="text" name="jurusan" class="form-control mb-2" placeholder="Jurusan" required>

<input type="number" name="tahun_lulus" class="form-control mb-2" placeholder="Tahun Lulus" required>

<input type="file" name="foto" class="form-control mb-2" required>

<button type="submit" name="simpan" class="btn btn-primary">
Simpan
</button>

</form>

<hr>

<table class="table table-bordered">

<tr>
<th>ID</th>
<th>NISN</th>
<th>Nama</th>
<th>Jurusan</th>
<th>Tahun Lulus</th>
<th>Foto</th>
</tr>

<?php

$data=mysqli_query($conn,"
SELECT * FROM tbalumni
ORDER BY id DESC
");

while($d=mysqli_fetch_assoc($data)){
?>

<tr>

<td><?= $d['id']; ?></td>
<td><?= $d['nisn']; ?></td>
<td><?= $d['nama']; ?></td>
<td><?= $d['jurusan']; ?></td>
<td><?= $d['tahun_lulus']; ?></td>

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
