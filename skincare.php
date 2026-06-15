<?php

require __DIR__.'/vendor/autoload.php';

use Aws\S3\S3Client;

$conn = mysqli_connect(
    "dbpenjualan.c83ya4kmsi7u.us-east-1.rds.amazonaws.com",
    "admin",
    "admin2026",
    "dbskincare"
);

$bucket = "penjualan.bucket";

$s3 = new S3Client([
    'version'=>'latest',
    'region'=>'us-east-1'
]);

if(isset($_POST['simpan'])){

    $nama     = $_POST['nama_produk'];
    $kategori = $_POST['kategori'];
    $harga    = $_POST['harga'];
    $stok     = $_POST['stok'];

    $fotoUrl = "";

    if($_FILES['foto']['name']!=""){

        $namaFile = time().'_'.$_FILES['foto']['name'];

        $upload = $s3->putObject([
            'Bucket'=>$bucket,
            'Key'=>'produk/'.$namaFile,
            'SourceFile'=>$_FILES['foto']['tmp_name']
        ]);

        $fotoUrl = $upload['ObjectURL'];
    }

    mysqli_query($conn,"
        INSERT INTO produk_skincare
        (nama_produk,kategori,harga,stok,foto)
        VALUES
        ('$nama','$kategori','$harga','$stok','$fotoUrl')
    ");

    header("Location:index.php");
}
?>

<form method="post" enctype="multipart/form-data">

<input type="text" name="nama_produk" placeholder="Nama Produk"><br><br>

<input type="text" name="kategori" placeholder="Kategori"><br><br>

<input type="number" name="harga" placeholder="Harga"><br><br>

<input type="number" name="stok" placeholder="Stok"><br><br>

<input type="file" name="foto"><br><br>

<button name="simpan">Simpan</button>

</form>

<hr>

<table border="1" cellpadding="10">

<tr>
<th>ID</th>
<th>Foto</th>
<th>Nama Produk</th>
<th>Kategori</th>
<th>Harga</th>
<th>Stok</th>
</tr>

<?php

$data = mysqli_query(
$conn,
"SELECT * FROM produk_skincare ORDER BY id DESC"
);

while($row=mysqli_fetch_assoc($data)){
?>

<tr>
<td><?= $row['id']; ?></td>

<td>
<img src="<?= $row['foto']; ?>" width="80">
</td>

<td><?= $row['nama_produk']; ?></td>
<td><?= $row['kategori']; ?></td>
<td><?= $row['harga']; ?></td>
<td><?= $row['stok']; ?></td>
</tr>

<?php } ?>

</table>
