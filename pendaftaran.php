<?php
require 'vendor/autoload.php';

use Aws\S3\S3Client;

$conn=mysqli_connect(
"RDS-ENDPOINT",
"admin",
"password",
"dbpendaftar"
);

if(isset($_POST['simpan'])){

$nomor=$_POST['nomor_formulir'];
$nisn=$_POST['nisn'];
$nama=$_POST['nama'];
$asal=$_POST['asal_sekolah'];

$s3 = new S3Client([
'version'=>'latest',
'region'=>'us-east-1'
]);

$file=$_FILES['foto']['tmp_name'];
$namaFile=time().$_FILES['foto']['name'];

$upload=$s3->putObject([
'Bucket'=>'bucket-zona1b',
'Key'=>$namaFile,
'SourceFile'=>$file
]);

$url=$upload['ObjectURL'];

mysqli_query($conn,"
INSERT INTO tbpendaftar
(nomor_formulir,nisn,nama,asal_sekolah,foto)
VALUES
('$nomor','$nisn','$nama','$asal','$url')
");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Pendaftaran Siswa Baru</title>
</head>
<body>

<h2>Pendaftaran Siswa Baru</h2>

<form method="POST" enctype="multipart/form-data">

Nomor Formulir<br>
<input type="text" name="nomor_formulir"><br><br>

NISN<br>
<input type="text" name="nisn"><br><br>

Nama<br>
<input type="text" name="nama"><br><br>

Asal Sekolah<br>
<input type="text" name="asal_sekolah"><br><br>

Foto<br>
<input type="file" name="foto"><br><br>

<button name="simpan">Simpan</button>

</form>

<hr>

<table border="1">
<tr>
<th>ID</th>
<th>Formulir</th>
<th>NISN</th>
<th>Nama</th>
<th>Asal Sekolah</th>
<th>Foto</th>
</tr>

<?php

$data=mysqli_query($conn,"SELECT * FROM tbpendaftar");

while($d=mysqli_fetch_array($data)){

echo "
<tr>
<td>$d[id]</td>
<td>$d[nomor_formulir]</td>
<td>$d[nisn]</td>
<td>$d[nama]</td>
<td>$d[asal_sekolah]</td>
<td><img src='$d[foto]' width='100'></td>
</tr>
";
}
?>

</table>

</body>
</html>
