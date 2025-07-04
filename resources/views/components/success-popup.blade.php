<style>
    #successOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 255, 100, 0.1);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(5px);
    }

    .successCheck {
        background: white;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        animation: popIn 0.6s ease forwards;
        transform-style: preserve-3d;
    }

    .successCheck i {
        font-size: 60px;
        color: #28a745;
        animation: pulse 1.5s infinite;
    }

    @keyframes popIn {
        0% {
            transform: scale(0.3) rotateX(-90deg);
            opacity: 0;
        }
        100% {
            transform: scale(1) rotateX(0deg);
            opacity: 1;
        }
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.2);
        }
    }
</style>

<div id="successOverlay">
    <div class="successCheck">
        <i class="bi bi-check-circle-fill"></i>
    </div>
</div>
