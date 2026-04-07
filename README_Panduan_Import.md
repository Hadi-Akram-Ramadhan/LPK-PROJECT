# 📖 PANDUAN MUDAH PERSIAPAN DATA LPK CBT (Khusus Pemula / Admin Dasar)

Selamat datang! Panduan ini dibuat khusus agar Anda **tidak kebingungan** saat harus memasukkan data murid yang sangat banyak, atau soal ujian berjumlah ratusan, beserta gambar/audionya secara praktis tanpa harus menginputnya satu per satu.

Panduan ini **TIDAK** membahas cara *upload* ke aplikasinya, melainkan **fokus 100% pada cara Anda MENYIAPKAN File Excel dan File ZIP**-nya terlebih dahulu sebelum disetor ke sistem.

Silakan baca pelan-pelan ya! 🚀

---

## 🙋‍♂️ 1. CARA MENGISI EXCEL DATA MURID BARU

Tujuan dari file Excel ini: memasukkan puluhan atau ratusan siswa sekaligus ke sistem tanpa harus membuatkan mereka akun satu-satu.

### Langkah-langkah:
1. **Download (Unduh) Template Excel** dari sistem bagian menu Murid/User.
2. Buka file bernama `Template_Import_Siswa_LPK.xlsx` yang baru saja Anda download.
3. Anda akan melihat dua baris contoh berwarna abu-abu (Budi & Siti). **HAPUS BARIS TERSEBUT** sebelum Anda memasukkan data anak-anak Anda yang asli agar Budi dan Siti fiktif ini tidak ikut terdaftar.
4. Perhatikan nama-nama kolom utama di bagian atas (hijau):
   - **Kolom A (Nama Murid):** Tulis nama lengkap setiap anak.
   - **Kolom B (Alamat Email):** Tulis alamat email. Ingat, sistem akan menolak jika ada lebih dari 1 siswa dengan email yang *huruf per hurufnya sama persis*. Semuanya harus unik/beda-beda.
   - **Kolom C (Password/Kata Sandi):** Buatlah password yang gampang untuk murid. Syarat mutlak: **HARUS minimal 8 huruf/angka**. Boleh *qwerty123*, boleh *pelajarlpk*.
   - **Kolom D (Nomor ID Kelas):** Ini adalah pilihan. Kalau muridnya langsung mau dimasukkan ke kelas A, Anda tinggal mengetik *angka* ID kelas A. Dari mana tahu angkanya? Buka Sheet kedua di bagian bawah layar Excel bertuliskan **"CARA BACA & BANTUAN"**, di sana ada daftar angka kelas aslinya.
5. **PENTING:** Jangan mengubah teks judul di kepala tabel (baris nomor 1, yang berwarna hijau).
6. **Simpan (Save)** file Excel tersebut bila sudah beres semua baris siswanya.

---

## 📝 2. CARA MENGISI EXCEL DATA SOAL UJIAN

Tujuan dari Excel ini: agar guru tidak perlu ribet mengklik *Tambah Soal* berkali-kali di komputer. Anda bisa menyusun soal di Excel sambil santai ngopi, lalu sisanya otomatis.

**PENTING:** Satu file Excel hanya untuk **SATU Ujian Saja**. Jangan mencampur soal Ujian Seoul dan soal Ujian Busan dalam satu file Excel yang sama. Buatlah file Excel terpisah untuk masing-masing tipe ujian agar tidak tertukar.

### Langkah-langkah:
1. **Download (Unduh) Template Excel Soal** dari dashboard Guru atau Admin.
2. Sama seperti file murid, **HAPUS baris nomor 2 sampai 7 (yang berwarna abu-abu & biru biru)** karena itu cuma contoh dari kami agar Anda lebih tergambar.
3. Mari kita mengisi per kolom:
   - **Kolom A (Tipe Soal):** INI WAJIB DIISI! Cukup ketik nama tipenya persis seperti ejaan kami (contoh: Pilihan Ganda, Essay, Audio, Short Answer).
   - **Kolom B (Tuliskan Pertanyaan):** Tulis pertanyaan di sini (Contoh: *Apa bahasa Koreanya tas?*). Jika kalimat panjang hingga tersembunyi, biarkan saja, Excel bisa digeser lebar-lebar sesukanya.
   - **Kolom C & D (Gambar / Audio):** KOSONGKAN SAJA kalau soalnya cuma teks biasa. TAPI, jika soal butuh gambar pemandangan atau suara percakapan, ketik nama file yang akan diupload nantinya persis huruf besar kecilnya (contoh ketik: `foto_tas.jpg` atau `suara01.mp3`).
   - **Kolom E, G, I, K, M:** Ini adalah isi dari pilihan jawaban (A, B, C, D, E). Jika pilihannya hanya sampai D, maka M (Opsi E) biarkan kosong.
   - **Kolom F, H, J, L, N:** Jarang kepakai. Ini dipakai KALO pilihan Jawabannya (A,B,C,D) BUKAN teks, melainkan *gambar*. Contoh: "Pilih gambar stasiun yang benar...". Maka gambar stasiun dimasukkan ke sini format ejaan namanya. Jika tidak, KOSONGKAN.
   - **Kolom O (Kunci Jawaban Benar):** Tulis hurufnya saja (misal: A atau B). Ingat, kalau tipe soalnya "Essay", kosongkan saja karena Essay dinilai oleh Guru, bukan dinilai mesin.
   - **Kolom P (Nilai Point):** Kasih nilai kalau soal ini dikerjakan dengan benar dapat poin berapa (misal ketik saja angka 10).
4. **Perhatikan Sheet Kedua!** Jika di tengah jalan lupa aturan di atas, lihat Sheet Excel kedua yang berjudul **"CARA BACA PANDUAN"**. Lengkap tertulis di sana apa harus ngisi apa, terutama bocoran cara mengisi agar soal **Isian Singkat (Short Answer)** agar anak-anak tidak dirugikan walau mereka cuma salah ketik 1 huruf / typo.

---

## 📁 3. CARA MENYIAPKAN FILE ZIP UNTUK GAMBAR & AUDIO

Biasanya, orang bingung dengan "Loh kok di Excel disuruh nulis *foto_tas.jpg*? Cara ngirim fotonya nanti gimana, masa ditempel fotonya masuk-masuk ke dalam kotak tabel Excel-nya?"

**Jawabannya: BUKAN DITEMPEL DI DALAM KOTAK EXCEL.**

Gambar dan Suara Anda kumpulkan sendiri di *Folder* terpisah pada komputer Saudara. Sistem CBT kita bisa menyedot foto/audio dalam jumlah massal lewat sebuah file "Gudang Kompres" bernama **.ZIP**.

### Cara Menyiapkan ZIP Media:
1. Pastikan Anda punya foto soal (misal nama filenya: `soal2A.jpg`, `soal10.png`) dan Audio (misal: `suara_bab2.mp3`).
2. Masukkan SEMUA foto dan suara yang berhubungan dengan soal tersebut ke dalam **Satu Folder Kosong** di Laptop Anda. Beri nama Foldernya misal "Media_Ku".
   - *Catatan:* Perhatikan baik-baik nama foto dan suara ini! Namanya harus 1.000.000% sama dengan teks yang Anda ketik di **Kolom C** dan **Kolom D** Excel tadi. Kalau C diisi `SepanjangJalan.jpg`, maka nama fotonya di komputer harus `SepanjangJalan.jpg` (Besar kecil huruf ngefek, tidak boleh beda spasi sedikitpun).
3. Kalau sudah ngumpul semua fotonya di *Folder* tadi:
   - Jika Anda pakai **Windows**: Blokir / *Highlight* klik semua foto dan audio yang ada di dalam folder tersebut (Tekan CTRL + A). Lalu **Klik Kanan > Kirim Ke (Send To) > Compressed (zipped) folder**.
   - Sistem akan menyulap jadi bentuk tumpukan buku (File ZIP). Anda bebas namain file barunya misal "MediaKoreaBab1.zip".
4. Beres! File `.zip` inilah yang kelak akan Anda *upload* (unggah) dari dalam sistem pada halaman Media/Galeri. Sistem CBT kita yang pintar kelak akan melepaskan segel pembungkus (.zip) dan menyebarkan foto-fotonya menempel ke nomor soal yang tepat yang dibaca dari Excel! 

🎉 **Kesimpulan:** Jika pekerjaan telah selesai, pastikan Anda siap di depan Laptop memegang DUA TIPE BERKAS: 1 Buah berwujud Excel Soal, dan 1 Buah berwujud Gudang Kompres (File .ZIP). Jangan sampai berpisah atau hilang namanya!
