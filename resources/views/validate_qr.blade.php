<!DOCTYPE html>
<html>
<head>
    <title>QR Code Validation</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4 text-center">Scan Your QR Code</h2>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div id="qr-reader" style="width: 100%;"></div>
            <div class="text-center mt-3">
        <a href="/" class="btn btn-secondary">
            ← Back
        </a>
    </div>
            <div id="qr-result" class="mt-4 alert d-none"></div>  
        </div>
    </div> 
</div>

<script>
    let html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader", 
        { fps: 10, qrbox: 250 }, 
        false
    );

    function onScanSuccess(decodedText, decodedResult) {
        // Stop scanning immediately
        html5QrcodeScanner.clear().then(() => {
            console.log("Scanner stopped after scan.");
        }).catch(err => {
            console.error("Failed to stop scanner", err);
        });

        const resultDiv = document.getElementById('qr-result');
        resultDiv.classList.remove('d-none');
        resultDiv.innerHTML = `<div class="text-info">🔍 Verifying QR code...</div>`;

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/qr-login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ qr_content: decodedText })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                resultDiv.classList.remove('alert-danger');
                resultDiv.classList.add('alert-success');
                resultDiv.innerHTML = `<strong>✅ Login Success:</strong><br>Welcome <b>${data.username}</b>`;

                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 10);
            } else {
                resultDiv.classList.remove('alert-success');
                resultDiv.classList.add('alert-danger');
                resultDiv.innerHTML = `
                    <strong>❌ ${data.message}</strong><br>
                    <button class="btn btn-sm btn-warning mt-2" onclick="restartScanner()">🔄 Re-scan</button>
                `;
            }
        })
        .catch(err => {
            resultDiv.classList.add('alert-danger');
            resultDiv.innerHTML = `
                <strong>⚠️ Server Error:</strong> ${err}<br>
                <button class="btn btn-sm btn-warning mt-2" onclick="restartScanner()">🔄 Re-scan</button>
            `;
        });
    }

    function restartScanner() {
        const resultDiv = document.getElementById('qr-result');
        resultDiv.classList.add('d-none');
        resultDiv.innerHTML = ''; // clear old message

        html5QrcodeScanner.clear().then(() => {
            html5QrcodeScanner.render(onScanSuccess); // re-initialize scanner
        }).catch(err => {
            console.error("Error restarting scanner:", err);
        });
    }

    html5QrcodeScanner.render(onScanSuccess);
</script>
</body>
</html>
