<?php

require 'vendor/autoload.php';

use Aws\S3\S3Client;

/* =====================
   KONEKSI RDS
===================== */

$host = "RDS-ENDPOINT";
$user = "admin";
$pass = "Password123";
$db   = "dbperpustakaan";

$conn = mysqli_connect($host,$user,$pass,$db);

if(!$conn){
    die("Koneksi RDS gagal");
}

/* =====================
   S3 CONFIG
===================== */

$bucketCover = "cover-buku-perpustakaan";

$s3 = new S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);

/* =====================
   SIMPAN DATA
===================== */

if(isset($_POST['simpan']))
{
    $kode    = $_POST['kode_buku'];
    $judul   = $_POST['judul_buku'];
    $penulis = $_POST['penulis'];
    $tahun   = $_POST['tahun_terbit'];

    $urlCover = "";

    if(!empty($_FILES['cover']['tmp_name']))
    {
        $namaFile = time().'_'.$_FILES['cover']['name'];

        $upload = $s3->putObject([
            'Bucket'     => $bucketCover,
            'Key'        => $namaFile,
            'SourceFile' => $_FILES['cover']['tmp_name']
        ]);

        $urlCover = $upload['ObjectURL'];
    }

    mysqli_query($conn,"
    INSERT INTO tbbuku(
        kode_buku,
        judul_buku,
        penulis,
        tahun_terbit,
        cover_buku
    )
    VALUES(
        '$kode',
        '$judul',
        '$penulis',
        '$tahun',
        '$urlCover'
    )
    ");

    header("Location:index.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Data Buku Perpustakaan</title>

<style>

body{
    font-family:Arial;
    margin:20px;
}

table{
    width:100%;
    border-collapse:collapse;
}

table,th,td{
    border:1px solid #000;
    padding:8px;
}

img{
    border-radius:5px;
}

input{
    width:300px;
    padding:5px;
}

</style>

</head>

<body>

<h2>Data Buku Perpustakaan</h2>

<form method="POST" enctype="multipart/form-data">

<p>
Kode Buku<br>
<input type="text" name="kode_buku" required>
</p>

<p>
Judul Buku<br>
<input type="text" name="judul_buku" required>
</p>

<p>
Penulis<br>
<input type="text" name="penulis" required>
</p>

<p>
Tahun Terbit<br>
<input type="number" name="tahun_terbit" required>
</p>

<p>
Upload Cover Buku<br>
<input type="file" name="cover" required>
</p>

<button type="submit" name="simpan">
Simpan Data
</button>

</form>

<hr>

<h3>Daftar Buku</h3>

<table>

<tr>
    <th>ID</th>
    <th>Kode Buku</th>
    <th>Judul Buku</th>
    <th>Penulis</th>
    <th>Tahun</th>
    <th>Cover</th>
</tr>

<?php

$data = mysqli_query($conn,"
SELECT * FROM tbbuku
ORDER BY id DESC
");

while($d=mysqli_fetch_assoc($data))
{
?>

<tr>

<td><?= $d['id']; ?></td>

<td><?= $d['kode_buku']; ?></td>

<td><?= $d['judul_buku']; ?></td>

<td><?= $d['penulis']; ?></td>

<td><?= $d['tahun_terbit']; ?></td>

<td>

<?php if($d['cover_buku']!=""){ ?>

<img
src="<?= $d['cover_buku']; ?>"
width="120">

<?php } ?>

</td>

</tr>

<?php
}
?>

</table>

</body>
</html>
