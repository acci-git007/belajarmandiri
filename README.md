CREATE DATABASE dbpendaftar;

USE dbpendaftar;

CREATE TABLE tbpendaftar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_formulir VARCHAR(20),
    nisn VARCHAR(20),
    nama VARCHAR(100),
    asal_sekolah VARCHAR(100),
    foto VARCHAR(255)
);


CREATE DATABASE dbalumni;

USE dbalumni;

CREATE TABLE tbalumni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nisn VARCHAR(20),
    nama VARCHAR(100),
    jurusan VARCHAR(100),
    tahun_lulus YEAR,
    foto VARCHAR(255)
);



IAM ROLE KE S3

{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Sid": "UploadDownloadS3",
      "Effect": "Allow",
      "Action": [
        "s3:PutObject",
        "s3:GetObject",
        "s3:DeleteObject"
      ],
      "Resource": [
        "arn:aws:s3:::bucket-zona1b/*"
      ]
    },
    {
      "Sid": "ListBucket",
      "Effect": "Allow",
      "Action": [
        "s3:ListBucket"
      ],
      "Resource": [
        "arn:aws:s3:::bucket-zona1b"
      ]
    }
  ]
}







policy ke s3 bucket


{
  "Version":"2012-10-17",
  "Statement":[
    {
      "Sid":"PublicReadImages",
      "Effect":"Allow",
      "Principal":"*",
      "Action":"s3:GetObject",
      "Resource":"arn:aws:s3:::bucket-zona1b/*"
    }
  ]
}








                 INTERNET
                      │

        ┌─────────────┴─────────────┐
        │                           │

        ▼                           ▼

  ALB-PENDAFTAR                ALB-ALUMNI
        │                           │
        ▼                           ▼

  TG-PENDAFTAR                 TG-ALUMNI
        │                           │

   ┌────┴────┐                 ┌────┴────┐
   │         │                 │         │

 EC2-1    EC2-2             EC2-3    EC2-4

        └─────────┬─────────┘
                  │
                  ▼

             RDS MySQL
       ┌─────────────────┐
       │ dbpendaftar     │
       │ dbalumni        │
       └─────────────────┘

                  │
                  ▼

                S3


