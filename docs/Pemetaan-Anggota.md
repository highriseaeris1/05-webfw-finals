# Pemetaan Anggota Tim dan Progres Pekerjaan

Dokumen ini memetakan kelima anggota tim ke tugas wajib masing-masing, lalu merangkum progres yang sudah dikerjakan. Status fitur dinilai terhadap **syarat minimum pada `Checklist Spesifikasi Proyek`**, bukan terhadap desain ideal. Bila dokumen referensi saling bertentangan, sumbernya disebutkan agar tidak menimbulkan klaim yang terlalu jauh.

Sumber yang dipakai:

1. **Prompt log** di `docs/prompt-logs/` (permintaan dan konteks yang ditulis anggota).
2. **Riwayat commit** di seluruh branch dan **kode aktual** di `dev` pada `a9266bd` (27 November 2026, setelah PR #7 yang membawa W4 dan W8 masuk `dev`).
3. **`Pembagian Tugas.pdf`** untuk penanggung jawab W1–W12.
4. **`Checklist Spesifikasi Proyek.pdf`** dan `Checklist Spesifikasi.xlsx` untuk syarat minimum.
5. **`Use Case dan Struktur Data.pdf`** untuk ERD, relasi, dan transisi status.
6. **`Alur dan Fitur Sistem.pdf`** untuk alur bisnis.
7. **`Laporan Progres Minggu 3 (Final).pdf`**, **`Diagram Use Case (Final).pdf`**, **`Progres 5.pdf`**, dan mockup untuk desain final.
8. **`README.md`** dan kode aplikasi untuk kondisi implementasi sebenarnya.

---

## 1. Ringkasan Identitas

| Anggota | Nama Resmi | Branch Kerja | OS | Peran Repositori | Fitur Wajib |
| :--- | :--- | :--- | :--- | :--- | :--- |
| Anggota 1 | **Atika** | `devs/atika` | Windows | Kontributor | W5, W7 |
| Anggota 2 | **Ratu** | `devs/ratu` | Windows | Kontributor | W1, W2, W3 |
| Anggota 3 | **Ibnu** | `devs/ibnu` | Windows | Kontributor | W9, W10 |
| Anggota 4 | **Dean** | `devs/dean` | Windows | Kontributor | W6, W11 |
| Anggota 5 | **Hideo Kojima** | `devs/hideokojima` | Linux | **Repository Maintainer** | W4, W8 |

W12 (repositori dan README) menjadi tanggung jawab bersama seluruh anggota.

---

## 2. Bukti Identifikasi

Pemetaan ini tidak hanya menebak dari nama branch; ada bukti eksplisit di prompt log dan commit.

| Anggota | Bukti Kunci |
| :--- | :--- |
| **Atika** | Prompt log `20261125_142935_atika_room-b451619d.md` dibuka dengan kalimat: *"Halo, saya Atika, Anggota 1 di tim ini, dan saya sedang di branch devs/atika."* Seluruh permintaannya membahas W5 (tabel `permintaan`) dan W7 (daftar permintaan dengan pencarian, filter, paginasi). |
| **Ratu** | Prompt log `20261126_144739_ratu_room-3f8a2c71.md` dan `20261126_160253_ratu_room-966a43be.md` berisi paket W1 (login, logout, ubah kata sandi), W2 (middleware peran, dashboard per peran), dan W3 (kelola pengguna). Paket itu persis milik Anggota 2 pada Pembagian Tugas. Commit author: `who-ratuu`. |
| **Ibnu** | Prompt log `20261127_142756_ibnu_097926d8.md` meminta *"Implementasi fitur laporan"*; W9 adalah Laporan dan pemiliknya Anggota 3. Commit author: `Noot002`. |
| **Dean** | Prompt log `20261125_140339_dean_room-38f08569.md` menyebut *"tugasku di sini sebagai anggota ke 4"* dan merencanakan W6 (status alur) + W11 (data contoh). Commit author: `Redwinter666`. |
| **Hideo Kojima** | Satu-satunya anggota resmi yang tersisa setelah empat di atas terpetakan, sehingga memegang Anggota 5 (W4 + W8). Diperkuat oleh perannya sebagai maintainer: merge PR #1, #3, #4, #5, #6, #7 dan penyelesaian konflik rute. Commit author: `highriseaeris1` / `Hideo Kojima`. |

Alias author commit yang perlu diketahui saat menelusuri riwayat:

| Author Git | Anggota |
| :--- | :--- |
| `atikasarii` | Atika |
| `who-ratuu` | Ratu |
| `Noot002` | Ibnu |
| `Redwinter666` | Dean |
| `highriseaeris1` / `Hideo Kojima` | Hideo Kojima |

---

## 3. Progres per Anggota

### Anggota 1 — Atika (`devs/atika`)

**Tanggung jawab:** W5 satu transaksi berelasi, W7 pencarian/filter/paginasi.

| Fitur | Progres | Bukti |
| :--- | :--- | :--- |
| W5 | **Sebagian (fondasi)** — tabel `permintaan` (header transaksi), model, factory, dan generator kode selesai. Model `Permintaan` baru punya relasi `belongsTo` ke `User` lewat `diproses_oleh`; belum ada `detail_permintaan` dan belum ada relasi ke `barang`/`kategori`. | `bfa48f1` (migration `permintaan`), `c0d4646` (generator `LF-YYYYMMDD-0001` / `REQ-0001`) |
| W7 | **Selesai** — halaman `/admin/permintaan` dengan pencarian `kode_resi`/`nama_pemohon`, filter status dan rentang `tanggal_pengajuan`, paginasi 10 + `withQueryString()`, dan feature test. | `e1f75e5`, `0789105` |

**Catatan penting:**
- `Checklist Spesifikasi W5` meminta tabel transaksi yang **berelasi ke data master** dan **detail transaksi one-to-many bila ada**. Kondisi sekarang baru header transaksi + relasi ke `users`; belum memenuhi syarat minimum tersebut.
- `detail_permintaan` dan pencarian nama barang belum dibuat karena tabel `barang` (milik Anggota 5) belum ada.
- Route `/admin/permintaan` masih berada **di luar** grup `auth + role:admin` dan hanya diberi komentar TODO. Ini celah keamanan W2, bukan kekurangan W7.
- Pekerjaan sudah masuk `dev` melalui PR #1 (`3f2642e`).

### Anggota 2 — Ratu (`devs/ratu`)

**Tanggung jawab:** W1 login/logout, W2 dua peran pengguna (Admin + Pengawas), W3 kelola pengguna.

| Fitur | Progres | Bukti |
| :--- | :--- | :--- |
| W1 | **Selesai** — login via email/username, logout, ubah kata sandi, session regeneration, akun nonaktif ditolak, feature test lengkap. | `3062478`, `3ace82b`, `ec207a2`, `9a6ebeb`, `28cc110`, `e12327a` |
| W2 | **Sebagian (2 peran; tinggal proteksi route)** — middleware `role`, halaman 403, serta dashboard dan menu terpisah Admin/Pengawas sudah berjalan. Keputusan dosen menetapkan **dua peran berakun** (Admin dan Pengawas); Pengunjung adalah aktor publik tanpa akun/menu. Sisa satu celah: route `/admin/permintaan` masih di luar middleware. | `4cfc44b`, `42c2e66`, `be0df1b` |
| W3 | **Selesai** — daftar + pencarian pengguna, tambah pengguna, edit pengguna, aktif/nonaktifkan, aturan minimal satu Admin aktif, dan pengujian akses. | `fd5997e`, `4e8482d`, `b6858a4`, `67c7ddc`, `e12327a` |

**Catatan penting:**
- **Keputusan dosen:** W2 cukup **dua peran berakun** (Admin dan Pengawas); Pengunjung tetap aktor publik tanpa akun. Dengan keputusan ini, ketentuan "3 peran" pada checklist tidak lagi menjadi blocker W2. Sisa satu celah: route `/admin/permintaan` masih di luar middleware `auth + role:admin`.
- `Checklist W11` meminta **3 akun**. Dengan keputusan dua peran, tiga akun ditafsirkan sebagai **2 Admin + 1 Pengawas**; `UserSeeder` sudah menyediakannya (satu akun nonaktif). Tidak perlu membuat peran ketiga.
- Dashboard dan menu per peran dibangun pada paket W2-B (`42c2e66`). Angka ringkasan W8 sudah ditambahkan Hideo dan masuk `dev` melalui PR #7.
- Middleware `EnsureUserIsActive` belum ada; akun yang dinonaktifkan saat sesi masih berjalan tetap bisa memakai sesi sampai logout.
- Pekerjaan masuk `dev` melalui integrasi `960031e` dan PR #3 (`608a65b`).

### Anggota 3 — Ibnu (`devs/ibnu`)

**Tanggung jawab:** W9 laporan, W10 unggah berkas.

| Fitur | Progres | Bukti |
| :--- | :--- | :--- |
| W9 | **Selesai** — halaman laporan Pengawas per periode, ringkasan status, tabel rincian, ekspor PDF (DomPDF) dan Excel (Maatwebsite), feature test. | `9f32662`, `0eb2fe0`, `3a06fa6` |
| W10 | **Belum** — belum ada controller, Form Request, penyimpanan disk, atau validasi unggah. Kolom `foto_identitas` dan `foto_selfie` sudah ada di tabel `permintaan`, tetapi belum ada alur unggahnya. | Tidak ada commit W10 |

**Catatan penting:**
- Laporan baru merekap tabel `permintaan`; belum melibatkan `detail_permintaan`, `barang`, atau `riwayat_pengambilan` karena tabel-tabel itu belum ada. Ini tetap memenuhi syarat minimum W9 (rekap per periode + PDF/Excel).
- `Checklist W10` hanya meminta **minimal satu fitur unggah** dengan validasi jenis dan ukuran. W10 **tidak perlu menunggu W4** untuk unggah KTP/KTM dan selfie karena kolomnya sudah ada di `permintaan`; yang menunggu W4 adalah unggah foto barang di modul master.
- Ibnu juga menandai bahwa route `/admin/permintaan` hasil merge masih dapat dibuka tanpa login.
- Pekerjaan masuk `dev` melalui PR #4 (`29b467a`).

### Anggota 4 — Dean (`devs/dean`)

**Tanggung jawab:** W6 status alur, W11 data contoh.

| Fitur | Progres | Bukti |
| :--- | :--- | :--- |
| W6 | **Selesai (minimum)** — enum `StatusPermintaan`, service transisi `permintaan` dengan `DB::transaction()` + `lockForUpdate()`, Form Request, controller, route aksi di grup `auth + role:admin`, badge/tombol aksi, dan pengujian. | `cbc8765` |
| W11 | **Sebagian** — `PermintaanSeeder` berisi 30 transaksi (8 diajukan, 5 disetujui, 12 diambil, 5 ditolak) dan dipanggil dari `DatabaseSeeder`, plus `DataContohSeederTest`. `UserSeeder` sudah ada dan membuat 3 akun (2 Admin + 1 Pengawas, satu di antaranya nonaktif), sesuai keputusan dua peran. | `217ded8` |

**Catatan penting:**
- `Checklist W6` hanya meminta transaksi dengan status bertahap dan perubahan status oleh peran berhak. Alur `permintaan` (`diajukan → disetujui/ditolak → diambil`) sudah memenuhi itu. Transisi status `barang` dan pencatatan `riwayat_pengambilan` yang ada di desain belum tersambung karena tabelnya belum ada; itu kekurangan alur bisnis penuh, bukan kegagalan syarat minimum W6.
- `Checklist W11` meminta **3 akun** dan **30 data transaksi**. Jumlah 30 transaksi dan 3 akun sudah terpenuhi; dengan keputusan dua peran, komposisi 2 Admin + 1 Pengawas adalah tafsiran yang disepakati. Seeder kategori dan barang belum ditambahkan meskipun W4 sudah masuk `dev`.
- Pekerjaan masuk `dev` melalui PR #5 (`4d2dfe8`).

### Anggota 5 — Hideo Kojima (`devs/hideokojima`)

**Tanggung jawab:** W4 dua data master, W8 dashboard per peran; sekaligus Repository Maintainer.

| Area | Progres | Bukti |
| :--- | :--- | :--- |
| W4 | **Selesai** — migration, model, factory, 4 Form Request validasi Bahasa Indonesia, controller CRUD dengan pencarian/filter/paginasi/`lockForUpdate()`, route `admin.kategori.*` & `admin.barang.*`, menu sidebar aktif, 6 view Blade, serta feature test `KategoriManagementTest` dan `BarangManagementTest`. Sudah masuk `dev` melalui PR #7. | Migration `2026_11_27_000001..000002`, `KategoriController`, `BarangController`, `KategoriManagementTest`, `BarangManagementTest` |
| W8 | **Selesai** — `DashboardSummaryService` + `DashboardController` menghitung angka nyata dari `barang`, `kategori`, `permintaan`, dan `users` untuk dashboard Admin (4 metrik utama, transaksi hari ini/per status, master & akun) maupun Pengawas (4 metrik utama, ringkasan permintaan, kandidat pemusnahan > 3 tahun, riwayat diambil terbaru). Sudah masuk `dev` melalui PR #7. | `DashboardSummaryService`, `DashboardController`, `dashboard/admin.blade.php`, `dashboard/pengawas.blade.php`, `DashboardSummaryTest` |
| Maintainer | **Berjalan** — merge PR #1 (`3f2642e`), integrasi Ratu + penyelesaian konflik rute (`960031e`), merge PR #3 (`608a65b`), PR #4 (`29b467a`), PR #5 (`4d2dfe8`), PR #6 (`1efca68`), PR #7 (`a9266bd`), serta bantuan konflik rute (`05b1739`). | Riwayat merge di `dev` |

**Catatan penting:**
- `Checklist W8` (ringkasan angka sesuai peran) dan `Checklist W4` (dua data master dengan CRUD + Form Request) sudah terpenuhi dan masuk `dev` melalui PR #7; terverifikasi lewat 129 test hijau (654 assertions) pada scope non-`LaporanTest`, Pint, serta `npm run build`. Setelah regresi `LaporanTest` diperbaiki, seluruh suite mencatat 141 test hijau (696 assertions).
- Kolom `barang.foto` sementara opsional berupa path teks sampai modul unggah berkas W10 (Ibnu) menggantinya dengan unggahan file ke disk `public`.
- Ringkasan `dimusnahkanTahunIni` dan `riwayatDiambilTerbaru` di `DashboardSummaryService` masih memakai `barang.created_at` dan `permintaan` berstatus `diambil` sampai tabel `riwayat_pengambilan` dan alur pemusnahan W6 penuh (Dean) dibuat (`TODO` sudah dicatat di kode).
- W4 sudah masuk `dev`; ini membuka jalan bagi W5 `detail_permintaan` (Atika), alur status `barang` & `riwayat_pengambilan` W6 (Dean), dan seeder data contoh W11.
- W10 untuk KTP/KTM dan selfie tetap bisa dikerjakan paralel oleh Ibnu di atas tabel `permintaan` yang sudah ada.

---

## 4. Status Fitur Wajib (W1–W12)

Status di bawah mengacu pada syarat minimum `Checklist Spesifikasi Proyek`, bukan pada kelengkapan desain final.

| Kode | Fitur | Penanggung Jawab | Status | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| W1 | Login dan logout | Anggota 2 (Ratu) | **Selesai** | Email/username + password hash, logout, ubah kata sandi, pengujian |
| W2 | Dua peran pengguna (Admin + Pengawas) | Anggota 2 (Ratu) | **Sebagian (2 peran; tinggal proteksi route)** | Keputusan dosen: 2 peran berakun + Pengunjung tanpa akun; route `/admin/permintaan` masih di luar middleware |
| W3 | Kelola pengguna | Anggota 2 (Ratu) | **Selesai** | Tambah, edit, nonaktifkan, tentukan peran |
| W4 | Dua data master | Anggota 5 (Hideo) | **Selesai** | CRUD `kategori` & `barang`, migration, model, Form Request, test; sudah masuk `dev` melalui PR #7 |
| W5 | Satu transaksi berelasi | Anggota 1 (Atika) | **Sebagian (fondasi)** | Header `permintaan` ada; relasi ke data master dan `detail_permintaan` belum |
| W6 | Status alur | Anggota 4 (Dean) | **Selesai (minimum)** | Alur `permintaan` bertahap + route `role:admin`; alur `barang`/riwayat belum |
| W7 | Pencarian, filter, paginasi | Anggota 1 (Atika) | **Selesai** | Pencarian, filter status/tanggal, paginasi; route masih perlu auth/role |
| W8 | Dashboard per peran | Anggota 5 (Hideo) | **Selesai** | Angka nyata transaksi, master, dan akun pada dashboard Admin & Pengawas; sudah masuk `dev` melalui PR #7 |
| W9 | Laporan | Anggota 3 (Ibnu) | **Selesai** | Rekap periode + PDF + Excel; lingkup data masih `permintaan`; tampilan/ekspor sudah menyesuaikan cast enum status |
| W10 | Unggah berkas | Anggota 3 (Ibnu) | **Belum** | Belum ada alur unggah/validasi; kolom path sudah ada |
| W11 | Data contoh | Anggota 4 (Dean) | **Sebagian** | 30 transaksi ada; 3 akun tersedia (2 Admin + 1 Pengawas) sesuai keputusan 2 peran; seeder kategori/barang belum |
| W12 | Repositori dan README | Seluruh anggota | **Sebagian** | Git history bertahap; README masih default Laravel, belum memuat instalasi + akun uji |

---

## 5. Dependency dan Risiko yang Perlu Diputuskan

1. **W4 sudah masuk `dev` melalui PR #7 (`a9266bd`).** Ini membuka jalan bagi Atika melengkapi W5 `detail_permintaan`, Dean menyambungkan status `barang` + `riwayat_pengambilan` (W6) dan seeder W11, serta Ibnu menambahkan unggah foto barang (W10).
2. **W10 tidak diblokir W4 untuk unggah KTP/KTM dan selfie.** Kolom `foto_identitas`/`foto_selfie` sudah ada di `permintaan`; yang membutuhkan W4 adalah unggah foto barang di modul master.
3. **Celah keamanan route `/admin/permintaan`.** Route W7 masih di luar middleware `auth` dan `role:admin`; siapa pun yang mengetik URL bisa membuka halaman. Perlu dipindahkan ke grup admin dan `PermintaanIndexTest` disesuaikan memakai `actingAs` Admin.
4. **Keputusan jumlah peran sudah diambil.** Dosen mengizinkan W2 dengan **2 peran berakun** (Admin + Pengawas); Pengunjung tanpa akun. W11 memakai 3 akun (2 Admin + 1 Pengawas); tidak perlu peran ketiga.
5. **W8 sudah selesai dan masuk `dev` melalui PR #7.** Angka ringkasan nyata Admin dan Pengawas telah diimplementasikan melalui `DashboardSummaryService` dan terverifikasi `DashboardSummaryTest`.
6. **W11 belum lengkap.** 30 transaksi dan 3 akun sudah ada (2 Admin + 1 Pengawas, sesuai keputusan dua peran); seeder kategori/barang belum ada meskipun W4 sudah masuk `dev`.
7. **Sesi akun nonaktif.** Login baru sudah ditolak, tetapi sesi yang sedang berjalan belum diputus paksa karena `EnsureUserIsActive` belum dibuat.
8. **W12 belum memenuhi.** `README.md` masih bawaan Laravel; belum ada cara instalasi proyek dan daftar akun uji seperti yang diminta checklist.
9. **Regresi `LaporanTest` sudah diperbaiki.** View laporan memakai `$item->status->label()`/`badgeVariant()` dari enum, service ringkasan memakai query builder agar status tetap string, dan export Excel memakai `status->label()`. Seluruh suite kini hijau (141 test, 696 assertions).

---

## 6. Ringkasan Alur Integrasi

| Tanggal | Peristiwa |
| :--- | :--- |
| 25 Nov 2026 | Fondasi repo, referensi, AGENTS, Workflow, mockup, dan prompt log awal |
| 26 Nov 2026 | Atika mengerjakan W5 (header `permintaan`) dan menyelesaikan W7; Ratu mengerjakan W1, W2 (sebagian), dan W3 |
| 27 Nov 2026 | Dean menyelesaikan W6 (minimum) dan W11 (sebagian); Ibnu menyelesaikan W9 |
| 27 Nov 2026 | PR #1 (Atika) di-merge; Ratu diintegrasikan lewat `960031e` dan PR #3; PR #4 (Ibnu) dan PR #5 (Dean) di-merge |
| 27 Nov 2026 | `dev` berada di `4d2dfe8`; `devs/hideokojima` disinkronkan ke titik yang sama |
| 27 Nov 2026 | Hideo menyelesaikan W4 (`kategori` & `barang`) dan W8 (dashboard angka nyata Admin & Pengawas) di `devs/hideokojima` |
| 27 Nov 2026 | PR #7 (Hideo) di-merge: W4 dan W8 masuk `dev` (`a9266bd`); keputusan dosen: W2 cukup 2 peran (Admin + Pengawas) |
| 27 Nov 2026 | Ibnu memperbaiki regresi `LaporanTest` akibat cast enum `StatusPermintaan`; view dan ekspor laporan kembali hijau |

---

## 7. Sumber dan Tafsiran yang Perlu Diketahui

| Dokumen | Dipakai untuk | Catatan |
| :--- | :--- | :--- |
| `Pembagian Tugas.pdf` | Penanggung jawab W1–W12 | Menetapkan Anggota 1–5 |
| `Checklist Spesifikasi Proyek.pdf` / `.xlsx` | Syarat minimum W1–W12 | Acuan status pada dokumen ini |
| `Use Case dan Struktur Data.pdf` | ERD, relasi, transisi status | Versi lama: hanya Admin yang berakun; Pengawas ditiadakan |
| `Alur dan Fitur Sistem.pdf` | Alur bisnis | Versi lama: hanya Admin yang berakun; Pengawas ditiadakan |
| `Laporan Progres Minggu 3 (Final).pdf` + `Diagram Use Case (Final).pdf` | Desain final peran dan use case | 3 aktor: Pengunjung, Admin, Pengawas; 20 use case |
| `Progres 5.pdf` + mockup | Desain UI final | Dashboard Admin/Pengawas, menu per peran, form dan tabel |
| `README.md` + kode aktual | Kondisi implementasi | README masih default Laravel; `dev` sudah memuat W4/W8, sedangkan W10 dan W5 detail masih belum |

**Keputusan yang sudah diambil:**

- **Jumlah peran:** dosen mengizinkan W2 dengan **dua peran berakun** (Admin + Pengawas); Pengunjung tanpa akun. W11 tetap 3 akun dengan komposisi 2 Admin + 1 Pengawas; tidak perlu peran ketiga. Checklist W2 yang menyebut 3 peran disesuaikan dengan keputusan ini.

**Kontradiksi yang masih terbuka:**

1. **Dashboard:** `Progres 5` dan mockup menampilkan dashboard dengan angka; angka nyata sudah dihitung (`DashboardSummaryService`) dan masuk `dev`, sedangkan aksi persetujuan pemusnahan dan `riwayat_pengambilan` per barang masih menunggu W6 penuh.
2. **README:** checklist W12 mewajibkan README berisi instalasi dan akun uji; README repo masih bawaan Laravel.
3. **Struktur tabel:** ERD lama memakai tabel `admin` dan kolom `permintaan.nama`; implementasi memakai `users` dengan kolom `role` dan `permintaan.nama_pemohon`. Implementasi mengikuti struktur yang lebih baru, bukan ERD lama.

Dokumen ini bersifat pemetaan teknis, bukan penilaian individu. Fokusnya adalah memastikan setiap fitur punya pemilik yang jelas, statusnya tidak dilebih-lebihkan, dan dependency antaranggota terlihat sebelum pekerjaan berikutnya dimulai.
