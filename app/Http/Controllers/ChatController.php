<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $message = $request->input('message');
        if(!$message)
        {
            return response()->json(['error'=>'Message is required'],400);

        }
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . env('GEMINI_API_KEY'),
        [
            'system_instruction'=>[
                'parts'=>[
                    [
                        'text' => "Bạn là trợ lý AI của Cổng dịch vụ công điện tử phường ABC. Hãy tư vấn thân thiện, ngắn gọn, dễ hiểu"
                    ],
                ],
            ],
            'contents'=>[
                [
                    'role'=>'user',
                    'parts'=>[['text'=>$message]],
                ],
            ],
        ],

    );
    if($response->failed())
    {
        return response()->json(['error'=>'Failed to get response from AI'],500);
    }
    $data = $response->json();
    $reply = $data['candidates'][0]['content']['parts'][0]['text']??'Xin lỗi, tôi không thể trả lời câu hỏi của bạn vào lúc này.';
    return response()->json(['reply'=>$reply]);

}
}
