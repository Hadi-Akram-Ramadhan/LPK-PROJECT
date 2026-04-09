# 🏆 Panduan Admin - UBT Learning LPK URISOWON

Selamat datang di Sistem Ujian Berbasis Komputer (CBT) **LPK URISOWON**. Panduan ini akan membantu Anda mengelola seluruh ekosistem ujian, dari manajemen data siswa hingga pemantauan realtime ujian.

---

## 📊 1. Dashboard Utama
Setelah login sebagai Admin, Anda akan disuguhkan statistik ringkas:
- **Total Siswa & Staff**: Jumlah pengguna aktif dalam sistem.
- **Ujian Aktif**: Informasi ujian yang sedang berlangsung.
- **Nilai Terbaru**: Ringkasan hasil ujian siswa yang baru selesai.

---

## 👥 2. Manajemen Pengguna (Siswa & Guru)

### Menambah Siswa via Excel (Bulk Import)
Untuk menghemat waktu, Anda dapat mengimpor data siswa dalam jumlah banyak sekaligus:
1. Masuk ke menu **Manajemen User** > **Import Siswa**.
2. Siapkan file Excel (.xlsx) dengan 4 kolom utama:
   - **Kolom A**: Nama Lengkap
   - **Kolom B**: Alamat Email
   - **Kolom C**: Password (Min. 8 karakter)
   - **Kolom D**: ID Kelas (Pastikan ID Kelas sudah benar dari menu Kelas)
3. Upload file dan klik **Import**.

### Menambah Staff/Guru
1. Klik **User Baru** di dashboard manajemen user.
2. Pilih Role (**Guru** atau **Admin**).
3. Isi detail dan simpan.

---

## 🏫 3. Pengolahan Kelas & Jurusan
Sebelum mengimpor siswa, pastikan kelas dan jurusan sudah terdaftar.
- **ID Kelas**: Sangat penting untuk proses import siswa. ID ini bisa dilihat di tabel daftar kelas.
- **Jurusan**: Mengelompokkan kelas berdasarkan bidang studi (misal: Bahasa Korea, Hospitality, dll).

---

## 📚 4. Bank Soal (Questions)
Anda atau Guru dapat membuat bank soal yang akan digunakan dalam ujian.

### Format Import Soal via Excel
Jika soal sangat banyak, gunakan fitur import soal:
- **Kolom A**: Pertanyaan (Mendukung teks dasar).
- **Kolom B - F**: Opsi Jawaban (A sampai E).
- **Kolom G**: Jawaban Benar (Gunakan huruf kapital 'A', 'B', 'C', 'D', atau 'E').
- **Kolom H**: Poin Soal (Misal: 4).

> **💡 Tips**: Untuk soal dengan gambar atau audio, sebaiknya edit soal secara manual setelah diimport untuk menyisipkan media dari **Media Explorer**.

---

## 📝 5. Pengaturan Ujian (Exam Management)
Menu ini digunakan untuk merilis ujian kepada siswa.
- **Judul & Token**: Buat judul ujian dan sistem akan menghasilkan token akses (Gunakan tombol "Refresh Token" jika diperlukan).
- **Durasi**: Atur berapa menit siswa boleh mengerjakan.
- **Waktu Mulai & Berakhir**: Ujian tidak akan bisa diakses jika belum waktunya atau sudah lewat batas.
- **Acak Soal**: Centang fitur ini untuk mencegah kecurangan antar siswa.

---

## 🛡️ 6. Keamanan & Log Kecurangan
Sistem ini dilengkapi dengan **Anti-Cheat Engine**:
- **Log Kecurangan**: Jika siswa mencoba membuka tab lain atau keluar dari halaman ujian, sistem akan mengirimkan peringatan dan mencatatnya.
- **Otomatis Selesai**: Anda dapat mengatur agar ujian otomatis diselesaikan jika siswa melanggar lebih dari 3 kali.
- Lihat rekaman pelanggaran di menu **Log Kecurangan**.

---

## 📂 7. Media Explorer (Audio & Image)
Gunakan menu ini untuk mengelola aset media yang digunakan dalam soal:
- **Audio Explorer**: Upload file suara (.mp3, .wav) untuk soal Listening.
- **Image Explorer**: Upload gambar (.jpg, .png) untuk visualisasi soal.
- Masukkan URL file media tersebut ke dalam editor soal saat membuat pertanyaan.

---

## ✍️ 8. Monitoring & Penilaian (Monitoring)
Melihat hasil dan memantau ujian secara realtime.
- **Live Monitoring**: Lihat siapa saja siswa yang sedang mengerjakan, berapa soal yang sudah diisi, dan status koneksi mereka.
- **Koreksi Manual (Essay)**: Jika terdapat soal essay, masuk ke menu **Nilai & Hasil** > **Koreksi** untuk memberikan nilai manual setelah ujian selesai.

---

## 🔒 Tips Keamanan Admin
1. **Ganti Password**: Selalu ganti password Admin Anda secara berkala di menu Profil.
2. **Backup**: Lakukan backup data nilai secara berkala (Download ke Excel).
3. **Logout**: Selalu logout setelah selesai menggunakan dashboard untuk mencegah akses yang tidak sah.

---
*© 2026 Admin Panel - LPK URISOWON High Tech Learning*
