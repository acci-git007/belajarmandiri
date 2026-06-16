<?php

require __DIR__.'/vendor/autoload.php';

use Aws\S3\S3Client;

/* ==========================
   KONEKSI RDS
========================== */

$conn = mysqli_connect(
    "",
    "admin",
    "PASSWORD_RDS",
    "dbpenjualan"
);

if (!$conn) {
    die("Koneksi RDS gagal : " . mysqli_connect_error());
}

/* ==========================
   KONEKSI S3
========================== */

$bucket = "NAMA_BUCKET_S3";

$s3 = new S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);

/* ==========================
   SIMPAN DATA
========================== */

if(isset($_POST['simpan'])){

    $kode     = $_POST['kode_barang'];
    $nama     = $_POST['nama_barang'];
    $kategori = $_POST['kategori'];
    $stok     = $_POST['stok'];
    $harga    = $_POST['harga'];

    $fotoUrl = "";

    if(!empty($_FILES['foto']['name'])){

        $namaFile =
        time().'_'.basename($_FILES['foto']['name']);

        try{

            $upload = $s3->putObject([
                'Bucket'     => $bucket,
                'Key'        => 'stock/'.$namaFile,
                'SourceFile' => $_FILES['foto']['tmp_name']
            ]);

            $fotoUrl = $upload['ObjectURL'];

        }catch(Exception $e){

            die("Upload S3 gagal : ".$e->getMessage());
        }
    }

    mysqli_query($conn,"
    INSERT INTO tbstock
    (
        kode_barang,
        nama_barang,
        kategori,
        stok,
        harga,
        foto
    )
    VALUES
    (
        '$kode',
        '$nama',
        '$kategori',
        '$stok',
        '$harga',
        '$fotoUrl'
    )
    ");

    header("Location: stock.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Data Stock Barang</title>

<style>

body{
    font-family:Arial;
    background:#f4f4f4;
    margin:20px;
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

<h2>Input Stock Barang</h2>

<form method="post" enctype="multipart/form-data">

<input type="text"
       name="kode_barang"
       placeholder="Kode Barang"
       required>

<input type="text"
       name="nama_barang"
       placeholder="Nama Barang"
       required>

<input type="text"
       name="kategori"
       placeholder="Kategori"
       required>

<input type="number"
       name="stok"
       placeholder="Jumlah Stock"
       required>

<input type="number"
       name="harga"
       placeholder="Harga"
       required>

<input type="file"
       name="foto"
       required>

<button type="submit"
        name="simpan">
Simpan
</button>

</form>

<hr>

<h2>Data Stock Barang</h2>

<table>

<tr>
    <th>ID</th>
    <th>Foto</th>
    <th>Kode</th>
    <th>Nama Barang</th>
    <th>Kategori</th>
    <th>Stock</th>
    <th>Harga</th>
</tr>

<?php

$data = mysqli_query(
$conn,
"SELECT * FROM tbstock ORDER BY id DESC"
);

while($row=mysqli_fetch_assoc($data)){
?>

<tr>

<td><?= $row['id']; ?></td>

<td>
<?php if(!empty($row['foto'])){ ?>
<img src="<?= $row['foto']; ?>" width="100">
<?php } ?>
</td>

<td><?= $row['kode_barang']; ?></td>
<td><?= $row['nama_barang']; ?></td>
<td><?= $row['kategori']; ?></td>
<td><?= $row['stok']; ?></td>
<td><?= number_format($row['harga']); ?></td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>
