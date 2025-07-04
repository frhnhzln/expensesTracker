<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>

    <!-- ✅ Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ✅ Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- ✅ jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- ✅ CSRF Token for AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- ✅ Face-scan id -->
    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <h2 class="mb-4 text-center">Register</h2>
            <div id="alert-placeholder"></div>

            <form id="registerForm" class="card card-body shadow">
                @csrf

                <div class="mb-3">
                    <!-- <label class="form-label">Face ID Scan</label> -->
                    <video id="video" width="320" height="240" autoplay muted class="rounded border"></video>
                    <!-- <button type="button" class="btn btn-outline-primary mt-2" onclick="captureFace()">Scan Face</button> -->
                    <input type="hidden" name="face_id" id="face_id_input">
                </div>

                <div class="mb-3">
                    <button type="button" class="btn btn-outline-primary mt-2" onclick="captureFace()">Scan Face</button>
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">CAPTCHA</label>
                    <div class="input-group">
                        <span class="input-group-text p-0 bg-white border rounded">
                            <img src="{{ url('/captcha') }}?t={{ time() }}" id="captchaImage" alt="CAPTCHA" style="height: 40px; width: 100px;">
                        </span>
                        <button class="btn btn-primary" type="button" onclick="refreshCaptcha()" title="Reload CAPTCHA">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                    <input type="text" name="captcha" class="form-control mt-2" placeholder="Enter the text above" required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="/" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-primary">Register</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    let modelsLoaded = false;

    async function startCamera() {
        try {
            const loadingMsg = document.createElement('p');
            loadingMsg.id = 'loadingMsg';
            loadingMsg.innerText = 'Loading face models...';
            document.getElementById('video').insertAdjacentElement('beforebegin', loadingMsg);

            // ✅ Load only required models
            await faceapi.nets.tinyFaceDetector.loadFromUri('/models');
            await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
            await faceapi.nets.faceRecognitionNet.loadFromUri('/models');

            modelsLoaded = true;
            loadingMsg.remove();

            // ✅ Start video stream
            const video = document.getElementById('video');
            const stream = await navigator.mediaDevices.getUserMedia({ video: {} });
            video.srcObject = stream;

        } catch (err) {
            alert('Failed to load face models or start camera.');
            console.error(err);
        }
    }

    async function captureFace() {
        if (!modelsLoaded) {
            alert("Face models are still loading...");
            return;
        }

        const video = document.getElementById('video');

        // ✅ Use explicit detector options
        const options = new faceapi.TinyFaceDetectorOptions({
            inputSize: 224, // smaller = faster
            scoreThreshold: 0.5
        });

        const detection = await faceapi
            .detectSingleFace(video, options)
            .withFaceLandmarks()
            .withFaceDescriptor();

        if (detection) {
            const encoding = JSON.stringify(Array.from(detection.descriptor));
            document.getElementById('face_id_input').value = encoding;
            alert("Face captured successfully.");
        } else {
            alert("No face detected. Please try again.");
        }
    }

    function refreshCaptcha() {
        document.getElementById('captchaImage').src = '/captcha?t=' + Date.now();
    }

    // ✅ CSRF setup for AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // ✅ Form submit
    $('#registerForm').on('submit', function(e) {
        e.preventDefault();

        if (!$('#face_id_input').val()) {
            alert("Please scan your face before registering.");
            return;
        }

        let formData = $(this).serialize();

        $.post('/register-process', formData)
            .done(response => {
                $('#alert-placeholder').html(`
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>${response.message}</strong><br>
                        <img src="${response.qr}" class="mt-2 border rounded shadow-sm" style="width:200px;">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `);

                setTimeout(() => {
                    window.location.href = "/";
                }, 1000);
            })
            .fail(xhr => {
                let html = `<div class="alert alert-danger alert-dismissible fade show" role="alert"><ul>`;
                if (xhr.responseJSON?.errors) {
                    $.each(xhr.responseJSON.errors, function(key, val) {
                        html += `<li>${val}</li>`;
                    });
                } else {
                    html += `<li>Unknown error occurred.</li>`;
                }
                html += `</ul><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`;
                $('#alert-placeholder').html(html);
            });
    });
</script>

<!-- ✅ Load face-api last -->
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
    window.addEventListener('load', () => {
        startCamera();
    });
</script>
</body>
</html>
