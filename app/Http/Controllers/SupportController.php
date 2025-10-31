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
        if ($categories->isEmpty()) {
            $categories = collect([
                (object) ['id_loaicauhoi' => '1', 'name_loaicauhoi' => 'Đăng ký, đăng nhập tài khoản'],
                (object) ['id_loaicauhoi' => '2', 'name_loaicauhoi' => 'Thanh toán nghĩa vụ tài chính'],
                (object) ['id_loaicauhoi' => '3', 'name_loaicauhoi' => 'Tra cứu nghĩa vụ tài chính'],
                (object) ['id_loaicauhoi' => '4', 'name_loaicauhoi' => 'Các lỗi bên ngân hàng, trung gian thanh toán'],
            ]);
        }

        // Chỉ truy vấn khi có từ khóa hoặc đã chọn loại; ngược lại trả danh sách rỗng
        if ($q !== '' || $categoryId !== null) {
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
        } else {
            $faqs = collect();
        }

        return view('support.faq', compact('categories', 'faqs', 'q', 'categoryId'));
    }
}
