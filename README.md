# TendaJatim

Aplikasi CodeIgniter 4 untuk mengelola pemesanan tenda, pembukuan kas, data pegawai, dan absensi.

## Fitur

- Dua jenis akun dan halaman masuk: Admin dan Super Admin.
- Super Admin mengelola akun Admin; Admin mengelola kegiatan operasional.
- Pencatatan pesanan, status acara, nilai pesanan, dan uang muka.
- Uang muka pesanan otomatis masuk ke buku kas; pemasukan dan pengeluaran lain dicatat manual.
- Data pegawai dan absensi harian dengan status hadir, terlambat, izin, sakit, dan alpa.
- Ringkasan absensi hari ini di dashboard Admin, termasuk pegawai yang belum dicatat, dan akses cepat untuk mencatat kehadiran.
- Login Staff terpisah untuk mencatat jam masuk/pulang dan melihat riwayat absensi miliknya sendiri.
- Rekap kehadiran per bulan untuk Admin; Staff tidak dapat membuka data pegawai atau rekap orang lain.
- Formulir dilindungi CSRF, kata sandi disimpan dengan `password_hash`, dan akses dibatasi di sisi server.

## Menjalankan cepat di Laragon

Proyek menggunakan SQLite lokal secara bawaan, jadi aplikasi dapat langsung dicoba tanpa membuat database MySQL:

```sh
php spark migrate
php spark serve --host localhost --port 8080
```

Buka `http://localhost:8080/setup` untuk membuat akun Super Admin dan Admin. File database tersimpan sebagai `writable/tendajatim.sqlite`.

Admin dapat membuat akun login Staff saat menambah atau mengubah data pegawai. Buka `/login/staff` untuk login Staff.
Kolom nama pengguna dan kata sandi Staff dapat dibiarkan kosong bila akun belum diperlukan. Jabatan pegawai dipilih sebagai Staff atau Admin; jabatan Admin sendiri tidak memberikan hak akses ke dashboard Admin. Saat pegawai dinonaktifkan, akun Staff ikut dinonaktifkan dan dapat diaktifkan kembali bersama data pegawai.

## Menggunakan MySQL di Laragon

Persyaratan: PHP 8.2+, Composer, ekstensi `intl`, `mbstring`, `mysqli`, dan MySQL.

1. Buat database MySQL bernama `tendajatim` (atau pilih nama lain).
2. Ubah pengaturan database di `.env`:

   ```ini
   CI_ENVIRONMENT = development
   app.baseURL = 'http://tendajatim.test/'
   database.default.hostname = 127.0.0.1
   database.default.database = tendajatim
   database.default.username = root
   database.default.password =
   database.default.DBDriver = MySQLi
   database.default.port = 3306
   ```

   Sesuaikan `app.baseURL` dengan alamat virtual host Laragon. Jangan unggah `.env` ke repositori.
3. Dari direktori proyek, pasang dependensi jika belum tersedia, lalu jalankan migrasi:

   ```sh
   composer install
   php spark migrate
   ```

4. Arahkan document root web server ke direktori `public/`.
5. Buka `/setup` sekali untuk membuat nama pengguna dan kata sandi masing-masing Super Admin serta Admin. Kata sandi awal minimal 12 karakter; tidak ada akun atau kata sandi bawaan.
6. Masuk melalui `/login/super-admin` atau `/login/admin`.

Untuk pengembangan tanpa virtual host, jalankan `php spark serve` dan buka `http://localhost:8080`.

## Deployment ke Render melalui GitHub

Repository menyertakan `render.yaml` dan `Dockerfile` untuk deployment otomatis dari GitHub. Render menjalankan PHP/Apache dan PostgreSQL; database SQLite lokal tidak digunakan untuk deployment.

1. Buat repository GitHub dan push kode aplikasi. Jangan unggah `.env`, database lokal, atau rahasia.
2. Di Render pilih **New > Blueprint**, hubungkan repository GitHub, lalu terapkan `render.yaml`.
3. Blueprint membuat web service Free dan PostgreSQL `basic-256mb` persisten. **PostgreSQL tersebut berbayar**; periksa biaya Render saat membuat service. Jangan lanjutkan jika belum menyetujui biaya.
4. Setelah deployment selesai, buka alamat `onrender.com` dan buat akun awal di `/setup`. Buat kata sandi unik dan kuat untuk Super Admin serta Admin.
5. Opsional: isi `APP_BASE_URL` di Environment service jika menggunakan domain khusus. Tanpa itu, aplikasi memakai `RENDER_EXTERNAL_URL`.

Setiap push ke branch yang terhubung akan memicu build/deploy otomatis. Render menjalankan migrasi database saat container mulai. Backup/export data sebelum menghapus database atau mengganti environment produksi.

Deployment memerlukan ekstensi PHP `intl` dan `pgsql`, yang sudah disiapkan pada Docker image.

## Catatan pembukuan dan absensi

- Nilai uang muka yang dimasukkan ketika membuat pesanan dicatat sebagai satu pemasukan otomatis. Perubahan status atau nilai total pesanan tidak membuat catatan kas baru.
- Pembayaran lanjutan, pemasukan, dan pengeluaran dicatat melalui Buku Kas. Transaksi yang terkait dengan pesanan tidak dapat dihapus dari halaman Buku Kas.
- Memperbarui absensi pada pegawai dan tanggal yang sama akan mengganti catatan hari itu, bukan membuat duplikat.
- Menonaktifkan pegawai mempertahankan seluruh riwayat absensinya.

## Pemeriksaan

Jalankan pengujian bawaan dengan `vendor/bin/phpunit`. Migrasi aplikasi dapat dibatalkan menggunakan `php spark migrate:rollback`; perintah rollback menghapus tabel aplikasi, sehingga lakukan hanya pada database yang memang akan dibersihkan.
