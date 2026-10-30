<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Contracts\View\View;

class AssetDisposalController extends Controller
{
    public function show(Asset $asset): View
    {
        $asset->load(['disposal', 'category', 'room']);
        abort_unless($asset->disposal, 404);

        return view('bap-document', [
            'asset' => $asset,
            'disposal' => $asset->disposal,
            'printedAt' => now(),
        ]);
    }
}
