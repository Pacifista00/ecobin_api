<?php

namespace App\Http\Controllers;

use App\Models\Bin;
use App\Models\History;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index()
    {
        $histories = History::latest()->get();

        return response()->json([
            'message' => 'Semua history berhasil diambil.',
            'histories' => $histories
        ], 200);
    }
    public function emptyBin(Request $request, $id, $type)
    {
        $bin = Bin::findOrFail($id);

        History::create([
            'bin_id' => $bin->id,
            'information' => 'Tempat sampah ' . $type . ' dikosongkan pada pukul ' . now()->format('H.i'),
        ]);


        return response()->json([
            'message' => 'Bin emptied successfully',
        ]);
    }
}
