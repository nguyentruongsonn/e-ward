<?php

namespace App\Http\Controllers;

use App\Models\TTHC;
use Illuminate\Http\Request;

class OutstandingServiceController extends Controller
{
    public function index()
    {
        // Lấy danh sách các thủ tục hành chính nổi bật
        $tthcs = TTHC::orderBy('tenTTHC', 'asc')->get();

        // Truyền sang view
        return view('pages.outstanding-service', compact('tthcs'));
    }
}
