<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\TTHC;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        try {
            $message = $request->input('message');
            if(!$message)
            {
                return response()->json(['error'=>'Message is required'],400);
            }

            $procedures = TTHC::with('linhVuc')->get();
            
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
                    $shortTrinhTu = \Illuminate\Support\Str::limit($proc->trinhTuThucHien, 150);
                    $context .= "   - Trình tự thực hiện: " . $shortTrinhTu . "\n";
                }
                if($proc->yeuCauDieuKien) {
                    $shortYeuCau = \Illuminate\Support\Str::limit($proc->yeuCauDieuKien, 150);
                    $context .= "   - Yêu cầu điều kiện: " . $shortYeuCau . "\n";
                }
                $context .= "\n";
            }
            
            $context .= "\nKhi trả lời, hãy dựa vào thông tin trên để tư vấn chính xác về các thủ tục hành chính.";

            // Groq API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $context
                    ],
                    [
                        'role' => 'user',
                        'content' => $message
                    ],
                ],
                'temperature' => 0.7,
                'max_tokens' => 1024,
            ]);

            if($response->failed())
            {
                \Log::error('Groq API failed', ['response' => $response->body()]);
                return response()->json(['error'=>'Failed to get response from AI'],500);
            }
            
            $data = $response->json();
            $reply = $data['choices'][0]['message']['content'] ?? 'Xin lỗi, tôi không thể trả lời câu hỏi của bạn vào lúc này.';
            return response()->json(['reply'=>$reply]);
        } catch (\Exception $e) {
            \Log::error('Chat error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }
}
