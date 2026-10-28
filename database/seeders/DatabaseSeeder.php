<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Room;
use App\Models\Supply;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database for Pakuan Karya Mandiri.
     */
    public function run(): void
    {
        // 1. DATA RUANGAN KANTOR
        $rooms = [
            ['code' => 'RM-DIR', 'name' => 'Ruang Direksi & Manajemen', 'location_floor' => 'Lantai 2', 'description' => 'Ruangan Direktur dan jajaran manajemen'],
            ['code' => 'RM-OPS', 'name' => 'Ruang Staff Operasional', 'location_floor' => 'Lantai 1', 'description' => 'Area kerja tim operasional'],
            ['code' => 'RM-FIN', 'name' => 'Ruang Keuangan & HRD', 'location_floor' => 'Lantai 1', 'description' => 'Area administrasi, payroll, dan keuangan'],
            ['code' => 'RM-MTG', 'name' => 'Ruang Rapat Utama (Meeting Room)', 'location_floor' => 'Lantai 2', 'description' => 'Ruangan pertemuan internal dan klien'],
            ['code' => 'RM-IT',  'name' => 'Ruang Server & IT Support', 'location_floor' => 'Lantai 1', 'description' => 'Pusat server, router jaringan, dan IT'],
            ['code' => 'RM-GUD', 'name' => 'Gudang Penyimpanan ATK & Logistik', 'location_floor' => 'Lantai 1', 'description' => 'Tempat stok perlengkapan kantor'],
        ];

        foreach ($rooms as $room) {
            Room::updateOrCreate(['code' => $room['code']], $room);
        }

        // 2. DATA KATEGORI
        $categories = [
            ['name' => 'Elektronik & IT', 'slug' => 'elektronik-it', 'type' => 'asset', 'description' => 'Laptop, Komputer PC, Printer, Router, dsb.'],
            ['name' => 'Mebel & Perabot Kantor', 'slug' => 'mebel-perabot', 'type' => 'asset', 'description' => 'Meja kerja, kursi staff, lemari berkas, AC'],
            ['name' => 'Fasilitas Meeting', 'slug' => 'fasilitas-meeting', 'type' => 'asset', 'description' => 'Proyektor, Smart TV, Sound System'],
            ['name' => 'Alat Tulis Kantor (ATK)', 'slug' => 'atk', 'type' => 'supply', 'description' => 'Kertas, pulpen, spidol, binder, amplop'],
            ['name' => 'Perlengkapan Pantry & Kebersihan', 'slug' => 'pantry-kebersihan', 'type' => 'supply', 'description' => 'Tisu, sabun, refill dispenser'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        // Ambil ID referensi
        $roomOps = Room::where('code', 'RM-OPS')->first();
        $roomMtg = Room::where('code', 'RM-MTG')->first();
        $roomDir = Room::where('code', 'RM-DIR')->first();
        $catIT = Category::where('slug', 'elektronik-it')->first();
        $catFurn = Category::where('slug', 'mebel-perabot')->first();
        $catMtg = Category::where('slug', 'fasilitas-meeting')->first();
        $catATK = Category::where('slug', 'atk')->first();

        // 3. DATA CONTOH ASET KANTOR
        $assets = [
            [
                'asset_code' => 'PKM-AST-001',
                'name' => 'Laptop ThinkPad E14 Gen 4',
                'category_id' => $catIT->id,
                'room_id' => $roomOps->id,
                'pic_name' => 'Rian Hidayat (Staff IT/Ops)',
                'pic_contact' => '081298765432',
                'condition' => 'Baik',
                'brand' => 'Lenovo',
                'purchase_date' => '2024-01-15',
                'price' => 12500000,
                'notes' => 'Inventaris tim operasional',
            ],
            [
                'asset_code' => 'PKM-AST-002',
                'name' => 'Printer Multifungsi EcoTank L3210',
                'category_id' => $catIT->id,
                'room_id' => $roomOps->id,
                'pic_name' => null,
                'pic_contact' => null,
                'condition' => 'Baik',
                'brand' => 'Epson',
                'purchase_date' => '2023-08-10',
                'price' => 2800000,
                'notes' => 'Digunakan bersama ruang operasional',
            ],
            [
                'asset_code' => 'PKM-AST-003',
                'name' => 'AC Split Inverter 1.5 PK',
                'category_id' => $catFurn->id,
                'room_id' => $roomMtg->id,
                'pic_name' => null,
                'pic_contact' => null,
                'condition' => 'Baik',
                'brand' => 'Daikin',
                'purchase_date' => '2023-05-20',
                'price' => 6200000,
                'notes' => 'Servis berkala tiap 3 bulan',
            ],
            [
                'asset_code' => 'PKM-AST-004',
                'name' => 'Proyektor Full HD 4000 Lumens',
                'category_id' => $catMtg->id,
                'room_id' => $roomMtg->id,
                'pic_name' => null,
                'pic_contact' => null,
                'condition' => 'Baik',
                'brand' => 'BenQ',
                'purchase_date' => '2024-02-01',
                'price' => 8500000,
                'notes' => 'Peralatan presentasi ruang rapat',
            ],
            [
                'asset_code' => 'PKM-AST-005',
                'name' => 'Kursi Kerja Ergonomis Direktur',
                'category_id' => $catFurn->id,
                'room_id' => $roomDir->id,
                'pic_name' => 'Ir. Bambang Sugiarto (Direktur)',
                'pic_contact' => '081122334455',
                'condition' => 'Baik',
                'brand' => 'Informa',
                'purchase_date' => '2023-03-12',
                'price' => 3100000,
                'notes' => 'Kondisi mulus',
            ],
        ];

        foreach ($assets as $asset) {
            Asset::updateOrCreate(['asset_code' => $asset['asset_code']], $asset);
        }

        // 4. DATA CONTOH PERLENGKAPAN / ATK (HABIS PAKAI)
        $supplies = [
            [
                'item_code' => 'PKM-ATK-001',
                'name' => 'Kertas HVS A4 75 GSM (PaperOne)',
                'category_id' => $catATK->id,
                'stock' => 25,
                'min_stock' => 5,
                'unit' => 'Rim',
                'notes' => 'Kebutuhan dokumen cetak harian',
            ],
            [
                'item_code' => 'PKM-ATK-002',
                'name' => 'Tinta Botol Epson 003 Black',
                'category_id' => $catATK->id,
                'stock' => 8,
                'min_stock' => 2,
                'unit' => 'Botol',
                'notes' => 'Refill printer Epson',
            ],
            [
                'item_code' => 'PKM-ATK-003',
                'name' => 'Spidol Whiteboard Snowman Hitam',
                'category_id' => $catATK->id,
                'stock' => 12,
                'min_stock' => 3,
                'unit' => 'Pcs',
                'notes' => 'Stok untuk ruang rapat',
            ],
            [
                'item_code' => 'PKM-ATK-004',
                'name' => 'Baterai AA Alkaline (Isi 2)',
                'category_id' => $catATK->id,
                'stock' => 4,
                'min_stock' => 5, // Stok sudah di bawah minimum untuk contoh alert!
                'unit' => 'Pack',
                'notes' => 'Untuk mouse wireless dan remote AC',
            ],
        ];

        foreach ($supplies as $supply) {
            Supply::updateOrCreate(['item_code' => $supply['item_code']], $supply);
        }
    }
}
