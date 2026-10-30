<x-filament-panels::page>
    <style>
        .guide-page {
            --guide-border: rgb(148 163 184 / .25);
            --guide-muted: rgb(100 116 139);
        }

        .guide-toolbar {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 1.25rem;
        }

        .guide-print-button {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            padding: .7rem 1rem;
            border: 0;
            border-radius: .65rem;
            background: rgb(123 53 152);
            color: white;
            font-weight: 650;
            cursor: pointer;
            transition: transform 150ms ease, filter 150ms ease;
        }

        .guide-print-button:hover {
            filter: brightness(1.08);
            transform: translateY(-1px);
        }

        .guide-intro {
            margin-bottom: 1.5rem;
            padding: clamp(1.25rem, 3vw, 2rem);
            border: 1px solid var(--guide-border);
            border-radius: 1rem;
            background: linear-gradient(135deg, rgb(123 53 152 / .10), rgb(16 138 68 / .06));
        }

        .guide-intro h2 {
            margin: 0 0 .45rem;
            font-size: clamp(1.25rem, 2vw, 1.65rem);
            font-weight: 750;
            letter-spacing: -.025em;
        }

        .guide-intro p {
            max-width: 70ch;
            margin: 0;
            color: var(--guide-muted);
            line-height: 1.7;
        }

        .guide-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .guide-card {
            padding: 1.3rem;
            border: 1px solid var(--guide-border);
            border-radius: .9rem;
            background: rgb(255 255 255 / .72);
            box-shadow: 0 5px 18px rgb(15 23 42 / .035);
            transition: border-color 150ms ease, box-shadow 150ms ease, transform 150ms ease;
        }

        .dark .guide-card {
            background: rgb(15 23 42 / .45);
        }

        .guide-card:hover {
            border-color: rgb(123 53 152 / .45);
            box-shadow: 0 10px 24px rgb(15 23 42 / .08);
            transform: translateY(-2px);
        }

        .guide-card-header {
            display: flex;
            align-items: center;
            gap: .85rem;
            margin-bottom: .85rem;
        }

        .guide-icon {
            display: grid;
            width: 2.8rem;
            height: 2.8rem;
            flex: 0 0 2.8rem;
            place-items: center;
            border-radius: .8rem;
            background: rgb(123 53 152 / .11);
            color: rgb(123 53 152);
        }

        .guide-icon svg { width: 1.4rem; height: 1.4rem; }

        .guide-card h3 {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 720;
            line-height: 1.35;
        }

        .guide-card ol,
        .guide-card ul {
            display: grid;
            gap: .58rem;
            margin: 0;
            padding-left: 1.25rem;
            color: var(--guide-muted);
            line-height: 1.6;
        }

        .guide-card li::marker { color: rgb(123 53 152); font-weight: 700; }

        .guide-callout {
            display: flex;
            gap: .9rem;
            align-items: flex-start;
            margin-top: 1.25rem;
            padding: 1.1rem 1.25rem;
            border: 1px solid rgb(16 138 68 / .25);
            border-left: 4px solid rgb(16 138 68);
            border-radius: .8rem;
            background: rgb(16 138 68 / .07);
        }

        .guide-callout svg {
            width: 1.35rem;
            height: 1.35rem;
            flex: 0 0 1.35rem;
            margin-top: .1rem;
            color: rgb(16 138 68);
        }

        .guide-callout strong { display: block; margin-bottom: .2rem; }
        .guide-callout p { margin: 0; color: var(--guide-muted); line-height: 1.6; }

        @media (max-width: 800px) {
            .guide-grid { grid-template-columns: 1fr; }
        }

        @media print {
            @page { size: A4 portrait; margin: 14mm; }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            html,
            body,
            .fi-body,
            .fi-layout,
            .fi-main-ctn,
            .fi-main,
            .fi-page-header-main-ctn,
            .fi-page-main,
            .fi-page-content,
            .guide-page {
                display: block !important;
                width: 100% !important;
                height: auto !important;
                min-height: 0 !important;
                overflow: visible !important;
                background: #fff !important;
                color: #172033 !important;
            }

            .fi-sidebar,
            .fi-sidebar-close-overlay,
            .fi-topbar,
            .fi-page-header-main-ctn > .fi-header,
            .fi-page-header-main-ctn > header:not(.guide-intro),
            .guide-toolbar { display: none !important; }

            .fi-main-ctn {
                position: static !important;
                inset: auto !important;
                padding: 0 !important;
                margin: 0 !important;
                transform: none !important;
            }

            body * {
                visibility: hidden !important;
            }

            .guide-page,
            .guide-page * {
                visibility: visible !important;
                opacity: 1 !important;
            }

            .fi-main,
            .fi-page,
            .fi-page-header-main-ctn,
            .fi-page-main,
            .fi-page-content,
            .guide-page {
                padding: 0 !important;
                margin: 0 !important;
                transform: none !important;
                animation: none !important;
            }

            .fi-layout,
            .fi-main-ctn,
            #fi-main-content,
            .fi-page,
            .fi-page-header-main-ctn,
            .fi-page-main,
            .fi-page-content {
                opacity: 1 !important;
                filter: none !important;
                visibility: visible !important;
            }

            .guide-page {
                position: relative !important;
                z-index: 1 !important;
                text-shadow: none !important;
            }

            .guide-intro,
            .guide-card,
            .guide-callout {
                background: white !important;
                color: #172033 !important;
                box-shadow: none !important;
                break-inside: avoid;
            }
            .guide-grid { grid-template-columns: 1fr 1fr; gap: .55rem; }
            .guide-card { padding: .85rem; }
            .guide-card h3 { font-size: 10pt; }
            .guide-card ol,
            .guide-card ul,
            .guide-intro p,
            .guide-callout p { color: #334155 !important; font-size: 8.5pt; }
            .guide-card strong,
            .guide-intro h2,
            .guide-callout strong { color: #172033 !important; }
            .guide-card-header { gap: .55rem; margin-bottom: .5rem; }
            .guide-icon {
                width: 2rem;
                height: 2rem;
                flex-basis: 2rem;
                background: #f1e8f5 !important;
                color: #6b2b86 !important;
            }
            .guide-icon svg { width: 1rem; height: 1rem; }
            .guide-intro { padding: 1rem; }
            .guide-callout { margin-top: .75rem; }
        }
    </style>

    <div class="guide-page">
        <div class="guide-toolbar">
            <button type="button" class="guide-print-button" onclick="window.print()">
                <x-filament::icon icon="heroicon-o-printer" class="h-5 w-5" />
                Cetak Buku Panduan (PDF / Print)
            </button>
        </div>

        <header class="guide-intro">
            <h2>Panduan Operasional Sistem Inventaris</h2>
            <p>
                Buku panduan singkat untuk membantu pengelola dan staf menjalankan pencatatan aset,
                peminjaman, logistik, pemeliharaan, dan dokumen inventaris PT Pakuan Karya Mandiri.
                Gunakan navigasi di panel untuk membuka modul sesuai kewenangan Anda.
            </p>
        </header>

        <section class="guide-grid" aria-label="Modul panduan sistem">
            <article class="guide-card">
                <header class="guide-card-header">
                    <span class="guide-icon"><x-filament::icon icon="heroicon-o-cube" /></span>
                    <h3>1. Manajemen Aset Kantor</h3>
                </header>
                <ol>
                    <li>Buka <strong>Manajemen Aset → Data Aset → Tambah</strong>. Isi wizard empat langkah: Informasi Utama, Status &amp; PIC, Akuntansi &amp; Pajak, lalu Foto &amp; Pemeliharaan. Field wajib harus valid sebelum melanjutkan.</li>
                    <li>Arti status: <strong>Tersedia di Gudang</strong> berarti belum ditempatkan; <strong>Digunakan</strong> berarti digunakan di ruangan/PIC; <strong>Dipinjam</strong> berarti sedang dipinjam; <strong>Rusak/Afkir</strong> berarti tidak digunakan dan/atau telah dilepas.</li>
                    <li>Gunakan aksi <strong>Cetak Label</strong> pada baris aset untuk membuka stiker QR. Cetak label dan tempelkan pada aset agar mudah dipindai.</li>
                    <li>Gunakan <strong>Cetak BAST</strong> untuk dokumen serah terima aset kepada pengguna. Periksa nama PIC dan kondisi barang sebelum dokumen ditandatangani.</li>
                </ol>
            </article>

            <article class="guide-card">
                <header class="guide-card-header">
                    <span class="guide-icon"><x-filament::icon icon="heroicon-o-arrow-path-rounded-square" /></span>
                    <h3>2. Peminjaman Fasilitas Kantor</h3>
                </header>
                <ol>
                    <li>Aset <strong>Barang Bersama</strong> atau tanpa PIC dapat dipinjam jika status dan kondisinya memenuhi syarat. Aset dengan PIC pribadi staf tidak tersedia untuk peminjaman umum.</li>
                    <li>Buka <strong>Manajemen Aset → Peminjaman Aset</strong>, pilih aset yang tersedia, isi peminjam, tanggal pinjam, dan tanggal pengembalian yang direncanakan.</li>
                    <li>Saat barang dikembalikan, buka catatan pinjaman dan gunakan aksi konfirmasi pengembalian. Periksa kondisi barang sebelum mengonfirmasi.</li>
                    <li>Gunakan tab <strong>Sedang Dipinjam</strong>, <strong>Terlambat</strong>, <strong>Riwayat Pengembalian</strong>, atau <strong>Semua</strong> untuk memantau status pinjaman.</li>
                </ol>
            </article>

            <article class="guide-card">
                <header class="guide-card-header">
                    <span class="guide-icon"><x-filament::icon icon="heroicon-o-archive-box" /></span>
                    <h3>3. Logistik &amp; Perlengkapan ATK</h3>
                </header>
                <ol>
                    <li>Buka <strong>Logistik &amp; ATK → Mutasi Stok</strong>. Catat barang <strong>Masuk</strong> untuk penerimaan dari supplier; catat barang <strong>Keluar</strong> saat ATK diberikan kepada staf atau ruangan.</li>
                    <li>Isi penerima/supplier, jumlah, tanggal mutasi, dan ruangan tujuan bila barang keluar. Pastikan jumlah sesuai bukti penerimaan atau pengambilan.</li>
                    <li>Gunakan <strong>Unduh Template Excel</strong> pada tabel Aset atau Perlengkapan sebelum menyiapkan file. Template CSV memakai titik koma (<strong>;</strong>) sebagai pemisah kolom dan memiliki contoh data.</li>
                    <li>Unggah CSV/XLSX melalui aksi <strong>Impor</strong>. Kolom kategori dan ruangan dapat diisi dengan nama yang sudah terdaftar. Baris pertama harus berisi header kolom.</li>
                </ol>
            </article>

            <article class="guide-card">
                <header class="guide-card-header">
                    <span class="guide-icon"><x-filament::icon icon="heroicon-o-wrench-screwdriver" /></span>
                    <h3>4. Pemeliharaan &amp; Jadwal Servis</h3>
                </header>
                <ol>
                    <li>Lonceng notifikasi memberi pengingat jika ada jadwal servis yang jatuh tempo atau sudah lewat. Buka tautan di notifikasi untuk melihat aset terkait.</li>
                    <li>Pada daftar aset, pilih tab <strong>Perlu Servis &amp; Perbaikan</strong> untuk melihat aset yang jadwalnya dekat atau memiliki kondisi rusak.</li>
                    <li>Pilih aksi <strong>Update Servis</strong>, lalu catat tanggal servis, bengkel/teknisi, biaya, uraian perbaikan, kondisi setelah servis, dan tanggal servis berikutnya.</li>
                    <li>Riwayat vendor dan total biaya servis tersedia pada modal <strong>Lihat Detail</strong> masing-masing aset.</li>
                </ol>
            </article>

            <article class="guide-card">
                <header class="guide-card-header">
                    <span class="guide-icon"><x-filament::icon icon="heroicon-o-archive-box-x-mark" /></span>
                    <h3>5. Pelepasan Aset &amp; Dokumen BAP</h3>
                </header>
                <ol>
                    <li>Ajukan pelepasan untuk aset yang tidak layak digunakan, rusak berat, atau biaya perbaikannya tidak ekonomis. Pastikan alasan teknis dan persetujuan pejabat tersedia.</li>
                    <li>Pilih aksi <strong>Pelepasan Aset</strong>, isi tanggal, metode (dimusnahkan, dijual/dilelang, atau dihibahkan), alasan, dan nama pejabat penyetuju. Isi nilai jual untuk metode lelang/penjualan.</li>
                    <li>Setelah pencatatan, status aset berubah menjadi <strong>Rusak/Afkir</strong>. Aksi <strong>Cetak BAP</strong> tersedia untuk mencetak Berita Acara Penghapusan.</li>
                    <li>Lengkapi tanda tangan Pengelola Aset, Keuangan, dan Direktur sebelum dokumen disimpan sebagai arsip resmi.</li>
                </ol>
            </article>

            <article class="guide-card">
                <header class="guide-card-header">
                    <span class="guide-icon"><x-filament::icon icon="heroicon-o-building-office-2" /></span>
                    <h3>6. Kartu Inventaris Ruangan (KIR)</h3>
                </header>
                <ol>
                    <li>Buka <strong>Master Data → Ruangan</strong> dan pastikan kode, nama, lantai, serta deskripsi ruangan sudah benar.</li>
                    <li>Pada baris ruangan, pilih <strong>Cetak KIR</strong>. Dokumen menampilkan daftar aset dan PIC yang tercatat berada di ruangan tersebut.</li>
                    <li>Gunakan tombol <strong>Cetak / Simpan PDF</strong> pada halaman KIR. Pilih kertas A4 pada dialog cetak browser.</li>
                    <li>Setelah ditandatangani Penanggung Jawab Ruangan dan Bagian Umum &amp; Logistik (GA), cetak dan tempelkan KIR di dinding atau pintu ruangan.</li>
                </ol>
            </article>
        </section>

        <aside class="guide-callout">
            <x-filament::icon icon="heroicon-o-lifebuoy" />
            <div>
                <strong>Kontak Administrator / IT Support PT Pakuan Karya Mandiri</strong>
                <p>
                    Jika mengalami kendala akses, kesalahan data, atau membutuhkan bantuan penggunaan sistem,
                    hubungi Administrator Sistem atau tim IT Support internal melalui kanal komunikasi resmi perusahaan.
                    Sertakan nama modul dan kode aset/ruangan terkait agar penanganan lebih cepat.
                </p>
            </div>
        </aside>
    </div>
</x-filament-panels::page>
