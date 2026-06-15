<?php

require __DIR__ . '/vendor/autoload.php';

use Aws\S3\S3Client;

/* ==================================
   KONFIGURASI RDS
================================== */

$host = "db-penjualan-skincare.c83ya4kmsi7u.us-east-1.rds.amazonaws.com";
$user = "admin";
$pass = "admin2026";
$db   = "dbskincare";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi RDS gagal: " . mysqli_connect_error());
}

/* ==================================
   KONFIGURASI S3
================================== */

$bucket = "penjualan-skincare-s3";

$s3 = new S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);

/* ==================================
   SIMPAN DATA
================================== */

if (isset($_POST['simpan'])) {

    $nama     = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $harga    = mysqli_real_escape_string($conn, $_POST['harga']);
    $stok     = mysqli_real_escape_string($conn, $_POST['stok']);

    $fotoUrl = '';

    if (!empty($_FILES['foto']['name'])) {

        $namaFile = time() . "_" . basename($_FILES['foto']['name']);

        try {

            $upload = $s3->putObject([
                'Bucket'      => $bucket,
                'Key'         => 'produk/' . $namaFile,
                'SourceFile'  => $_FILES['foto']['tmp_name'],
                'ContentType' => $_FILES['foto']['type']
            ]);

            $fotoUrl = $upload['ObjectURL'];

        } catch (Exception $e) {

            die("Upload S3 gagal : " . $e->getMessage());
        }
    }

    $sql = "INSERT INTO produk
            (nama_produk,kategori,harga,stok,foto)
            VALUES
            ('$nama','$kategori','$harga','$stok','$fotoUrl')";

    mysqli_query($conn, $sql);

    header("Location:index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Penjualan Skincare</title>

<style>

body{
    font-family:Arial;
    background:#f4f4f4;
    margin:30px;
}

.container{
    background:#fff;
    padding:20px;
    border-radius:10px;
}

input{
    width:100%;
    padding:10px;
    margin-bottom:10px;
}

button{
    background:#28a745;
    color:white;
    border:none;
    padding:10px 20px;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

table th,
table td{
    border:1px solid #ddd;
    padding:10px;
}

table th{
    background:#28a745;
    color:white;
}

img{
    border-radius:5px;
}

</style>

</head>
<body>

<div class="container">

<h2>Input Produk Skincare</h2>

<form method="POST" enctype="multipart/form-data">

<input type="text"
       name="nama_produk"
       placeholder="Nama Produk"
       required>

<input type="text"
       name="kategori"
       placeholder="Kategori (Serum, Toner, Moisturizer)"
       required>

<input type="number"
       name="harga"
       placeholder="Harga"
       required>

<input type="number"
       name="stok"
       placeholder="Stok"
       required>

<input type="file"
       name="foto"
       required>

<button type="submit" name="simpan">
Simpan Produk
</button>

</form>

<hr>

<h2>Daftar Produk</h2>

<table>

<tr>
    <th>ID</th>
    <th>Foto</th>
    <th>Nama Produk</th>
    <th>Kategori</th>
    <th>Harga</th>
    <th>Stok</th>
</tr>

<?php

$data = mysqli_query($conn,
        "SELECT * FROM produk ORDER BY id DESC");

while($row = mysqli_fetch_assoc($data))
{
?>

<tr>

<td><?php echo $row['id']; ?></td>

<td>

<?php if(!empty($row['foto'])) { ?>

<img src="<?php echo $row['foto']; ?>"
     width="100"
     height="100">

<?php } ?>

</td>

<td><?php echo $row['nama_produk']; ?></td>
<td><?php echo $row['kategori']; ?></td>
<td>Rp <?php echo number_format($row['harga']); ?></td>
<td><?php echo $row['stok']; ?></td>

</tr>

<?php
}
?>

</table>

</div>

</body>
</html>
