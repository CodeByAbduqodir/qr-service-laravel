<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Http;

class QRController extends Controller
{
    public function index()
    {
        return view('qr.index');
    }

    public function generate(Request $request)
    {
        $text = $request->input('text');

        if (!$text) {
            return response()->json(['status' => 'error', 'message' => 'No text provided']);
        }

        $options = new QROptions([
            'version' => 5,
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel' => QRCode::ECC_L,
            'scale' => 5,
        ]);

        $filename = 'qrcode_' . time() . '_' . bin2hex(random_bytes(4)) . '.png';
        $filepath = public_path('qrcodes/' . $filename);

        $qr = new QRCode($options);
        $qr->render($text, $filepath);

        $qr_url = url('qrcodes/' . $filename);

        if ($request->has('format') && $request->input('format') === 'json') {
            return response()->json([
                'status' => 'success',
                'qr_url' => $qr_url,
                'message' => 'QR code generated'
            ]);
        }

        return view('qr.generate', compact('qr_url'));
    }

    public function decode(Request $request)
    {
        if (!$request->hasFile('image')) {
            return response()->json(['status' => 'error', 'message' => 'No image provided']);
        }

        $image = $request->file('image');
        if (!$image->isValid() || !in_array($image->getClientMimeType(), ['image/png', 'image/jpeg', 'image/gif'])) {
            return response()->json(['status' => 'error', 'message' => 'Invalid image format']);
        }

        $response = Http::attach(
            'file',
            file_get_contents($image->getRealPath()),
            $image->getClientOriginalName()
        )->post('http://api.qrserver.com/v1/read-qr-code/');

        $data = $response->json();

        if ($data && isset($data[0]['symbol'][0]['data'])) {
            $decoded_text = $data[0]['symbol'][0]['data'];
            if ($request->has('format') && $request->input('format') === 'json') {
                return response()->json([
                    'status' => 'success',
                    'decoded_text' => $decoded_text
                ]);
            }
            return view('qr.decode', compact('decoded_text'));
        }

        if ($request->has('format') && $request->input('format') === 'json') {
            return response()->json([
                'status' => 'error',
                'message' => 'No QR code found'
            ]);
        }

        return view('qr.decode', ['error' => 'No QR code found']);
    }
    public function welcome()
    {
        return view('qr.welcome');
    }
}