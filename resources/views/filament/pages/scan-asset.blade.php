<x-filament-panels::page>
    @assets
        <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    @endassets

    <div class="mx-auto w-full max-w-3xl space-y-6">
        <section class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:p-6">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-950 dark:text-white">Arahkan kamera ke QR Code aset</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Izinkan akses kamera dan pastikan kode aset terlihat jelas di dalam kotak pemindai.
                </p>
            </div>

            <div
                id="asset-qr-reader"
                wire:ignore
                class="mx-auto min-h-64 w-full max-w-xl overflow-hidden rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-950"
            ></div>

            <p
                id="asset-qr-status"
                role="status"
                aria-live="polite"
                class="mt-3 min-h-6 text-sm text-gray-600 dark:text-gray-300"
            >Menyiapkan kamera…</p>

            @if ($scanMessage)
                <p
                    class="mt-2 text-sm {{ $assetResult ? 'text-success-600 dark:text-success-400' : 'text-warning-600 dark:text-warning-400' }}"
                    role="status"
                    aria-live="polite"
                >
                    {{ $scanMessage }}
                </p>
            @endif
        </section>

        @if ($assetResult)
            <section class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sm:p-6">
                <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-950 dark:text-white">Ringkasan Aset</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Hasil pemindaian QR Code.</p>
                    </div>
                    <span class="inline-flex rounded-full bg-primary-50 px-3 py-1 text-sm font-medium text-primary-700 dark:bg-primary-400/10 dark:text-primary-300">
                        {{ $assetResult['condition'] }}
                    </span>
                </div>

                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Kode Aset</dt>
                        <dd class="mt-1 font-mono font-semibold text-gray-950 dark:text-white">{{ $assetResult['asset_code'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Nama Barang</dt>
                        <dd class="mt-1 font-medium text-gray-950 dark:text-white">{{ $assetResult['name'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Ruangan</dt>
                        <dd class="mt-1 text-gray-950 dark:text-white">{{ $assetResult['room'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">PIC / Pemegang</dt>
                        <dd class="mt-1 text-gray-950 dark:text-white">{{ $assetResult['pic_name'] }}</dd>
                    </div>
                </dl>

                <div class="mt-6">
                    <x-filament::button :href="$assetResult['edit_url']" tag="a" icon="heroicon-o-pencil-square">
                        Buka Detail / Edit Aset
                    </x-filament::button>
                </div>
            </section>
        @endif
    </div>

    @script
        <script>
            const statusElement = document.getElementById('asset-qr-status');
            let scanner;
            let scanLocked = false;

            const showCameraError = (error) => {
                const detail = error instanceof Error ? ` ${error.message}` : '';
                statusElement.textContent = `Kamera tidak dapat dijalankan.${detail} Periksa izin kamera dan gunakan HTTPS atau localhost.`;
            };

            const playBeep = () => {
                try {
                    const audioContext = new AudioContext();
                    const oscillator = audioContext.createOscillator();
                    const gain = audioContext.createGain();

                    oscillator.frequency.value = 880;
                    gain.gain.setValueAtTime(0.15, audioContext.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, audioContext.currentTime + 0.18);
                    oscillator.connect(gain);
                    gain.connect(audioContext.destination);
                    oscillator.start();
                    oscillator.stop(audioContext.currentTime + 0.18);
                    oscillator.addEventListener('ended', () => audioContext.close(), { once: true });
                } catch (error) {
                    console.warn('Bunyi notifikasi tidak tersedia pada perangkat ini.', error);
                }
            };

            const handleScan = async (decodedText) => {
                if (scanLocked) {
                    return;
                }

                scanLocked = true;
                playBeep();
                statusElement.textContent = 'QR terdeteksi. Mencari data aset…';

                try {
                    await $wire.lookupAsset(decodedText);
                    statusElement.textContent = 'Pemindaian selesai. Arahkan kamera ke QR lain untuk memindai lagi.';
                } catch (error) {
                    console.error('Pencarian aset dari QR gagal.', error);
                    statusElement.textContent = 'Pencarian aset gagal. Silakan pindai ulang atau muat ulang halaman.';
                } finally {
                    window.setTimeout(() => {
                        scanLocked = false;
                    }, 1800);
                }
            };

            const startScanner = async () => {
                if (typeof Html5Qrcode === 'undefined') {
                    statusElement.textContent = 'Library pemindai QR gagal dimuat. Periksa koneksi internet lalu muat ulang halaman.';

                    return;
                }

                scanner = new Html5Qrcode('asset-qr-reader');

                try {
                    const cameras = await Html5Qrcode.getCameras();

                    if (!cameras.length) {
                        statusElement.textContent = 'Kamera tidak ditemukan pada perangkat ini.';

                        return;
                    }

                    const camera = cameras.find(({ label }) => /back|rear|environment/i.test(label)) ?? cameras[0];

                    await scanner.start(
                        camera.id,
                        { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1 },
                        handleScan,
                        () => {},
                    );

                    statusElement.textContent = 'Kamera aktif. Arahkan ke QR Code aset.';
                } catch (error) {
                    showCameraError(error);
                }
            };

            startScanner();

            document.addEventListener('livewire:navigating', () => {
                if (scanner?.isScanning) {
                    scanner.stop().catch((error) => {
                        console.warn('Pemindai kamera tidak berhasil dihentikan saat navigasi.', error);
                    });
                }
            }, { once: true });
        </script>
    @endscript
</x-filament-panels::page>
