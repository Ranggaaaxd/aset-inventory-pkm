@php
    $qrCode = (new \chillerlan\QRCode\QRCode)->render($record->asset_code);
@endphp

<div class="space-y-4">
    <!-- Pratinjau Stiker Fisik -->
    <div id="printable-asset-label-{{ $record->id }}" class="mx-auto max-w-md bg-white border-2 border-gray-900 rounded-lg p-4 text-gray-900 shadow-sm print:m-0 print:border-2 print:border-black print:shadow-none font-sans">
        <!-- Header Perusahaan -->
        <div class="border-b-2 border-gray-900 pb-2 mb-3 text-center">
            <h2 class="text-sm font-extrabold tracking-wider uppercase text-gray-900">PAKUAN KARYA MANDIRI</h2>
            <p class="text-[10px] font-semibold text-gray-600 uppercase tracking-widest">Sistem Inventaris & Aset Kantor</p>
        </div>

        <!-- Konten Tengah: QR Code & Detail Aset -->
        <div class="flex items-center gap-4">
            <!-- QR Code -->
            <div class="flex-shrink-0 bg-white p-1 border border-gray-300 rounded text-center">
                <img src="{{ $qrCode }}" alt="QR Code {{ $record->asset_code }}" class="w-24 h-24 object-contain" />
                <span class="block text-[9px] font-mono font-bold mt-1 text-gray-700 tracking-tighter">{{ $record->asset_code }}</span>
            </div>

            <!-- Rincian Data Aset -->
            <div class="flex-1 space-y-1 text-xs">
                <div>
                    <span class="text-[10px] uppercase font-bold text-gray-500 block">Kode Aset</span>
                    <span class="font-mono font-black text-sm text-blue-700">{{ $record->asset_code }}</span>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-gray-500 block">Nama Barang</span>
                    <span class="font-bold text-gray-900 line-clamp-2 leading-tight">{{ $record->name }}</span>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-gray-500 block">Ruangan</span>
                    <span class="font-semibold text-gray-800">{{ $record->room?->name ?? '-' }}</span>
                </div>
                @if($record->pic_name)
                <div>
                    <span class="text-[10px] uppercase font-bold text-gray-500 block">PIC / Pemegang</span>
                    <span class="font-semibold text-emerald-700">{{ $record->pic_name }}</span>
                </div>
                @endif
                @if($record->brand)
                <div>
                    <span class="text-[10px] uppercase font-bold text-gray-500 block">Merk / Tipe</span>
                    <span class="text-gray-700">{{ $record->brand }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Footer / Watermark Stiker -->
        <div class="border-t border-gray-300 mt-3 pt-1 flex justify-between items-center text-[9px] text-gray-500">
            <span>Kondisi: <strong>{{ $record->condition }}</strong></span>
            <span class="italic">Dilarang Merusak / Melepas Stiker</span>
        </div>
    </div>

    <!-- Tombol Aksi Cetak -->
    <div class="flex justify-end gap-2 pt-2 border-t border-gray-200 dark:border-gray-800">
        <a href="{{ route('assets.print-label', ['asset' => $record->getKey() ?? $record->id ?? 1]) }}" target="_blank"
           class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Buka Halaman Cetak (Print)
        </a>
    </div>
</div>
