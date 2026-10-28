<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;

class AssetLabelController extends Controller
{
    /**
     * Display a printable label page for a single asset.
     */
    public function printSingle(Asset $asset)
    {
        $asset->load(['room', 'category']);
        $qrCode = (new QRCode())->render($asset->asset_code);

        return view('labels.print-asset-label', [
            'assets' => [$asset],
            'qrCodes' => [$asset->id => $qrCode],
            'title' => 'Cetak Label Aset - ' . $asset->asset_code,
        ]);
    }

    /**
     * Display a printable page for multiple assets (batch print).
     */
    public function printBatch(Request $request)
    {
        $ids = explode(',', $request->query('ids', ''));
        $assets = Asset::with(['room', 'category'])
            ->whereIn('id', array_filter($ids))
            ->orderBy('id', 'asc')
            ->get();

        if ($assets->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada aset yang dipilih untuk dicetak.');
        }

        $qrCodeGenerator = new QRCode();
        $qrCodes = [];
        foreach ($assets as $asset) {
            $qrCodes[$asset->id] = $qrCodeGenerator->render($asset->asset_code);
        }

        return view('labels.print-asset-label', [
            'assets' => $assets,
            'qrCodes' => $qrCodes,
            'title' => 'Cetak Label Aset (' . $assets->count() . ' Item)',
        ]);
    }
}

