<?php

require __DIR__ . '/vendor/autoload.php';

use Aws\S3\S3Client;

/* ==========================
   KONFIGURASI RDS
========================== */

$host = "dbsiswa.c83ya4kmsi7u.us-east-1.rds.amazonaws.com";
$user = "admin";
$pass = "admin2026";
$db   = "dbsiswa";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi RDS gagal: " . mysqli_connect_error());
}

/* ==========================
   KONFIGURASI S3
========================== */

$s3 = new S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1' // 
]);

$bucket = "foto-siswa-bucket";

/* ==========================
   SIMPAN DATA
========================== */

if (isset($_POST['simpan'])) {

    $nis    = mysqli_real_escape_string($conn, $_POST['nis']);
    $nama   = mysqli_real_escape_string($conn, $_POST['nama']);
    $kelas  = mysqli_real_escape_string($conn, $_POST['kelas']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    $fotoUrl = "";

    if (!empty($_FILES['foto']['name'])) {

        $namaFile = time() . "_" . basename($_FILES['foto']['name']);

        try {

            $upload = $s3->putObject([
                'Bucket'     => $bucket,
                'Key'        => 'siswa/' . $namaFile,
                'SourceFile' => $_FILES['foto']['tmp_name']
            ]);

            $fotoUrl = $upload['ObjectURL'];

        } catch (Exception $e) {

            die("Upload S3 gagal: " . $e->getMessage());
        }
    }

    $sql = "INSERT INTO siswa(nis,nama,kelas,alamat,foto)
            VALUES('$nis','$nama','$kelas','$alamat','$fotoUrl')";

    mysqli_query($conn, $sql);

    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Aplikasi Data Siswa AWS</title>

<style>

body{
    font-family: Arial, sans-serif;
    margin:40px;
    background:#f4f6f9;
}

.container{
    background:white;
    padding:20px;
    border-radius:10px;
}

h2{
    color:#333;
}

input, textarea{
    width:100%;
    padding:10px;
    margin-top:5px;
    margin-bottom:15px;
}

button{
    background:#007bff;
    color:white;
    border:none;
    padding:10px 20px;
    cursor:pointer;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

table th, table td{
    border:1px solid #ddd;
    padding:10px;
}

table th{
    background:#007bff;
    color:white;
}

img{
    border-radius:5px;
}

</style>

</head>
<body>

<div class="container">

<h2>Data Siswa</h2>

<form method="POST" enctype="multipart/form-data">

<label>NIS</label>
<input type="text" name="nis" required>

<label>Nama</label>
<input type="text" name="nama" required>

<label>Kelas</label>
<input type="text" name="kelas" required>

<label>Alamat</label>
<textarea name="alamat"></textarea>

<label>Foto</label>
<input type="file" name="foto" required>

<button type="submit" name="simpan">
Simpan Data
</button>

</form>

<hr>

<h2>Daftar Siswa</h2>

<table>

<tr>
    <th>ID</th>
    <th>Foto</th>
    <th>NIS</th>
    <th>Nama</th>
    <th>Kelas</th>
    <th>Alamat</th>
</tr>

<?php

$data = mysqli_query($conn, "SELECT * FROM siswa ORDER BY id DESC");

while($row = mysqli_fetch_assoc($data))
{
?>

<tr>

<td><?= $row['id']; ?></td>

<td>
<?php if($row['foto']) { ?>
<img src="<?= $row['foto']; ?>" width="80">
<?php } ?>
</td>

<td><?= $row['nis']; ?></td>
<td><?= $row['nama']; ?></td>
<td><?= $row['kelas']; ?></td>
<td><?= $row['alamat']; ?></td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>
