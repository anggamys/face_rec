<?php
session_start();
require_once "../auth_check.php";
include "../../components/header.php";
?>

<div class="d-flex">
    <?php include "../../components/sidebar.php"; ?>

    <div class="content flex-grow-1 p-4 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="text-center">
            <h2 class="mb-4">📷 Ambil Foto Kamera</h2>

            <div style="max-width: 100%; width: 480px;">
                <video id="video" autoplay class="rounded w-100"></video>
            </div>

            <div class="mt-3">
                <button id="captureBtn" class="btn btn-primary">
                    <i class="bi bi-camera"></i> Ambil Foto
                </button>
            </div>

            <div id="status" class="alert mt-3 d-none" role="alert"></div>

            <canvas id="canvas" width="480" height="360" style="display:none;"></canvas>
        </div>
    </div>
</div>

<script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');
    const captureBtn = document.getElementById('captureBtn');
    const statusBox = document.getElementById('status');

    // Ambil ID sesi dari URL
    const urlParams = new URLSearchParams(window.location.search);
    const idSession = urlParams.get('id_session');

    // Akses kamera
    navigator.mediaDevices.getUserMedia({
            video: true
        })
        .then(stream => {
            video.srcObject = stream;
        })
        .catch(err => {
            setStatus("❌ Gagal akses kamera: " + err, "danger");
        });

    captureBtn.addEventListener('click', () => {
        setStatus("⏳ Mengunggah foto...", "info");

        // Ambil gambar dari video
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Ubah ke blob
        canvas.toBlob(blob => {
            const formData = new FormData();
            formData.append('id_session', idSession);
            formData.append('image', blob, 'frame.jpg');

            fetch('http://localhost:8000/absen-session/face-recognition', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        setStatus("✅ " + data.message, "success");
                    } else {
                        setStatus("❌ " + data.message, "danger");
                    }
                })
                .catch(err => {
                    setStatus("❌ Gagal upload: " + err, "danger");
                });
        }, 'image/jpeg');
    });

    function setStatus(message, type) {
        statusBox.className = `alert alert-${type} mt-3`;
        statusBox.innerText = message;
        statusBox.classList.remove('d-none');
    }
</script>

<?php include "../../components/footer.php"; ?>