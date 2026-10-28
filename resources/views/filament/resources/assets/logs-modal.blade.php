<div class="space-y-4 text-xs font-sans">
    <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 flex justify-between items-center">
        <div>
            <span class="block text-[10px] uppercase font-bold text-gray-400">Kode &amp; Nama Aset</span>
            <span class="font-bold text-gray-900 dark:text-white text-sm">{{ $record->asset_code }} - {{ $record->name }}</span>
        </div>
        <div class="text-right">
            <span class="block text-[10px] uppercase font-bold text-gray-400">Total Log Tercatat</span>
            <span class="font-bold text-blue-600 dark:text-blue-400 text-sm">{{ $logs->count() }} Entri</span>
        </div>
    </div>

    @if($logs->isEmpty())
        <div class="text-center py-6 text-gray-400">
            <p>Belum ada riwayat perubahan yang tercatat untuk aset ini.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($logs as $log)
                <div class="p-3 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-lg space-y-2">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                {{ $log->action === 'created' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-400' :
                                  ($log->action === 'updated' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-400' :
                                   'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-400') }}">
                                {{ $log->action_label }}
                            </span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">Oleh: <strong>{{ $log->user_name ?? 'System' }}</strong></span>
                        </div>
                        <span class="text-[10px] text-gray-400 font-mono">{{ $log->created_at->format('d/m/Y H:i:s') }} ({{ $log->created_at->diffForHumans() }})</span>
                    </div>

                    @if($log->action === 'updated' && $log->changed_fields)
                        <div class="mt-2 text-[11px] border-t border-gray-100 dark:border-gray-800/80 pt-2">
                            <span class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Perubahan:</span>
                            <div class="space-y-1">
                                @foreach($log->changed_fields as $field)
                                    @php
                                        $old = $log->old_values[$field] ?? null;
                                        $new = $log->new_values[$field] ?? null;
                                    @endphp
                                    <div class="flex items-center gap-2 font-mono text-[10px]">
                                        <span class="font-bold text-gray-700 dark:text-gray-300 w-28">{{ $field }}:</span>
                                        <span class="text-red-600 line-through">{{ is_array($old) ? json_encode($old) : ($old ?? 'null') }}</span>
                                        <span class="text-gray-400">&rarr;</span>
                                        <span class="text-emerald-600 font-bold">{{ is_array($new) ? json_encode($new) : ($new ?? 'null') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

