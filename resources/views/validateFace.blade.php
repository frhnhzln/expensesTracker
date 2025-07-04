<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Face Login</title>

    <!-- Face API -->
    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom Styles -->
    @include('components.face-login-style')
</head>
<body>

    <h2>👁 Face Login</h2>

    <div id="video-wrapper">
        <video id="video" autoplay muted></video>
    </div>

    <div id="status" class="status-text scanning">🔍 Scanning for face...</div>

    @include('components.success-popup')
    @include('components.failed-popup')

    <script>
        let modelsLoaded = false;
        let scanning = true;
        let noFaceCounter = 0;

        async function startCamera() {
            document.getElementById('status').innerText = '🔄 Loading models...';

            // Load better model for low-light conditions
            await faceapi.nets.ssdMobilenetv1.loadFromUri('/models');
            await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
            await faceapi.nets.faceRecognitionNet.loadFromUri('/models');
            modelsLoaded = true;

            const video = document.getElementById('video');
            const stream = await navigator.mediaDevices.getUserMedia({ video: {} });
            video.srcObject = stream;

            document.getElementById('status').innerText = '🔍 Scanning for face...';

            video.addEventListener('playing', () => {
                autoScanFace();
            });
        }

        async function autoScanFace() {
            const video = document.getElementById('video');

            async function scan() {
                if (!scanning) return;

                const detection = await faceapi
                    .detectSingleFace(video) // No options needed for ssdMobilenetv1
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                if (detection) {
                    scanning = false;
                    noFaceCounter = 0;

                    document.getElementById('status').innerText = '✅ Face detected. Validating...';

                    const face_id = JSON.stringify(Array.from(detection.descriptor));

                    fetch('/validateFace', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ face_id })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('status').innerText = '🎉 Login successful!';
                            document.getElementById('status').classList.remove('scanning');
                            document.getElementById('status').classList.add('success');
                            document.getElementById('successOverlay').style.display = 'flex';
                            setTimeout(() => {
                                window.location.href = '/dashboard';
                            }, 1000);
                        } else {
                            document.getElementById('status').innerText = '❌ Face not recognized';
                            document.getElementById('status').classList.remove('scanning');
                            document.getElementById('status').classList.add('failed');
                            document.getElementById('failOverlay').style.display = 'flex';

                            setTimeout(() => {
                                document.getElementById('failOverlay').style.display = 'none';
                                document.getElementById('status').className = 'status-text scanning';
                                document.getElementById('status').innerText = '🔍 Scanning for face...';
                                scanning = true;
                                scan();
                            }, 1500);
                        }
                    })
                    .catch(() => {
                        console.error("Server error.");
                        scanning = true;
                        setTimeout(scan, 1500);
                    });

                } else {
                    noFaceCounter++;

                    // Show low-light warning after 5 failed attempts
                    if (noFaceCounter > 5) {
                        document.getElementById('status').innerText = '⚠️ Low visibility. Please move to better lighting.';
                    } else {
                        document.getElementById('status').innerText = '🔍 Scanning for face...';
                    }

                    setTimeout(scan, 1000); // retry
                }
            }

            scan(); // Start scan loop
        }

        window.onload = startCamera;
    </script>
</body>
</html>
