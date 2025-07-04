<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap icon -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- ✅ Face-scan id -->
    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
</head>
<body class="bg-light">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        ✅ {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Login</h4>

                        <form action="/login" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">

                            <div class="d-flex flex-column align-items-start">

                                <!-- Login + QR Scan -->
                                <div class="btn-group mb-2">
                                    <button type="submit" class="btn btn-primary">Login</button>
                                    <button type="button" class="btn btn-outline-secondary" title="Scan QR to login" onclick="qr_scan()">
                                        <i class="bi bi-qr-code-scan"></i>
                                    </button>
                                </div>

                                <!-- Face ID-->
                                <div class="btn-group mb-2">
                                    <a href="/validateFace" class="btn btn-outline-secondary" title="Scan Face to login">
                                        <i class="bi bi-person-bounding-box"></i>
                                    </a>
                                </div>
                            </div>

                                <div>
                                    <a href="#" onclick="register_js()">Register</a> |
                                    <a href="#" onclick="forgot_js()">Forgot Password?</a>
                                </div>
                        </form>
                        @if ($errors->has('login'))
                            <div class="alert alert-danger mt-3">
                                {{ $errors->first('login') }}
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>
<input type="hidden" name="face_id" id="face_id_input">
<video id="video" width="320" height="240" autoplay muted style="display: none;" class="rounded border mt-3"></video>

    <script>
        function register_js() {
            window.location.href = '/register';
        }

        function forgot_js() {
            window.location.href = '/forgot_pwd';
        }

        function qr_scan()  {
            window.location.href = '/validate_qr';
        }

        let modelsLoaded = false;

        // ✅ Load face-api models
        async function loadFaceModels() {
            await faceapi.nets.tinyFaceDetector.loadFromUri('/models');
            await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
            await faceapi.nets.faceRecognitionNet.loadFromUri('/models');
            modelsLoaded = true;
            console.log("Face-api models loaded");
        }

        window.addEventListener('DOMContentLoaded', loadFaceModels);

        async function face_scan() {
            if (!modelsLoaded) {
                alert("Face models are still loading...");
                return;
            }

            const video = document.getElementById('video');
            video.style.display = 'block';

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: {} });
                video.srcObject = stream;
            } catch (err) {
                alert("Camera access denied.");
                console.error(err);
                return;
            }

            setTimeout(async () => {
                const detection = await faceapi
                    .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 224 }))
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                if (detection) {
                    const descriptor = Array.from(detection.descriptor);
                    document.getElementById('face_id_input').value = JSON.stringify(descriptor);
                    
                    // stop camera
                    const tracks = video.srcObject.getTracks();
                    tracks.forEach(track => track.stop());
                    video.style.display = 'none';

                    // auto-submit form
                    document.querySelector('form').submit();
                } else {
                    alert("No face detected. Please try again.");
                }
            }, 1500); // give camera time to stabilize
        }
    </script>
</body>
</html>