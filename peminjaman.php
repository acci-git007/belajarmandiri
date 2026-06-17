<?php

require 'vendor/autoload.php';

use Aws\S3\S3Client;

/* =====================
   KONEKSI RDS
===================== */

$host = "RDS-ENDPOINT";
$user = "admin";
$pass = "Password123";
$db   = "dbpeminjaman";

$conn = mysqli_connect($host,$user,$pass,$db);

if(!$conn){
    die("Koneksi RDS gagal");
}

/* =====================
   KONFIGURASI S3
===================== */

$bucketDokumen = "dokumen-perpustakaan";

$s3 = new S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);

/* =====================
   SIMPAN DATA
===================== */

if(isset($_POST['simpan']))
{
    $kode     = $_POST['kode_pinjam'];
    $nama     = $_POST['nama_peminjam'];
    $nim      = $_POST['nim_nis'];
    $email    = $_POST['email'];
    $hp       = $_POST['no_hp'];
    $judul    = $_POST['judul_buku'];
    $tglPinjam= $_POST['tanggal_pinjam'];
    $tglKembali=$_POST['tanggal_kembali'];

    $urlDokumen = "";

    if(!empty($_FILES['dokumen']['tmp_name']))
    {
        $namaFile = time().'_'.$_FILES['dokumen']['name'];

        $upload = $s3->putObject([
            'Bucket'     => $bucketDokumen,
            'Key'        => $namaFile,
            'SourceFile' => $_FILES['dokumen']['tmp_name']
        ]);

        $urlDokumen = $upload['ObjectURL'];
    }

    mysqli_query($conn,"
    INSERT INTO tbpeminjaman(
        kode_pinjam,
        nama_peminjam,
        nim_nis,
        email,
        no_hp,
        judul_buku,
        tanggal_pinjam,
        tanggal_kembali,
        dokumen
    )
    VALUES(
        '$kode',
        '$nama',
        '$nim',
        '$email',
        '$hp',
        '$judul',
        '$tglPinjam',
        '$tglKembali',
        '$urlDokumen'
    )
    ");

    header("Location:index.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Aplikasi Peminjaman Buku AWS</title>

<style>
body{
    font-family: Arial;
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

input{
    width:300px;
    padding:5px;
}
</style>

</head>
<body>

<h2>Data Peminjaman Buku</h2>

<form method="POST" enctype="multipart/form-data">

<p>
Kode Peminjaman<br>
<input type="text" name="kode_pinjam" required>
</p>

<p>
Nama Peminjam<br>
<input type="text" name="nama_peminjam" required>
</p>

<p>
NIM/NIS<br>
<input type="text" name="nim_nis" required>
</p>

<p>
Email<br>
<input type="email" name="email">
</p>

<p>
No HP<br>
<input type="text" name="no_hp">
</p>

<p>
Judul Buku<br>
<input type="text" name="judul_buku" required>
</p>

<p>
Tanggal Pinjam<br>
<input type="date" name="tanggal_pinjam" required>
</p>

<p>
Tanggal Kembali<br>
<input type="date" name="tanggal_kembali" required>
</p>

<p>
Upload Dokumen PDF<br>
<input type="file" name="dokumen" required>
</p>

<button type="submit" name="simpan">
Simpan Data
</button>

</form>

<hr>

<h3>Daftar Peminjaman</h3>

<table>

<tr>
    <th>ID</th>
    <th>Kode</th>
    <th>Nama</th>
    <th>NIM/NIS</th>
    <th>Judul Buku</th>
    <th>Tgl Pinjam</th>
    <th>Tgl Kembali</th>
    <th>Dokumen</th>
</tr>

<?php

$data = mysqli_query($conn,"
SELECT * FROM tbpeminjaman
ORDER BY id DESC
");

while($d=mysqli_fetch_assoc($data))
{
?>

<tr>

<td><?= $d['id']; ?></td>

<td><?= $d['kode_pinjam']; ?></td>

<td><?= $d['nama_peminjam']; ?></td>

<td><?= $d['nim_nis']; ?></td>

<td><?= $d['judul_buku']; ?></td>

<td><?= $d['tanggal_pinjam']; ?></td>

<td><?= $d['tanggal_kembali']; ?></td>

<td>
<?php if($d['dokumen']!=""){ ?>
<a href="<?= $d['dokumen']; ?>" target="_blank">
Lihat Dokumen
</a>
<?php } ?>
</td>

</tr>

<?php
}
?>

</table>

</body>
</html>
