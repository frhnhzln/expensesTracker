<style>
    body {
        background: linear-gradient(145deg, #f0f2f5, #ffffff);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
        font-family: 'Segoe UI', sans-serif;
    }

    h2 {
        margin-bottom: 20px;
        color: #2c3e50;
    }

    #video-wrapper {
        position: relative;
        width: 340px;
        height: 260px;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .scan-ring {
        position: absolute;
        border: 3px solid rgba(0, 150, 255, 0.7);
        width: 100px;
        height: 100px;
        border-radius: 50%;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
        50% { transform: translate(-50%, -50%) scale(1.1); opacity: 0.6; }
        100% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
    }

    .status-text {
        margin-top: 15px;
        font-size: 1.2rem;
        color: #555;
    }

    .status-text.scanning {
        color: #0d6efd;
    }

    .status-text.success {
        color: #28a745;
    }

    .status-text.failed {
        color: #dc3545;
    }
</style>