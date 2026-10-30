<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Contracts\View\View;

class RoomKirController extends Controller
{
    public function show(Room $room): View
    {
        $room->load(['assets' => fn ($query) => $query->orderBy('asset_code')]);

        return view('kir-document', [
            'room' => $room,
            'printedAt' => now(),
        ]);
    }
}
