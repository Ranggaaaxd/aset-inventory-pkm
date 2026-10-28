<div class="space-y-4 text-xs font-sans">
    <!-- Ringkasan Metadata Aktivitas -->
    <div class="grid grid-cols-2 gap-3 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 text-gray-700 dark:text-gray-300">
        <div>
            <span class="block text-[10px] uppercase font-bold text-gray-400">Pengguna / Editor</span>
            <span class="font-bold text-gray-900 dark:text-white text-sm">{{ $record->user_name ?? 'System' }}</span>
        </div>
        <div>
            <span class="block text-[10px] uppercase font-bold text-gray-400">Waktu Perubahan</span>
            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $record->created_at->format('d/m/Y H:i:s') }} ({{ $record->created_at->diffForHumans() }})</span>
        </div>
        <div>
            <span class="block text-[10px] uppercase font-bold text-gray-400">Jenis Data (Model)</span>
            <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $record->model_label }}</span>
        </div>
        <div>
            <span class="block text-[10px] uppercase font-bold text-gray-400">IP Address</span>
            <span class="font-mono text-gray-600 dark:text-gray-400">{{ $record->ip_address ?? '-' }}</span>
        </div>
        <div class="col-span-2 border-t border-gray-200 dark:border-gray-800 pt-2">
            <span class="block text-[10px] uppercase font-bold text-gray-400">Target Rekaman</span>
            <span class="font-bold text-gray-900 dark:text-white text-sm">{{ $record->record_label ?? '-' }}</span>
        </div>
    </div>

    <!-- Tabel Rincian Perubahan Nilai (Diff) -->
    <div>
        <h4 class="font-bold text-gray-900 dark:text-white mb-2 text-xs uppercase tracking-wider flex items-center gap-1.5">
            <span>Rincian Nilai Data</span>
            @if($record->action === 'updated' && $record->changed_fields)
                <span class="text-[10px] font-normal text-gray-500">({{ count($record->changed_fields) }} kolom diubah)</span>
            @endif
        </h4>

        @if($record->action === 'updated')
            @php
                $changed = $record->changed_fields ?? array_keys($record->new_values ?? []);
            @endphp
            <div class="overflow-hidden border border-gray-200 dark:border-gray-800 rounded-lg">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-800 text-[10px] uppercase font-bold text-gray-600 dark:text-gray-300">
                            <th class="p-2 border-b border-gray-200 dark:border-gray-700">Nama Kolom</th>
                            <th class="p-2 border-b border-gray-200 dark:border-gray-700">Nilai Sebelumnya</th>
                            <th class="p-2 border-b border-gray-200 dark:border-gray-700">Nilai Baru</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @foreach($changed as $field)
                            @php
                                $old = $record->old_values[$field] ?? null;
                                $new = $record->new_values[$field] ?? null;
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50">
                                <td class="p-2 font-mono font-bold text-gray-700 dark:text-gray-300">{{ $field }}</td>
                                <td class="p-2 text-red-600 dark:text-red-400 bg-red-50/50 dark:bg-red-950/20 font-medium">
                                    {{ is_array($old) ? json_encode($old) : ($old ?? '(kosong / null)') }}
                                </td>
                                <td class="p-2 text-emerald-600 dark:text-emerald-400 bg-emerald-50/50 dark:bg-emerald-950/20 font-bold">
                                    {{ is_array($new) ? json_encode($new) : ($new ?? '(kosong / null)') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @elseif($record->action === 'created')
            <div class="overflow-hidden border border-gray-200 dark:border-gray-800 rounded-lg">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-800 text-[10px] uppercase font-bold text-gray-600 dark:text-gray-300">
                            <th class="p-2 border-b border-gray-200 dark:border-gray-700">Nama Kolom</th>
                            <th class="p-2 border-b border-gray-200 dark:border-gray-700">Nilai Input Awal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @foreach($record->new_values ?? [] as $field => $val)
                            <tr>
                                <td class="p-2 font-mono font-semibold text-gray-600 dark:text-gray-400">{{ $field }}</td>
                                <td class="p-2 font-medium text-gray-900 dark:text-white">
                                    {{ is_array($val) ? json_encode($val) : ($val ?? '-') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @elseif($record->action === 'deleted')
            <div class="overflow-hidden border border-gray-200 dark:border-gray-800 rounded-lg">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-800 text-[10px] uppercase font-bold text-gray-600 dark:text-gray-300">
                            <th class="p-2 border-b border-gray-200 dark:border-gray-700">Nama Kolom</th>
                            <th class="p-2 border-b border-gray-200 dark:border-gray-700">Nilai Terakhir Sebelum Dihapus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @foreach($record->old_values ?? [] as $field => $val)
                            <tr>
                                <td class="p-2 font-mono font-semibold text-gray-600 dark:text-gray-400">{{ $field }}</td>
                                <td class="p-2 font-medium text-gray-700 dark:text-gray-300">
                                    {{ is_array($val) ? json_encode($val) : ($val ?? '-') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

