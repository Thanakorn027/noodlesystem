# Noodle System (โปรเจกต์สั่งอาหาร)

ไฟล์ตัวอย่างเว็บ PHP ขนาดเล็กสำหรับสาธิตการสั่งอาหาร (เมนู, ตะกร้า, ยืนยันออเดอร์) พร้อมฐานข้อมูล MySQL ที่มาพร้อมกับ SQL dump

**Prerequisites:**
- **Docker & Docker Compose** ติดตั้งบนเครื่อง
- พอร์ต `8080` และ `3307` ไม่ถูกใช้งานโดยโปรแกรมอื่น

**โครงสร้างไฟล์สำคัญ:**
- `index.php` : หน้าแสดงเมนู
- `add_to_cart.php` : เพิ่มรายการลงตะกร้า
- `review.php` : ตรวจสอบรายการสั่ง
- `confirm.php` : ยืนยันการสั่งและบันทึกลง DB
- `db.php` : ตั้งค่าการเชื่อมต่อฐานข้อมูล
- `4685002_project.sql` : SQL dump สำหรับสร้างตารางและข้อมูลตัวอย่าง
- `docker-compose.yml` : คอนฟิก Docker (web + db)

**วิธีรัน (คำแนะนำสั้น ๆ):**

1) เข้าไดเรกทอรีโปรเจกต์:
```
cd /Users/thanakorn/Downloads/noodle_system
```

2) เริ่มบริการด้วย Docker Compose (รันเป็นแบ็กกราวน์):
```
docker-compose up -d
```

3) ตรวจสถานะคอนเทนเนอร์และล็อก (ถ้าต้องการดีบัก):
```
docker-compose ps
docker-compose logs -f db
docker-compose logs -f web
```

4) ถ้าเป็นการรันครั้งแรก ไฟล์ SQL (`4685002_project.sql`) จะถูกแมปไปยัง `/docker-entrypoint-initdb.d/init.sql` ภายในคอนเทนเนอร์ฐานข้อมูล และ MySQL image จะรันสคริปต์นี้อัตโนมัติเพื่อสร้างฐานข้อมูล ถ้าไม่ถูกนำเข้าโดยอัตโนมัติ (เช่น container ถูกสร้างก่อนเพิ่มไฟล์) สามารถนำเข้าเองได้:
```
# สร้าง/รีเซ็ตฐานข้อมูลและนำเข้าไฟล์ dump (ใช้เมื่อจำเป็น)
docker-compose exec db bash -lc "mysql -u root -proot -e \"DROP DATABASE IF EXISTS 4685002_project; CREATE DATABASE 4685002_project DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;\" && (echo 'SET FOREIGN_KEY_CHECKS=0;'; cat /docker-entrypoint-initdb.d/init.sql; echo 'SET FOREIGN_KEY_CHECKS=1;') | mysql -u root -proot 4685002_project"
```

5) เปิดเว็บเบราว์เซอร์:
```
http://localhost:8080/index.php
```

**DB Credentials (ค่าที่ใช้ในโปรเจกต์):**
- host: `db` (ชื่อ service ใน `docker-compose.yml`)
- port (ภายในคอนเทนเนอร์): `3306` (แมปไปยัง `3307` บน host)
- root user: `root`
- password: `root`
- database: `4685002_project`

**ถ้าไม่ใช้ Docker (เร็ว ๆ):**
- ต้องมี PHP 8+ และ MySQL/MariaDB ติดตั้งบนเครื่อง
- import `4685002_project.sql` ลงฐานข้อมูล แล้วแก้ `db.php` ให้ชี้ไปที่ `localhost` แทน `db`
- รัน PHP built-in server (สำหรับทดสอบ):
```
cd /Users/thanakorn/Downloads/noodle_system
php -S localhost:8000
```
และเปิด `http://localhost:8000/index.php`

**การแก้ปัญหาเบื้องต้น:**
- หน้าแอปแสดงข้อความ Fatal error เกี่ยวกับฐานข้อมูล: ตรวจสอบว่า DB ถูกสร้างและตารางมีข้อมูลหรือไม่ (ดู logs ของ `db` และ `web`)
- ถ้า SQL import ล้มเหลวเพราะ foreign key: ใช้วิธี import โดยปิด `FOREIGN_KEY_CHECKS` ชั่วคราว (ตัวอย่างคำสั่งด้านบน)
- ถ้าไฟล์ `db.php` เป็นไฟล์ว่างหรือหาย ให้คืนค่าไฟล์ตามตัวอย่างใน repo เพื่อเชื่อมต่อ DB
- ถ้าไฟล์ไม่ถูกแมปเข้า container ให้ตรวจสิทธิ์/owner ของไฟล์บน host หรือแก้ `docker-compose.yml` ให้แมป path ที่ถูกต้อง

**สิ่งที่ผมได้แก้ให้ใน repository นี้:**
- คืนค่า `db.php` (เพิ่มการเชื่อมต่อ MySQL)
- แก้ชื่อไฟล์ SQL (`4685002_project.sql`) เพื่อเอาช่องว่างออก
- อัปเดต `docker-compose.yml` ให้แมตช์ฐานข้อมูลและแมปโฟลเดอร์โปรเจกต์เป็นเว็บ root
- เพิ่มการตรวจสอบ input และ query ใน `add_to_cart.php`, `confirm.php`, `review.php` และ escape output ใน `index.php`

**คำแนะนำต่อ:**
- ถ้าต้องการ ผมสามารถช่วยทำให้ปลอดภัยขึ้น (prepared statements), ล้าง/ปรับ permission ของไฟล์ที่แมปกับ Docker, หรือลบการเตือนเกี่ยวกับ `ServerName` ใน Apache

ถ้าต้องการให้ผมเพิ่มหัวข้อหรือคำอธิบายเพิ่มเติมใน `README.md` บอกได้เลยครับ
