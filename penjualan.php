<?php

require 'vendor/autoload.php';

use Aws\S3\S3Client;

$host = "database-skincare.xxxxx.us-east-1.rds.amazonaws.com";
$user = "admin";
$pass = "password123";
$db   = "db_skincare";

$conn = new mysqli($host,$user,$pass,$db);

if($conn->connect_error){
    die("Koneksi gagal: ".$conn->connect_error);
}

$s3 = new S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1',
    'credentials' => [
        'key'    => 'AKIAxxxxxxxx',
        'secret' => 'xxxxxxxxxxxxxxxx'
    ]
]);

$bucket = "bucket-skincare";

if(isset($_POST['simpan'])){

    $nama_produk = $_POST['nama_produk'];
    $merk = $_POST['merk'];
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    $foto_url = '';

    if($_FILES['foto']['name'] != ''){

        $file_tmp = $_FILES['foto']['tmp_name'];
        $file_name = time().'_'.$_FILES['foto']['name'];

        try{

            $upload = $s3->putObject([
                'Bucket' => $bucket,
                'Key'    => $file_name,
                'SourceFile' => $file_tmp,
                'ACL' => 'public-read'
            ]);

            $foto_url = $upload['ObjectURL'];

        }catch(Exception $e){
            die("Upload gagal : ".$e->getMessage());
        }
    }

    $sql = "INSERT INTO penjualan_skincare
            (nama_produk,merk,kategori,harga,stok,foto)
            VALUES
            ('$nama_produk','$merk','$kategori','$harga','$stok','$foto_url')";

    $conn->query($sql);

    header("Location:index.php");
}

if(isset($_GET['hapus'])){

    $id = $_GET['hapus'];

    $conn->query("DELETE FROM penjualan_skincare WHERE id='$id'");

    header("Location:index.php");
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Penjualan Skincare AWS</title>

<style>

body{
    font-family: Arial;
    background:#f5f5f5;
    padding:20px;
}

.container{
    background:white;
    padding:20px;
    border-radius:10px;
}

input{
    width:100%;
    padding:10px;
    margin-bottom:10px;
}

button{
    background:#ff69b4;
    color:white;
    border:none;
    padding:10px 20px;
}

table{
    width:100%;
    margin-top:20px;
    border-collapse:collapse;
}

table,th,td{
    border:1px solid #ddd;
}

th,td{
    padding:10px;
}

img{
    width:100px;
}

</style>

</head>
<body>

<div class="container">

<h2>Penjualan Skincare</h2>

<form method="POST" enctype="multipart/form-data">

<input type="text" name="nama_produk" placeholder="Nama Produk" required>

<input type="text" name="merk" placeholder="Merk" required>

<input type="text" name="kategori" placeholder="Kategori" required>

<input type="number" name="harga" placeholder="Harga" required>

<input type="number" name="stok" placeholder="Stok" required>

<input type="file" name="foto" required>

<button type="submit" name="simpan">
Simpan
</button>

</form>

<table>

<tr>
<th>ID</th>
<th>Produk</th>
<th>Merk</th>
<th>Kategori</th>
<th>Harga</th>
<th>Stok</th>
<th>Foto</th>
<th>Aksi</th>
</tr>

<?php

$data = $conn->query("SELECT * FROM penjualan_skincare");

while($d = $data->fetch_assoc()){

?>

<tr>

<td><?= $d['id']; ?></td>

<td><?= $d['nama_produk']; ?></td>

<td><?= $d['merk']; ?></td>

<td><?= $d['kategori']; ?></td>

<td>Rp <?= number_format($d['harga']); ?></td>

<td><?= $d['stok']; ?></td>

<td>
<img src="<?= $d['foto']; ?>">
</td>

<td>
<a href="?hapus=<?= $d['id']; ?>"
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
