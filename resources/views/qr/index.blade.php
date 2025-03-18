<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Master</title>
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
            <h1 class="text-center mb-4" style="color: #fff;">QR Code Master</h1>
            <div class="card-body">
                <h2 class="text-center mb-3 animate__animated animate__bounceIn">Generate QR Code</h2>
                <form action="{{ route('qr.generate') }}" method="POST" class="mb-4">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" name="text" class="form-control" placeholder="Enter text or URL" required>
                        <button type="submit" class="btn btn-primary">Generate</button>
                    </div>
                </form>
                <h2 class="text-center mb-3 animate__animated animate__bounceIn" style="animation-delay: 0.2s;">Decode QR Code</h2>
                <form action="{{ route('qr.decode') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                        <button type="submit" class="btn btn-success">Decode</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.js"></script>
</body>
</html>