<?php

require 'vendor/autoload.php';

use Aws\S3\S3Client;

$host   = "dblatihanlks2026.cs16kc6eazau.us-east-1.rds.amazonaws.com";
$user   = "admin";
$pass   = "admin2026";
$db     = "dbpendaftar";

$bucket = "traininglks2026";
$region = "us-east-1";

$conn = mysqli_connect($host,$user,$pass,$db);

if(!$conn){
    die("Koneksi RDS gagal : ".mysqli_connect_error());
}

$s3 = new S3Client([
    'version' => 'latest',
    'region'  => $region
]);

if(isset($_POST['simpan'])){

    $nomor_formulir = mysqli_real_escape_string($conn,$_POST['nomor_formulir']);
    $nisn           = mysqli_real_escape_string($conn,$_POST['nisn']);
    $nama           = mysqli_real_escape_string($conn,$_POST['nama']);
    $asal_sekolah   = mysqli_real_escape_string($conn,$_POST['asal_sekolah']);

    $fotoUrl = "";

    if(isset($_FILES['foto']) && $_FILES['foto']['error']==0){

        try{

            $namaFile = "pendaftar/".time()."_".basename($_FILES['foto']['name']);

            $s3->putObject([
                'Bucket'     => $bucket,
                'Key'        => $namaFile,
                'SourceFile' => $_FILES['foto']['tmp_name']
            ]);

            $fotoUrl = "https://".$bucket.".s3.".$region.".amazonaws.com/".$namaFile;

        }catch(Exception $e){

            die("Upload S3 gagal : ".$e->getMessage());
        }
    }

    mysqli_query($conn,"
    INSERT INTO tbpendaftar
    (nomor_formulir,nisn,nama,asal_sekolah,foto)
    VALUES
    ('$nomor_formulir','$nisn','$nama','$asal_sekolah','$fotoUrl')
    ");
}

if(isset($_GET['hapus'])){

    $id = (int)$_GET['hapus'];

    mysqli_query($conn,"
    DELETE FROM tbpendaftar
    WHERE id='$id'
    ");
}

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Pendaftaran Siswa Baru</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

<div class="container mt-4">

<h2 class="mb-4">Pendaftaran Siswa Baru</h2>

<div class="card">
<div class="card-body">

<form method="post" enctype="multipart/form-data">

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

<table class="table table-bordered table-striped">

<thead>
<tr>
<th>ID</th>
<th>No Formulir</th>
<th>NISN</th>
<th>Nama</th>
<th>Asal Sekolah</th>
<th>Foto</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>

<?php

$data = mysqli_query($conn,"
SELECT *
FROM tbpendaftar
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

<img src="<?= $d['foto']; ?>" width="100">

<?php } ?>

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

</tbody>

</table>

</div>

</body>
</html>
