[EN](README.md) | [TH](README.th.md)

<h1 align="center">PHP-CRUD-PDO-OOP</h1>

<p align="center">
  เว็บแอป CRUD ด้วย PHP + PDO ขนาดเล็กสำหรับการเรียนรู้: ดูรายการ เพิ่ม แก้ไข และลบผู้ใช้
</p>

<p align="center">
  <a href="#ภาพรวม">ภาพรวม</a> ·
  <a href="#ฟีเจอร์">ฟีเจอร์</a> ·
  <a href="#เทคโนโลยีที่ใช้">เทคโนโลยี</a> ·
  <a href="#โครงสร้างโปรเจกต์">โครงสร้าง</a> ·
  <a href="#การตั้งค่าฐานข้อมูล">ฐานข้อมูล</a> ·
  <a href="#เริ่มต้นใช้งาน">เริ่มต้นใช้งาน</a> ·
  <a href="#ตัวแปรสภาพแวดล้อม">ตัวแปรสภาพแวดล้อม</a> ·
  <a href="#วิธีใช้งาน">วิธีใช้งาน</a> ·
  <a href="#หมายเหตุ">หมายเหตุ</a>
</p>

---

## ภาพรวม

โปรเจกต์นี้เป็นตัวอย่างสำหรับการเรียนรู้การสร้างระบบ CRUD ด้วย PHP ล้วน ข้อมูลผู้ใช้ถูกเก็บในฐานข้อมูล MySQL/MariaDB เชื่อมต่อผ่าน PDO ในลักษณะ OOP (`Config` → `Database`) ส่วนหน้าจอใช้ Bootstrap 5 แบบ modal ร่วมกับ JavaScript พื้นฐานที่เรียก backend ผ่าน `fetch` (AJAX) จึงไม่ต้องโหลดหน้าใหม่ทั้งหน้าในทุกการทำงาน

โค้ดถูกแยกเป็นไฟล์ย่อยตามหน้าที่ชัดเจน ทำให้ไล่ลำดับการทำงานได้ง่ายตั้งแต่ต้นจนจบ:

1. `index.php` แสดงโครงหน้าเว็บ (ตาราง + modal สำหรับเพิ่ม/แก้ไข)
2. `main.js` โหลดรายชื่อผู้ใช้ และส่งคำขอเพิ่ม/แก้ไข/ลบ ไปยัง `action.php`
3. `action.php` ทำหน้าที่เป็นตัวควบคุม (controller) ทำความสะอาดข้อมูลด้วย `Util` แล้วเรียกเมธอดที่ตรงกันของ `Database`
4. `Database` (ใน `db.php`) รัน prepared statements กับตาราง `users` ผ่านการเชื่อมต่อ PDO ที่สร้างใน `Config` (ใน `config.php`)

## ฟีเจอร์

- แสดงรายชื่อผู้ใช้ทั้งหมดในตาราง Bootstrap ที่ปรับตามขนาดหน้าจอ เรียงจากใหม่ไปเก่า (`id DESC`)
- เพิ่มผู้ใช้ใหม่ผ่านฟอร์มใน modal ของ Bootstrap
- แก้ไขผู้ใช้เดิมใน modal โดยดึงข้อมูลมาเติมล่วงหน้าจาก `action.php?edit=1&id=...` (JSON)
- ลบผู้ใช้ได้ทันทีจากแถวของตน โดยไม่มีกล่องยืนยัน
- ไม่ต้องโหลดหน้าใหม่: ดูรายการ เพิ่ม แก้ไข และลบ ทำงานผ่าน `fetch` ทั้งหมด
- ใช้ prepared statements พร้อม named placeholders ทุกคำสั่ง
- ฟังก์ชันช่วยทำความสะอาดข้อมูลนำเข้า (`trim`, `stripcslashes`, `htmlspecialchars`, `strip_tags`)
- แจ้งผลด้วย alert ของ Bootstrap ที่ปิดได้
- แสดงแถวว่าง (`No users found in the Database`) เมื่อตารางไม่มีข้อมูล

## เทคโนโลยีที่ใช้

| ชั้น | เทคโนโลยี |
| --- | --- |
| Backend | PHP 8.2.35 / 8.3.36 / 8.4.27 / 8.5.12 หรือใหม่กว่า, `pdo_mysql`; จุดเข้าแบบ procedural + คลาส OOP |
| ฐานข้อมูล | MySQL / MariaDB (`InnoDB`, `utf8` / `utf8_general_ci`); dump สร้างจาก MariaDB 10.4.32 |
| การเข้าถึงข้อมูล | PDO พร้อม prepared statements และ named placeholders |
| Frontend | HTML5, Bootstrap 5.3.3 ผ่าน CDN jsDelivr, JavaScript ล้วน (Fetch API) |
| เครื่องมือ | ไฟล์ SQL dump จาก phpMyAdmin; ไม่มี Composer, npm หรือขั้นตอน build |

## โครงสร้างโปรเจกต์

```
learn-php-crud-pdo/
├── .gitattributes                  # LF line-ending normalisation rule
├── README.md                       # English documentation (this file)
├── README.th.md                    # Thai documentation
├── config.php                      # DB connection configuration class (PDO)
├── db.php                          # Database class: CRUD methods
├── util.php                        # Util class: input sanitising + alert markup
├── action.php                      # Controller: routes requests to Database
├── index.php                       # Page shell: table + add/edit modals
├── main.js                         # Frontend logic: fetch calls, modal wiring
└── database/
    └── php_crud_pdo_oop.sql        # Schema + seed data (phpMyAdmin dump)
```

### ไฟล์สำคัญ

| ไฟล์ | หน้าที่ |
| --- | --- |
| `config.php` | `Config`: ค่าตั้งฐานข้อมูล, DSN, การเชื่อมต่อ PDO ที่ใช้ร่วมกัน (`PDO::FETCH_ASSOC`) |
| `db.php` | `Database extends Config`: `insert`, `read`, `readOne`, `update`, `delete` |
| `util.php` | `Util`: ตัวทำความสะอาด `testInput()` และ alert Bootstrap จาก `showMessage()` |
| `action.php` | Controller: แยกคำขอ add/read/edit/update/delete |
| `index.php` | หน้าเว็บเดียว: ตารางผู้ใช้, modal เพิ่ม/แก้ไข, กล่อง alert |
| `main.js` | โหลดตารางและผูก add/edit/delete ผ่าน `fetch` |
| `database/php_crud_pdo_oop.sql` | schema และข้อมูลตัวอย่างสองแถว (dump จาก phpMyAdmin) |

### ลำดับการทำงานของคำขอ

| คำขอ | Method | ผลลัพธ์ |
| --- | --- | --- |
| `add` (ฟิลด์ในฟอร์ม) | POST | ทำความสะอาดข้อมูล เพิ่มผู้ใช้ แล้วคืน alert สำเร็จ |
| `read` | GET query (frontend ส่ง POST พร้อม query เดียวกัน) | แถว HTML ของตาราง หรือแถวว่าง |
| `edit` + `id` | GET | ผู้ใช้หนึ่งรายเป็น JSON เพื่อเติมใน modal แก้ไข |
| `update` (ฟิลด์ในฟอร์ม) | POST | ทำความสะอาดข้อมูล อัปเดตผู้ใช้ แล้วคืน alert |
| `delete` + `id` | GET | ลบผู้ใช้ แล้วคืน alert |

## การตั้งค่าฐานข้อมูล

ไฟล์ dump มีตาราง `users` และข้อมูลตัวอย่างสองแถว แต่ **ไม่มี** `CREATE DATABASE` หรือ `USE` จึงต้องสร้างฐานข้อมูลว่างก่อนแล้วนำเข้า:

```bash
mysql -u root -p -e "CREATE DATABASE php_crud_pdo_oop CHARACTER SET utf8 COLLATE utf8_general_ci"
mysql -u root -p php_crud_pdo_oop < database/php_crud_pdo_oop.sql
```

หรือจะสร้างฐานข้อมูลชื่อ `php_crud_pdo_oop` ใน phpMyAdmin แล้วใช้แท็บ **Import** ก็ได้

ตาราง `users`:

| คอลัมน์ | ชนิด | คุณสมบัติ |
| --- | --- | --- |
| `id` | `int(10)` | Primary key, auto-increment |
| `first_name` | `varchar(255)` | Not null |
| `last_name` | `varchar(255)` | Not null |
| `email` | `varchar(255)` | Not null |
| `phone` | `varchar(10)` | Not null |
| `timestamp` | `timestamp` | Nullable; ค่าเริ่มต้น `CURRENT_TIMESTAMP`, อัปเดตเมื่อแถวเปลี่ยน |

- Engine: `InnoDB`, charset เริ่มต้น `utf8`, collation `utf8_general_ci`
- auto-increment เริ่มที่ `3` หลังนำเข้าผู้ใช้ตัวอย่างสองราย (`UserOne`, `UserTwo`)
- ตรวจสอบว่าข้อมูลรับรองใน `config.php` ตรงกับสภาพแวดล้อมของคุณ

## เริ่มต้นใช้งาน

สิ่งที่ต้องมี:

- **PHP ที่เปิดส่วนขยาย `pdo_mysql`** — dump นี้สร้างด้วย PHP 8.2.12 แนะนำให้ใช้รุ่นที่แพตช์แล้ว (8.2.35 / 8.3.36 / 8.4.27 / 8.5.12 หรือใหม่กว่า)
- **MySQL หรือ MariaDB** — dump นี้สร้างด้วย MariaDB 10.4.32
- **เว็บเซิร์ฟเวอร์ที่รัน PHP ได้** — built-in server ของ PHP, Apache หรือ Nginx

ติดตั้ง:

1. clone โปรเจกต์ไปไว้ใน web root หรือโฟลเดอร์โลคอลใดก็ได้:
   ```bash
   git clone https://github.com/NetParamad/learn-php-crud-pdo.git
   cd learn-php-crud-pdo
   ```
2. สร้างฐานข้อมูลและนำเข้า schema (ดู [การตั้งค่าฐานข้อมูล](#การตั้งค่าฐานข้อมูล))
3. แก้ค่าใน `config.php` (`DBHOST`, `DBUSER`, `DBPASS`, `DBNAME`)
4. เปิด built-in server ของ PHP จากโฟลเดอร์โปรเจกต์:
   ```bash
   php -S localhost:8000
   ```

จากนั้นเปิด `http://localhost:8000/index.php` ในเบราว์เซอร์

## ตัวแปรสภาพแวดล้อม

โปรเจกต์นี้อ่านตัวแปรสภาพแวดล้อมใดๆ ไม่เลย และไม่มี `.env.example` ค่าการเชื่อมต่อถูกฝังเป็น private constant ใน `config.php`:

| Constant | จำเป็น | หมายเหตุ |
| --- | --- | --- |
| `DBHOST` | ✅ | โฮสต์ฐานข้อมูล ค่าเริ่มต้น `localhost` |
| `DBUSER` | ✅ | ผู้ใช้ฐานข้อมูล ค่าเริ่มต้น `root` |
| `DBPASS` | ✅ | รหัสผ่านฐานข้อมูล ค่าเริ่มต้นว่าง |
| `DBNAME` | ✅ | ชื่อฐานข้อมูล ค่าเริ่มต้น `php_crud_pdo_oop` |

ค่าเหล่านี้เป็น PHP constant ไม่ใช่ environment variable ควรย้ายไปเป็น environment variable หรือไฟล์ config โลคอลที่อยู่ใน `.gitignore` ก่อนใช้งานจริง

## วิธีใช้งาน

1. เมื่อโหลดหน้า ระบบจะแสดงรายชื่อผู้ใช้ทั้งหมดทันที
2. กด **Add New User** กรอกชื่อ นามสกุล อีเมล และเบอร์โทร แล้วกดส่ง ระบบจะแสดง alert สำเร็จและรีเฟรชตาราง
3. กด **Edit** ที่แถวใดแถวหนึ่งเพื่อเปิด modal ที่เติมข้อมูลล่วงหน้า แก้ไขฟิลด์แล้วกดส่งเพื่ออัปเดต
4. กด **Delete** ที่แถวใดแถวหนึ่งเพื่อลบผู้ใช้นั้นทันที

## หมายเหตุ

- ข้อมูลรับรองถูกฝังไว้ใน `config.php` ให้ถือว่าโปรเจกต์นี้เป็นงานเรียนรู้เท่านั้น และควรแยกค่าลับออกก่อนใช้งานจริง
- การทำความสะอาดข้อมูลทำในฝั่ง PHP (`Util::testInput`) แต่ไม่มีการตรวจชนิด/รูปแบบฝั่งเซิร์ฟเวอร์ ไม่มีการป้องกัน CSRF และไม่มีระบบยืนยันตัวตน ควรเพิ่มก่อนเปิดใช้งานจริง
- คอลัมน์ `phone` เป็น `varchar(10)` สอดคล้องกับรูปแบบเบอร์ 10 หลักในข้อมูลตัวอย่าง
- `timestamp` ถูกจัดการโดยฐานข้อมูล และแสดงเวลาล่าสุดที่แถวถูกเพิ่มหรือแก้ไข
- **สัญญาอนุญาต** — MIT (ดู [LICENSE](LICENSE))
