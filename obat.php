<?php

$conn = mysqli_connect(
    "dbpenjualan.c83ya4kmsi7u.us-east-1.rds.amazonaws.com",
    "admin",
    "admin2026",
    "dbobat"
);

if(isset($_POST['simpan'])){

    $kode  = $_POST['kode_obat'];
    $nama  = $_POST['nama_obat'];
    $jenis = $_POST['jenis_obat'];
    $harga = $_POST['harga'];
    $stok  = $_POST['stok'];

    mysqli_query($conn,"
    INSERT INTO produk_obat
    (kode_obat,nama_obat,jenis_obat,harga,stok)
    VALUES
    ('$kode','$nama','$jenis','$harga','$stok')
    ");

    header("Location:index.php");
}
?>

<form method="post">

<input type="text" name="kode_obat" placeholder="Kode Obat"><br><br>

<input type="text" name="nama_obat" placeholder="Nama Obat"><br><br>

<input type="text" name="jenis_obat" placeholder="Jenis Obat"><br><br>

<input type="number" name="harga" placeholder="Harga"><br><br>

<input type="number" name="stok" placeholder="Stok"><br><br>

<button name="simpan">Simpan</button>

</form>

<hr>

<table border="1" cellpadding="10">

<tr>
<th>ID</th>
<th>Kode</th>
<th>Nama Obat</th>
<th>Jenis</th>
<th>Harga</th>
<th>Stok</th>
</tr>

<?php

$data = mysqli_query(
$conn,
"SELECT * FROM produk_obat ORDER BY id DESC"
);

while($row=mysqli_fetch_assoc($data)){
?>

<tr>
<td><?= $row['id']; ?></td>
<td><?= $row['kode_obat']; ?></td>
<td><?= $row['nama_obat']; ?></td>
<td><?= $row['jenis_obat']; ?></td>
<td><?= $row['harga']; ?></td>
<td><?= $row['stok']; ?></td>
</tr>

<?php } ?>

</table>
