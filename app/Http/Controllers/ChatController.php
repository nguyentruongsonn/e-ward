<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\TTHC;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $message = $request->input('message');
        if(!$message)
        {
            return response()->json(['error'=>'Message is required'],400);
        }

        // Fetch all procedures with related data
        $procedures = TTHC::with('linhVuc')->get();
        
        // Build context from database
        $context = "Bạn là trợ lý AI của Cổng dịch vụ công điện tử phường ABC. Hãy tư vấn thân thiện, ngắn gọn, dễ hiểu.\n\n";
        $context .= "DANH SÁCH THỦ TỤC HÀNH CHÍNH:\n\n";
        
        foreach($procedures as $index => $proc) {
            $context .= ($index + 1) . ". " . $proc->tenTTHC . "\n";
            if($proc->linhVuc) {
                $context .= "   - Lĩnh vực: " . $proc->linhVuc->tenLinhVuc . "\n";
            }
            if($proc->doiTuongThucHien) {
                $context .= "   - Đối tượng thực hiện: " . $proc->doiTuongThucHien . "\n";
            }
            if($proc->thoiHanGiaiQuyet) {
                $context .= "   - Thời hạn giải quyết: " . $proc->thoiHanGiaiQuyet . "\n";
            }
            if($proc->lePhi) {
                $context .= "   - Lệ phí: " . number_format($proc->lePhi) . " VNĐ\n";
            }
            if($proc->trinhTuThucHien) {
                $context .= "   - Trình tự thực hiện: " . $proc->trinhTuThucHien . "\n";
            }
            if($proc->yeuCauDieuKien) {
                $context .= "   - Yêu cầu điều kiện: " . $proc->yeuCauDieuKien . "\n";
            }
            $context .= "\n";
        }
        
        $context .= "\nKhi trả lời, hãy dựa vào thông tin trên để tư vấn chính xác về các thủ tục hành chính.";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . env('GEMINI_API_KEY'),
        [
            'system_instruction'=>[
                'parts'=>[
                    [
                        'text' => $context
                    ],
                ],
            ],
            'contents'=>[
                [
                    'role'=>'user',
                    'parts'=>[['text'=>$message]],
                ],
            ],
        ]);

        if($response->failed())
        {
            return response()->json(['error'=>'Failed to get response from AI'],500);
        }
        
        $data = $response->json();
        $reply = $data['candidates'][0]['content']['parts'][0]['text']??'Xin lỗi, tôi không thể trả lời câu hỏi của bạn vào lúc này.';
        return response()->json(['reply'=>$reply]);
    }
}
