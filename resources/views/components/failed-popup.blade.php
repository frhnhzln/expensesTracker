<!-- resources/views/components/failed-popup.blade.php -->
<style>
    #failOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(255, 0, 0, 0.1);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(4px);
    }

    .failPopup {
        background: white;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        animation: shake 0.5s ease;
        transform-style: preserve-3d;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .failPopup i {
        font-size: 60px;
        color: #dc3545;
        animation: pulse 1.5s infinite;
    }

    .failPopup p {
        font-size: 18px;
        color: #dc3545;
        margin-top: 10px;
    }

    @keyframes shake {
        0% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        50% { transform: translateX(10px); }
        75% { transform: translateX(-10px); }
        100% { transform: translateX(0); }
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.2); }
    }
</style>

<div id="failOverlay">
    <div class="failPopup">
        <i class="bi bi-x-circle-fill"></i>
        <p>Face not recognized!</p>
    </div>
</div>
