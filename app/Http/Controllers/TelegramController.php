<?php

namespace App\Http\Controllers;

use App\Models\GajiReport;
use App\Models\Expense;
use App\Models\TelegramLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();
        
        if (!isset($payload['message'])) return response()->json(['status' => 'no message']);
        
        $message = $payload['message'];
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';

        // Anti-SPAM / Authorized ID
        if (env('TELEGRAM_ID') && $chatId != env('TELEGRAM_ID')) {
            $this->sendMessage($chatId, "Maaf, Anda tidak diizinkan menggunakan bot ini.");
            return response()->json(['status' => 'unauthorized']);
        }

        TelegramLog::create([
            'telegram_id' => $chatId,
            'raw_message' => json_encode($payload),
            'status' => 'processed'
        ]);

        // Logic: Photo OCR
        if (isset($message['photo'])) {
            return $this->handlePhoto($message);
        }

        // Logic: Text Regex (Format: "Makan 50000")
        if (preg_match('/(.*?)\s(\d+)$/', $text, $matches)) {
            $item = trim($matches[1]);
            $nominal = (int)$matches[2];
            
            $this->addExpenseFromTelegram($item, $nominal);
            $this->sendMessage($chatId, "✅ Catat: *$item* sebesar *Rp " . number_format($nominal) . "* telah dimasukkan ke pengeluaran lainnya.");
            return response()->json(['status' => 'ok']);
        }

        if ($text == '/start') {
            $this->sendMessage($chatId, "Halo! Kirim pesan format: `NamaBarang Harga` (misal: Makan 50000) atau kirim Foto Struk.");
            return response()->json(['status' => 'ok']);
        }

        return response()->json(['status' => 'no action']);
    }

    private function handlePhoto($message)
    {
        $chatId = $message['chat']['id'];
        $photo = end($message['photo']); // Get highest resolution
        $fileId = $photo['file_id'];
        
        $response = Http::get("https://api.telegram.org/bot".env('TELEGRAM_BOT_TOKEN')."/getFile?file_id=$fileId");
        $filePath = $response->json()['result']['file_path'];
        $fileUrl = "https://api.telegram.org/file/bot".env('TELEGRAM_BOT_TOKEN')."/$filePath";

        // Call OCR.space
        $ocrResponse = Http::asMultipart()->post('https://api.ocr.space/parse/image', [
            'apikey' => env('OCR_SPACE_API_KEY', 'K83742468588957'),
            'url' => $fileUrl,
            'language' => 'eng',
            'isOverlayRequired' => 'false',
        ]);

        $parsedText = $ocrResponse->json()['ParsedResults'][0]['ParsedText'] ?? '';
        
        // Find Largest Number (usually the total)
        preg_match_all('/\d+[\.,]?\d+/', $parsedText, $numbers);
        $maxNominal = 0;
        if (!empty($numbers[0])) {
            foreach ($numbers[0] as $num) {
                $cleanNum = (int)str_replace([',', '.'], '', $num);
                if ($cleanNum > $maxNominal) $maxNominal = $cleanNum;
            }
        }

        if ($maxNominal > 0) {
            $this->addExpenseFromTelegram("Struk Foto", $maxNominal);
            $this->sendMessage($chatId, "📸 Struk terdeteksi! Nominal otomatis: *Rp " . number_format($maxNominal) . "* dimasukkan ke pengeluaran lainnya.");
        } else {
            $this->sendMessage($chatId, "Maaf, nominal tidak terbaca di struk ini. Coba ketik manual.");
        }

        return response()->json(['status' => 'photo_processed']);
    }

    private function addExpenseFromTelegram($item, $nominal)
    {
        $periode = now()->translatedFormat('F Y');
        $report = GajiReport::firstOrCreate(['periode' => $periode]);
        
        $report->expenses()->create([
            'nama_item' => $item,
            'nominal' => $nominal,
            'kategori' => 'lainnya'
        ]);

        // Recalculate
        $total = $report->expenses()->sum('nominal');
        $report->update([
            'total_pengeluaran' => $total,
            'sisa_bersih' => $report->gaji_pokok - $total
        ]);
    }

    private function sendMessage($chatId, $text)
    {
        Http::post("https://api.telegram.org/bot".env('TELEGRAM_BOT_TOKEN')."/sendMessage", [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'Markdown'
        ]);
    }
}
