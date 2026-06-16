<?php
require 'vendor/autoload.php';

use Aws\S3\S3Client;

$conn=mysqli_connect(
"RDS-ENDPOINT",
"admin",
"password",
"dbalumni"
);

if(isset($_POST['simpan'])){

$nisn=$_POST['nisn'];
$nama=$_POST['nama'];
$jurusan=$_POST['jurusan'];
$tahun=$_POST['tahun_lulus'];

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
INSERT INTO tbalumni
(nisn,nama,jurusan,tahun_lulus,foto)
VALUES
('$nisn','$nama','$jurusan','$tahun','$url')
");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Data Alumni</title>
</head>
<body>

<h2>Data Alumni</h2>

<form method="POST" enctype="multipart/form-data">

NISN<br>
<input type="text" name="nisn"><br><br>

Nama<br>
<input type="text" name="nama"><br><br>

Jurusan<br>
<input type="text" name="jurusan"><br><br>

Tahun Lulus<br>
<input type="number" name="tahun_lulus"><br><br>

Foto<br>
<input type="file" name="foto"><br><br>

<button name="simpan">Simpan</button>

</form>

<hr>

<table border="1">

<tr>
<th>ID</th>
<th>NISN</th>
<th>Nama</th>
<th>Jurusan</th>
<th>Tahun Lulus</th>
<th>Foto</th>
</tr>

<?php

$data=mysqli_query($conn,"SELECT * FROM tbalumni");

while($d=mysqli_fetch_array($data)){

echo "
<tr>
<td>$d[id]</td>
<td>$d[nisn]</td>
<td>$d[nama]</td>
<td>$d[jurusan]</td>
<td>$d[tahun_lulus]</td>
<td><img src='$d[foto]' width='100'></td>
</tr>
";
}
?>

</table>

</body>
</html>
