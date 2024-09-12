<?php
session_start();
require '../db_connection.php';
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}
$donorId = $_SESSION['donorId'];
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/hospital.css">
    <link rel="icon" href="../../Images/logo.png" type="image/png">
    <title>MedAlert</title>
</head>

<body>
    <header class="navbar-container_long">
        <div class="logo">
            <img src="../../Images/logo.png" alt="MedAlert Logo" class="logo-img">
            <span class="logo-name">MedAlert - Your Healthcare Partner</span>
        </div>
        <div class="activeNav mt-3">
            <div id="nav-toggle" class="nav-toggle_long">☰</div>
            <div class="nav-links nav-links_long">
                <a href="setSession.php?section=profile" class="nav-button"> Profile </a>
                <a href="QR.php" class="nav-button"> Attendance Scanner</a>
                <a href="setSession.php?section=DonationHistory" class="nav-button"> Donation History</a>
                <a href="../logout.php" class="nav-button"> Logout</a>
            </div>
        </div>
    </header>

    <div id="ScanQr" class="section active">
        <div id="camera-container">
            <video id="scanner" autoplay></video>
        </div>
        <form id="qrForm" action="QrScan.php" method="POST" style="display: none;">
            <input type="hidden" id="qrDataInput" name="qrData" value="">
        </form>
    </div>


    <footer>
        <p>&copy; 2024 MedAlert. All Rights Reserved.</p>
    </footer>
</body>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="../../Script/script.js"></script>
<script src="https://unpkg.com/jsqr/dist/jsQR.js"></script>
<script>
    const video = document.getElementById('scanner');
    const resultElement = document.getElementById('result');
    const canvas = document.createElement('canvas');
    const canvasContext = canvas.getContext('2d', {
        willReadFrequently: true
    });
    let timeoutId = null;

    function startVideo() {
        navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: "user"
                }
            })
            .then(stream => {
                video.srcObject = stream;
                video.setAttribute("playsinline", true);
                video.play();
                requestAnimationFrame(scanQRCode);

                timeoutId = setTimeout(() => {
                    if (confirm("Invalid QR code. Please try again.")) {
                        location.reload();
                    }
                }, 60000);
            })
            .catch(err => {
                console.error("Error accessing camera: ", err);
            });
    }

    function scanQRCode() {
        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvasContext.drawImage(video, 0, 0, canvas.width, canvas.height);
            const imageData = canvasContext.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, canvas.width, canvas.height, {
                inversionAttempts: "dontInvert",
            });

            if (code) {
                const qrData = code.data;
                video.srcObject.getTracks().forEach(track => track.stop());

                clearTimeout(timeoutId);

                if (confirm(`Do you want to mark your attendance?`)) {
                    document.getElementById('qrDataInput').value = qrData;
                    document.getElementById('qrForm').submit();
                }
            } else {
                requestAnimationFrame(scanQRCode); // Continue scanning
            }
        } else {
            requestAnimationFrame(scanQRCode);
        }
    }

    window.onload = startVideo;
</script>

</html>