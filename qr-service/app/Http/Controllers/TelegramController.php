<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    public function handleWebhook(Request $request)
    {
        Log::info('Webhook received: ' . json_encode($request->all()));
        $update = Telegram::getWebhookUpdate();

        if (!$update || !$update->getMessage()) {
            Log::warning('No valid update or message');
            return response()->json(['status' => 'error', 'message' => 'No valid update'], 200);
        }

        $chatId = $update->getMessage()->getChat()->getId();
        $text = $update->getMessage()->getText();
        $photo = $update->getMessage()->getPhoto();

        if ($text) {
            Log::info('Handling text: ' . $text);
            $this->handleText($chatId, $text);
        } elseif ($photo) {
            Log::info('Handling photo');
            $this->handlePhoto($chatId, $photo);
        } else {
            Log::warning('No text or photo in update');
        }

        return response()->json(['status' => 'success']);
    }

    private function handleText($chatId, $text)
    {
        if (str_starts_with($text, '/generate')) {
            $textToEncode = trim(substr($text, strlen('/generate')));
            if (empty($textToEncode)) {
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => 'Please provide text to generate a QR code. Example: /generate https://example.com',
                ]);
                return;
            }

            Log::info('Generating QR code for: ' . $textToEncode);
            $response = Http::post('http://127.0.0.1:8000/generate', [
                'text' => $textToEncode,
                'format' => 'json',
            ]);

            Log::info('API response: ' . $response->body());
            $data = $response->json();
            Log::info('API response data: ' . gettype($data));
            if ($data->status === 'success') {
                Telegram::sendPhoto([
                    'chat_id' => $chatId,
                    'photo' => $data->qr_url,
                    'caption' => 'Here is your QR code!',
                ]);
            } else {
                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => 'Error generating QR code: ' . $data['message'],
                ]);
            }
        } elseif ($text === '/start') {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => "Welcome to QR Code Master Bot!\n\nUse /generate <text> to create a QR code.\nSend a QR code image to decode it.",
            ]);
        } else {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => 'Unknown command. Use /generate <text> to create a QR code or send a QR code image to decode it.',
            ]);
        }
    }

    private function handlePhoto($chatId, $photo)
    {
        Log::info('Processing photo');
        $fileId = end($photo)['file_id'];
        $file = Telegram::getFile(['file_id' => $fileId]);
        $filePath = $file->getFilePath();

        $fileUrl = "https://api.telegram.org/file/bot" . env('TELEGRAM_BOT_TOKEN', 'YOUR_BOT_TOKEN') . "/" . $filePath;
        $tempFile = tempnam(sys_get_temp_dir(), 'qr_') . '.jpg';
        file_put_contents($tempFile, file_get_contents($fileUrl));

        Log::info('Decoding QR code from: ' . $tempFile);
        $response = Http::attach(
            'image',
            file_get_contents($tempFile),
            'qr.jpg'
        )->post('http://127.0.0.1:8000/decode', [
            'format' => 'json',
        ]);

        Log::info('API response: ' . $response->body());
        $data = $response->json();

        if ($data['status'] === 'success') {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => 'Decoded Text: ' . $data['decoded_text'],
            ]);
        } else {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => 'Error decoding QR code: ' . $data['message'],
            ]);
        }

        unlink($tempFile);
    }
}