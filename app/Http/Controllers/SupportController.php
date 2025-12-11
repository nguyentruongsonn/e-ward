<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faq;
use App\Models\Notice;

class SupportController extends Controller
{
    public function about()
    {
        return view('support.about');
    }

    public function terms()
    {
        return view('support.terms');
    }

    public function guide()
    {
        return view('support.guide');
    }

    public function notice()
    {
        $notices = Notice::query()
            ->orderByDesc('create_at')
            ->paginate(12);

        return view('support.notice', compact('notices'));
    }

    public function faq(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $categoryId = $request->get('category');
        $categoryId = $categoryId !== null && $categoryId !== '' ? (string) $categoryId : null;

        $categories = Faq::select('id_loaicauhoi', 'name_loaicauhoi')
            ->groupBy('id_loaicauhoi', 'name_loaicauhoi')
            ->orderBy('id_loaicauhoi')
            ->get();
        
        // Hiển thị tất cả FAQ khi không có tìm kiếm, hoặc lọc theo điều kiện
        $faqs = Faq::query()
            ->when($q !== '', function ($qb) use ($q) {
                return $qb->where(function ($inner) use ($q) {
                    $inner->where('cauhoi', 'like', "%{$q}%")
                          ->orWhere('dapan', 'like', "%{$q}%");
                });
            })
            ->when($categoryId !== null, fn($qb) => $qb->where('id_loaicauhoi', $categoryId))
            ->orderBy('id', 'desc')
            ->get();

        return view('support.faq', compact('categories', 'faqs', 'q', 'categoryId'));
    }
}
