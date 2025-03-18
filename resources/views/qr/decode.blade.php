<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Decoded</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <style>
        body {
            background: linear-gradient(135deg, #DD4124, #E15D44);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card animate__animated animate__fadeIn" style="max-width: 600px; margin: 0 auto; padding: 20px; background: rgba(255, 255, 255, 0.1); border-radius: 15px; backdrop-filter: blur(10px);">
            @if (isset($decoded_text))
                <h2 class="text-center mb-4">QR Code Decoded</h2>
                <div class="card-body text-center animate__animated animate__slideInUp" style="animation-delay: 0.3s;">
                    <p class="display-6">Decoded Text: <span class="text-primary">{{ $decoded_text }}</span></p>
                    <p><a href="{{ route('qr.index') }}" class="btn btn-secondary mt-4">Back</a></p>
                </div>
            @else
                <h2 class="text-center mb-4">Error</h2>
                <div class="card-body text-center animate__animated animate__shakeX" style="animation-delay: 0.3s;">
                    <p class="text-danger">{{ $error ?? 'Unknown error' }}</p>
                    <p><a href="{{ route('qr.index') }}" class="btn btn-secondary mt-4">Back</a></p>
                </div>
            @endif
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.js"></script>
</body>
</html>