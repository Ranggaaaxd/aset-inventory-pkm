<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;

class AssetBastController extends Controller
{
    public function show(Asset $asset): View
    {
        $imageUrl = $asset->image && Storage::disk('public')->exists($asset->image)
            ? Storage::disk('public')->url($asset->image)
            : null;

        return view('bast-document', [
            'asset' => $asset,
            'imageUrl' => $imageUrl,
            'printedAt' => now(),
        ]);
    }
}
