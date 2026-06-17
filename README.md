JSON IAM 

{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Sid": "BucketAccess",
      "Effect": "Allow",
      "Action": [
        "s3:ListBucket"
      ],
      "Resource": "arn:aws:s3:::traininglks2026"
    },
    {
      "Sid": "ObjectAccess",
      "Effect": "Allow",
      "Action": [
        "s3:GetObject",
        "s3:PutObject",
        "s3:DeleteObject"
      ],
      "Resource": "arn:aws:s3:::traininglks2026/*"
    }
  ]
}






BUCKET POLICY


{
  "Version":"2012-10-17",
  "Statement":[
    {
      "Sid":"PublicRead",
      "Effect":"Allow",
      "Principal":"*",
      "Action":"s3:GetObject",
      "Resource":"arn:aws:s3:::traininglks2026/*"
    }
  ]
}





PENDAFTARAN SISWA


CREATE DATABASE dbpendaftar;

USE dbpendaftar;

CREATE TABLE tbpendaftar(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_formulir VARCHAR(20),
    nisn VARCHAR(20),
    nama VARCHAR(100),
    asal_sekolah VARCHAR(100),
    foto VARCHAR(500)
);





ALUMNI


CREATE DATABASE dbalumni;

USE dbalumni;

CREATE TABLE tbalumni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nisn VARCHAR(20) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    jurusan VARCHAR(100) NOT NULL,
    tahun_lulus YEAR NOT NULL,
    foto VARCHAR(500)
);










JSON IAM

{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "ListBuckets",
            "Effect": "Allow",
            "Action": [
                "s3:ListBucket"
            ],
            "Resource": [
                "arn:aws:s3:::cover-buku-perpustakaan",
                "arn:aws:s3:::dokumen-perpustakaan"
            ]
        },
        {
            "Sid": "ObjectAccess",
            "Effect": "Allow",
            "Action": [
                "s3:GetObject",
                "s3:PutObject",
                "s3:DeleteObject"
            ],
            "Resource": [
                "arn:aws:s3:::cover-buku-perpustakaan/*",
                "arn:aws:s3:::dokumen-perpustakaan/*"
            ]
        }
    ]
}










bucket s3

{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Sid": "PublicReadImages",
      "Effect": "Allow",
      "Principal": "*",
      "Action": [
        "s3:GetObject"
      ],
      "Resource": [
        "arn:aws:s3:::cover-buku-perpustakaan/*"
      ]
    }
  ]
}






samul buku

CREATE DATABASE dbperpustakaan;

USE dbperpustakaan;

CREATE TABLE tbbuku (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_buku VARCHAR(20) NOT NULL,
    judul_buku VARCHAR(150) NOT NULL,
    penulis VARCHAR(100) NOT NULL,
    tahun_terbit INT NOT NULL,
    cover_buku VARCHAR(255),
    dokumen VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);







CREATE DATABASE dbpeminjaman;

USE dbpeminjaman;

CREATE TABLE tbpeminjaman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_pinjam VARCHAR(20) NOT NULL,
    nama_peminjam VARCHAR(100) NOT NULL,
    nim_nis VARCHAR(30) NOT NULL,
    judul_buku VARCHAR(150) NOT NULL,
    tanggal_pinjam DATE NOT NULL,
    tanggal_kembali DATE NOT NULL,
    dokumen VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
