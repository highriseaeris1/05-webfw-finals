# Panduan Alur Kerja Tim dan Penggunaan Git

Dokumen ini memuat panduan kerja sama tim pengembang (terdiri dari 5 anggota: **Dean**, **Ibnu**, **Atika**, **Ratu**, dan **Hideo Kojima**) agar proses pengembangan, integrasi kode, dan kolaborasi lintas sistem operasi berjalan tertib serta bebas dari konflik yang tidak perlu.

---

## 1. Pembagian Peran dan Struktur Branch

| Branch | Fungsi | Wewenang Perubahan / Push |
| :--- | :--- | :--- |
| `main` | *Branch* rilis dan demonstrasi akhir (harus selalu stabil) | **Hanya Hideo Kojima** |
| `dev` | *Branch* integrasi seluruh fitur yang telah selesai | **Hanya Hideo Kojima** (melalui *merge*/PR) |
| `devs/dean` | *Branch* pengembangan pribadi Dean (Windows) | **Dean** |
| `devs/ibnu` | *Branch* pengembangan pribadi Ibnu (Windows) | **Ibnu** |
| `devs/atika` | *Branch* pengembangan pribadi Atika (Windows) | **Atika** |
| `devs/ratu` | *Branch* pengembangan pribadi Ratu (Windows) | **Ratu** |
| `devs/hideokojima` | *Branch* pengembangan pribadi Hideo Kojima (Linux) | **Hideo Kojima** |
| `archive/*` | Arsip historis bersifat *read-only* | **Tidak ada** (dilarang diubah) |

### Pembagian Wewenang
- **Hideo Kojima (Repository Maintainer)**: bertindak sebagai pengelola repositori yang berwenang melakukan penggabungan (*merge*) ke `dev` dan `main`, menyelesaikan konflik lintas *branch*, menyinkronkan *branch*, serta memelihara struktur repositori.
- **Dean, Ibnu, Atika, dan Ratu (Kontributor)**: berfokus pada pengembangan fitur dan melakukan *commit* pada *branch* pribadi (`devs/<nama>`) masing-masing. Setelah satu bagian pekerjaan selesai dan diunggah (*push*) ke *branch* pribadi, buat Pull Request menuju `dev` di GitHub atau informasikan kepada Hideo Kojima untuk ditinjau dan digabungkan.

---

## 2. Pengaturan Awal (Khusus Anggota pada Windows)

Pastikan perangkat Windows telah terpasang **Git**, **PHP 8.3+**, **Composer 2.2+**, **Node.js 20.19+ atau 22.12+**, serta **MySQL/MariaDB** (dapat menggunakan Laravel Herd atau Laragon).

### A. Konfigurasi Akhiran Baris Git (Cukup Sekali)
Jalankan perintah berikut agar akhiran baris (*line endings*) tetap konsisten antara Windows dan Linux:
```powershell
git config --global core.autocrlf input
```

### B. Kloning Repositori dan Pindah ke Branch Pribadi
Ganti `<nama>` dengan nama *branch* masing-masing (`dean`, `ibnu`, `atika`, atau `ratu`):
```powershell
git clone [https://github.com/highriseaeris1/05-webfw-finals.git](https://github.com/highriseaeris1/05-webfw-finals.git)
cd 05-webfw-finals
git fetch --all --prune
git switch devs/<nama>
```

### C. Instalasi Dependensi Laravel
```powershell
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan storage:link
```
Buka berkas `.env`, sesuaikan konfigurasi `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` dengan layanan MySQL lokal, kemudian jalankan:
```powershell
php artisan migrate --seed
npm run build
```

---

## 3. Prosedur Kerja Harian

### Langkah 1: Menarik Pembaruan Terbaru dari `dev` Sebelum Mulai Bekerja
Sebelum menulis kode baru, pastikan *branch* pribadi sudah memuat pembaruan terakhir dari `dev`:
```powershell
git switch devs/<nama>
git fetch origin
git merge origin/dev
```
Apabila terdapat berkas *migration* atau paket baru dari anggota lain, jalankan:
```powershell
composer install
php artisan migrate
```

### Langkah 2: Pengembangan pada Branch Pribadi
- Pastikan selalu berada pada *branch* `devs/<nama>` dengan memeriksa `git branch --show-current`.
- Kerjakan berkas sesuai lingkup tugas masing-masing tanpa memodifikasi modul anggota lain tanpa koordinasi.
- Perhatikan **case-sensitivity**: sistem operasi Linux membedakan huruf besar dan kecil. Gunakan standar **PSR-4 PascalCase** secara tepat untuk seluruh berkas PHP (contoh: `NamaModel.php`, `NamaController.php`) serta huruf kecil (*lowercase/kebab-case*) untuk berkas Blade.
- Apabila memerlukan perubahan struktur basis data, **jangan mengubah berkas migration lama yang sudah masuk ke `dev`**. Buat berkas *migration* baru melalui `php artisan make:migration`.

### Langkah 3: Commit dan Push ke Branch Pribadi
Lakukan *commit* secara bertahap setiap menyelesaikan satu unit pekerjaan kecil. Gunakan format Conventional Commits tanpa tanda kurung scope dalam Bahasa Indonesia yang ringkas:
- `feat: ...` untuk penambahan fitur baru (contoh: `feat: tambah halaman dan proses CRUD data master`)
- `fix: ...` untuk perbaikan galat (contoh: `fix: perbaiki validasi unggah berkas`)
- `chore: ...` untuk pemeliharaan konfigurasi atau log (contoh: `chore: tambah log prompt sesi kerja`)
- `docs: ...` untuk pembaruan dokumentasi (contoh: `docs: perbarui panduan instalasi pada README`)

```powershell
git status
git add .
git commit -m "feat: deskripsi singkat perubahan"
git push origin devs/<nama>
```

### Langkah 4: Pengajuan Integrasi ke `dev`
Setelah proses `git push origin devs/<nama>` berhasil, buka Pull Request menuju `dev` di GitHub atau sampaikan pemberitahuan kepada **Hideo Kojima** agar perubahan dapat ditinjau dan digabungkan ke `dev`.

---

## 4. Penggunaan AI Coding Agent (`AGENTS.md`)

Repositori ini dilengkapi pedoman resmi pada `AGENTS.md` (dan `CLAUDE.md`) untuk mendukung pengembangan secara aman:
- Agent **tidak pernah menjalankan commit atau push secara otomatis**; setiap perubahan tetap harus diperiksa dan di-*commit* secara manual oleh anggota tim.
- Agent akan memeriksa *branch* aktif pada awal sesi dan mengingatkan apabila pengguna berada pada *branch* yang keliru.
- Anggota tim dapat menggunakan agent untuk bertanya mengenai konsep Laravel, alur Git, maupun pesan galat (agent dikonfigurasi untuk menjelaskan secara sederhana dengan pendekatan ELI12 tanpa memodifikasi berkas kode saat sesi tanya-jawab).
- Setiap sesi interaksi dengan agent dicatat secara penuh pada direktori `docs/prompt-logs/`. Pastikan berkas log terbaru pada direktori tersebut turut di-*commit* sebelum melakukan *push*.

### Struktur Direktori Dokumentasi (`docs/`)
- `docs/Workflow.md`: panduan alur kerja tim dan penggunaan Git harian.
- `docs/references/`: dokumen spesifikasi, struktur data, dan referensi utama proyek.
- `docs/examples/`: contoh pola implementasi, templat, atau acuan format bagi tim dan agent.
- `docs/prompt-logs/`: arsip pencatatan percakapan penuh (*verbatim*) bersama AI coding agent.

---

## 5. Larangan Operasional

1. **Dilarang melakukan commit atau push langsung ke `dev` maupun `main`** (khusus wewenang Repository Maintainer).
2. **Dilarang menggunakan `git push --force`** pada *branch* mana pun.
3. **Dilarang memasukkan berkas `.env`, direktori `vendor/`, `node_modules/`, maupun berkas sensitif di `storage/app/private/` ke dalam Git**.
4. **Dilarang memodifikasi berkas migration yang telah tergabung di `dev`**.
5. **Dilarang mengubah atau menghapus *branch* `archive/*`**.

---

## 6. Penanganan Kendala Git Umum

- **Telanjur mengubah berkas di `dev` sebelum melakukan commit**:
  Pindah langsung ke *branch* pribadi agar seluruh perubahan yang belum di-*commit* terbawa ke *branch* yang tepat:
  ```powershell
  git switch devs/<nama>
  ```
- **Terjadi merge conflict saat menjalankan `git merge origin/dev`**:
  Jangan menjalankan `git reset --hard` atau menghapus kode anggota lain secara sembarangan. Jalankan `git merge --abort` untuk membatalkan proses penggabungan secara aman, lalu hubungi **Hideo Kojima** untuk penyelesaian bersama.

---

## 7. Daftar Periksa Repository Maintainer (Hideo Kojima)

1. Meninjau Pull Request atau perubahan pada `devs/<nama>` (memastikan tidak ada berkas `.env`, fungsi *debugging* tertinggal, perubahan *migration* lama, atau ketidaksesuaian penamaan huruf besar/kecil untuk Linux).
2. Memastikan berkas pencatatan percakapan pada `docs/prompt-logs/` telah tersimpan sesuai format.
3. Menggabungkan perubahan yang telah lolos pemeriksaan ke `dev` (`git switch dev && git merge ... && git push origin dev`).
4. Menjaga stabilitas `main` hingga seluruh fitur siap didemonstrasikan, kemudian menggabungkan `dev` ke `main` setelah verifikasi akhir.