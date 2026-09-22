# Batasan Perubahan AI

Aturan ini wajib dipatuhi pada setiap permintaan perubahan di workspace ini.

## Scope pekerjaan

- Kerjakan hanya bagian yang secara spesifik diminta oleh pengguna.
- Jika pengguna meminta perubahan pada bagian A, ubah atau buat hanya bagian A tersebut.
- Jangan mengubah, menghapus, memindahkan, mengganti nama, memformat ulang, atau merapikan bagian lain yang tidak diminta.
- Jangan melakukan refactor, upgrade dependency, perubahan database, perubahan API, atau perubahan konfigurasi di luar scope tanpa izin eksplisit.
- Jangan mengganti implementasi atau hasil kerja yang sudah dibuat oleh anggota tim lain.
- Pertahankan perilaku, struktur, penamaan, gaya kode, dan konfigurasi yang sudah ada di luar area yang diminta.

## Sebelum mengubah file

- Identifikasi file, fungsi, komponen, atau blok yang benar-benar terkait dengan permintaan.
- Baca perubahan yang sudah ada dan perlakukan sebagai pekerjaan yang harus dipertahankan.
- Jika scope permintaan belum jelas, tanyakan klarifikasi sebelum mengedit.
- Jika solusi yang benar membutuhkan perubahan di luar scope, jelaskan file dan alasan perubahan tersebut, lalu minta persetujuan terlebih dahulu.

## Saat mengedit

- Buat perubahan sekecil mungkin dan hanya pada area yang diperlukan.
- Jangan menghapus perubahan pengguna atau anggota tim lain.
- Jangan melakukan perubahan massal atau formatting seluruh file jika tidak diminta.
- Jangan menyentuh file yang tidak berkaitan dengan permintaan.

## Setelah mengedit

- Validasi hanya area yang berubah dengan pemeriksaan atau test yang relevan.
- Laporkan file dan bagian yang diubah secara ringkas.
- Laporkan juga hal yang sengaja tidak diubah karena berada di luar scope.
- Jika menemukan masalah lain yang tidak berkaitan, jangan memperbaikinya otomatis; cukup laporkan.

## Aturan persetujuan

Jika ada keraguan apakah suatu perubahan termasuk scope, anggap perubahan tersebut di luar scope dan minta persetujuan pengguna. Tidak boleh memperluas pekerjaan berdasarkan asumsi.