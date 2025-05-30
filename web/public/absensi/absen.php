<?php
session_start();
require_once "../auth_check.php";

require_role("dosen");
?>

<?php include "../../components/header.php"; ?>

<div class="d-flex">
    <?php include "../../components/sidebar.php"; ?>

    <div class="content flex-grow-1 p-4 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="container text-center mt-5">
            <h2 class="mb-4">📷 Realtime Face Recognition</h2>

            <video id="video" autoplay muted></video>
            <canvas id="canvas" width="480" height="360" style="display: none;"></canvas>

            <div id="status" class="alert mt-3 d-none" role="alert"></div>
        </div>
    </div>
</div>

<script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');
    const statusBox = document.getElementById('status');

    const urlParams = new URLSearchParams(window.location.search);
    const idSession = urlParams.get('id_session');

    let isProcessing = false;
    let lastDetected = null;
    let retryCount = 0;

    navigator.mediaDevices.getUserMedia({
            video: true
        })
        .then(stream => {
            video.srcObject = stream;
        })
        .catch(err => {
            setStatus("❌ Gagal akses kamera: " + err, "danger");
        });

    function setStatus(message, type) {
        statusBox.className = `alert alert-${type} mt-3`;
        statusBox.innerText = message;
        statusBox.classList.remove('d-none');
    }

    async function sendFrame() {
        if (isProcessing) return;
        isProcessing = true;

        try {
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg'));

            const formData = new FormData();
            formData.append('id_session', idSession);
            formData.append('image', blob, 'frame.jpg');

            const response = await fetch('http://localhost:8000/absen-session/face-recognition', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                if (lastDetected !== data.nrp) {
                    setStatus("✅ " + data.message, "success");
                    lastDetected = data.nrp;
                    retryCount = 0;
                }
            } else {
                retryCount++;
                if (retryCount >= 3) {
                    setStatus("⚠️ " + data.message, "warning");
                    retryCount = 0;
                }
            }
        } catch (err) {
            setStatus("❌ Error: " + err.message, "danger");
        } finally {
            isProcessing = false;
        }
    }

    setInterval(sendFrame, 2000);
</script>

<?php include "../../components/footer.php"; ?>